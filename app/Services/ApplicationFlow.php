<?php

namespace App\Services;

use App\Enums\ApplicationStatus as S;
use App\Enums\Role;
use App\Models\Appraisal;
use App\Models\Application;
use App\Models\Approval;
use App\Models\Collateral;
use App\Models\Member;
use App\Models\Product;
use App\Models\User;
use App\Support\Pii;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Satu-satunya jalan mengubah status pengajuan. Setiap langkah: kunci baris, cek status asal,
 * cek aturan bisnis, simpan, lalu audit otomatis (model Auditable). Pelanggaran -> DomainException (pesan Indonesia).
 * Otorisasi peran/cabang dilakukan di lapisan atas (Gate + global scope); service ini menegakkan ATURAN ALUR.
 */
class ApplicationFlow
{
    /** Item daftar periksa kelayakan; tiap item bernilai 20 poin (skor 0-100). */
    public const DAFTAR_PERIKSA = [
        'identitas_cocok' => 'Identitas penggadai cocok dengan KTP',
        'barang_menyala' => 'Barang menyala dan berfungsi normal',
        'bebas_kunci' => 'Bebas kunci akun / kunci layar',
        'kelengkapan_sesuai' => 'Kelengkapan sesuai yang dilaporkan',
        'riwayat_baik' => 'Riwayat anggota baik (tanpa tunggakan)',
    ];

    public function ajukan(Member $member, Product $produk, int $pinjaman, ?string $catatan, User $oleh): Application
    {
        if ($member->status !== 'terverifikasi') {
            throw new DomainException('Keanggotaan belum terverifikasi oleh admin cabang.');
        }
        if (! $produk->is_active) {
            throw new DomainException('Produk ini sedang tidak tersedia.');
        }
        if ($pinjaman < $produk->pinjaman_min || $pinjaman > $produk->pinjaman_max) {
            throw new DomainException('Jumlah pinjaman harus antara '.\App\Support\Rupiah::format($produk->pinjaman_min).' dan '.\App\Support\Rupiah::format($produk->pinjaman_max).'.');
        }

        $kebijakan = Situs::kebijakan();
        $maks = (int) ($kebijakan['maks_pengajuan_terbuka'] ?? 3);
        $terbuka = Application::withoutGlobalScopes()->where('member_id', $member->id)
            ->whereIn('status', array_map(fn ($s) => $s->value, S::terbuka()))->count();
        if ($terbuka >= $maks) {
            throw new DomainException("Anda sudah memiliki $maks pengajuan yang masih berjalan. Selesaikan atau batalkan salah satunya.");
        }

        return DB::transaction(function () use ($member, $produk, $pinjaman, $catatan, $oleh, $kebijakan) {
            $a = new Application([
                'kode' => $this->kodeBaru(),
                'member_id' => $member->id,
                'branch_id' => $member->branch_id,
                'product_id' => $produk->id,
                'pinjaman_diminta' => $pinjaman,
                'tenor_hari' => $produk->tenor_hari,
                'catatan_member' => $catatan,
                'created_by' => $oleh->id,
            ]);
            $a->forceFill([
                'status' => S::MenungguBarang,
                'diajukan_at' => now(),
                'batas_bawa_barang' => today()->addDays((int) ($kebijakan['batas_bawa_barang_hari'] ?? 7)),
            ])->save();

            return $a;
        });
    }

    /** @param  array{identitas_dicocokkan:bool, merek:string, model:string, imei_serial?:?string, kelengkapan?:?string, kondisi?:?string, no_segel:string, lokasi_simpan?:?string}  $d
     *  @param  UploadedFile[]  $foto  kunci = jenis foto (depan|belakang|serial|segel) */
    public function terimaBarang(Application $a, User $teller, array $d, array $foto = []): Collateral
    {
        return DB::transaction(function () use ($a, $teller, $d, $foto) {
            $a = $this->kunci($a, S::MenungguBarang);

            if (empty($d['identitas_dicocokkan'])) {
                throw new DomainException('Identitas penggadai wajib dicocokkan dengan KTP asli sebelum barang diterima.');
            }

            $hash = ! empty($d['imei_serial']) ? Pii::hash($d['imei_serial']) : null;
            if ($hash && Collateral::withoutGlobalScopes()->where('imei_serial_hash', $hash)->whereIn('status', ['diterima', 'tersimpan'])->exists()) {
                throw new DomainException('Nomor seri/IMEI ini tercatat pada barang lain yang masih dititipkan. Periksa kembali barangnya.');
            }

            $c = new Collateral([
                'application_id' => $a->id,
                'branch_id' => $a->branch_id,
                'jenis' => $a->product->kategori_barang,
                'merek' => $d['merek'],
                'model' => $d['model'],
                'imei_serial_enc' => $d['imei_serial'] ?? null,
                'imei_serial_hash' => $hash,
                'kelengkapan' => $d['kelengkapan'] ?? null,
                'kondisi' => $d['kondisi'] ?? null,
                'no_segel' => $d['no_segel'],
                'lokasi_simpan' => $d['lokasi_simpan'] ?? null,
                'diterima_oleh' => $teller->id,
                'diterima_at' => now(),
            ]);
            $c->forceFill(['status' => 'diterima'])->save();

            foreach ($foto as $jenis => $berkas) {
                $c->photos()->create(['path' => $berkas->store("jaminan/{$c->id}"), 'jenis' => $jenis]);
            }

            $this->ubahStatus($a, S::BarangDiterima);

            return $c;
        });
    }

    /** @param  array{nilai_pasar:int, nilai_taksiran:int, periksa?:array, rekomendasi:string, catatan?:?string}  $d */
    public function taksir(Application $a, User $analis, array $d): Appraisal
    {
        return DB::transaction(function () use ($a, $analis, $d) {
            $a = $this->kunci($a, S::BarangDiterima);
            $produk = $a->product;

            if (! in_array($d['rekomendasi'], ['setuju', 'tolak'], true)) {
                throw new DomainException('Rekomendasi harus setuju atau tolak.');
            }
            if ($d['nilai_taksiran'] < 1) {
                throw new DomainException('Nilai taksiran harus lebih dari nol.');
            }
            if ($d['rekomendasi'] === 'tolak' && blank($d['catatan'] ?? null)) {
                throw new DomainException('Isi alasan penolakan pada catatan.');
            }

            $ltv = $produk->ltv_max_bp;
            $plafon = min(LoanCalculator::pokok($d['nilai_taksiran'], $ltv), $produk->pinjaman_max);
            $disetujui = min($a->pinjaman_diminta, $plafon);

            if ($d['rekomendasi'] === 'setuju' && $disetujui < $produk->pinjaman_min) {
                throw new DomainException('Plafon hasil taksiran di bawah pinjaman minimum produk. Pilih tolak, atau periksa kembali nilai taksiran.');
            }

            $skor = count(array_intersect(array_keys(self::DAFTAR_PERIKSA), array_keys(array_filter($d['periksa'] ?? [])))) * 20;

            $a->appraisals()->update(['is_current' => false]);
            $t = new Appraisal([
                'application_id' => $a->id,
                'analis_id' => $analis->id,
                'tipe' => 'awal',
                'nilai_pasar' => $d['nilai_pasar'],
                'nilai_taksiran' => $d['nilai_taksiran'],
                'ltv_diterapkan_bp' => $ltv,
                'plafon_maks' => $plafon,
                'skor_kelayakan' => $skor,
                'rekomendasi' => $d['rekomendasi'],
                'catatan' => $d['catatan'] ?? null,
                'is_current' => true,
            ]);
            $t->save();

            Approval::create([
                'application_id' => $a->id, 'level' => 'analis', 'keputusan' => $d['rekomendasi'],
                'oleh' => $analis->id, 'catatan' => $d['catatan'] ?? null, 'decided_at' => now(),
            ]);

            if ($d['rekomendasi'] === 'tolak') {
                $this->ubahStatus($a, S::Ditolak);

                return $t;
            }

            $a->forceFill(['pinjaman_disetujui' => $disetujui])->save();
            $limit = (int) (Situs::kebijakan()['limit_persetujuan_manager'] ?? 5_000_000);
            $this->ubahStatus($a, $disetujui > $limit ? S::MenungguManager : S::Disetujui);

            return $t;
        });
    }

    public function putuskan(Application $a, User $u, string $keputusan, ?string $catatan): void
    {
        DB::transaction(function () use ($a, $u, $keputusan, $catatan) {
            $a = $this->kunci($a, S::MenungguManager);

            if (! in_array($keputusan, ['setuju', 'tolak'], true)) {
                throw new DomainException('Keputusan harus setuju atau tolak.');
            }
            if ($a->created_by === $u->id) {
                throw new DomainException('Anda tidak dapat memutuskan pengajuan yang Anda buat sendiri.');
            }
            if ($keputusan === 'tolak' && blank($catatan)) {
                throw new DomainException('Isi alasan penolakan pada catatan.');
            }

            Approval::create([
                'application_id' => $a->id, 'level' => $u->role === Role::Owner ? 'owner' : 'manager', 'keputusan' => $keputusan,
                'oleh' => $u->id, 'catatan' => $catatan, 'decided_at' => now(),
            ]);
            $this->ubahStatus($a, $keputusan === 'setuju' ? S::Disetujui : S::Ditolak);
        });
    }

    public function batalkan(Application $a, User $u): void
    {
        DB::transaction(function () use ($a) {
            $a = $this->kunci($a, S::MenungguBarang);
            $this->ubahStatus($a, S::Dibatalkan);
        });
    }

    /** Barang pengajuan yang ditolak dikembalikan ke penggadai. */
    public function kembalikanBarang(Application $a, User $teller): int
    {
        return DB::transaction(function () use ($a, $teller) {
            $a = $this->kunci($a, S::Ditolak);
            $n = 0;
            foreach ($a->collaterals()->where('status', 'diterima')->get() as $c) {
                $c->forceFill(['status' => 'dikeluarkan'])->save();
                $n++;
            }
            if ($n === 0) {
                throw new DomainException('Tidak ada barang yang perlu dikembalikan.');
            }

            return $n;
        });
    }

    /** Dipanggil scheduler: pengajuan yang barangnya tak kunjung dibawa melewati batas. */
    public function kedaluwarsakan(): int
    {
        $n = 0;
        Application::withoutGlobalScopes()->where('status', S::MenungguBarang->value)
            ->where('batas_bawa_barang', '<', today())->get()
            ->each(function (Application $a) use (&$n) {
                $this->ubahStatus($a, S::Kedaluwarsa);
                $n++;
            });

        return $n;
    }

    private function kunci(Application $a, S $harus): Application
    {
        $baru = Application::withoutGlobalScopes()->whereKey($a->id)->lockForUpdate()->firstOrFail();
        if ($baru->status !== $harus) {
            throw new DomainException('Status pengajuan sudah berubah ('.$baru->status->label().'). Muat ulang halaman.');
        }

        return $baru;
    }

    private function ubahStatus(Application $a, S $ke): void
    {
        $a->forceFill(['status' => $ke])->save();
    }

    private function kodeBaru(): string
    {
        do {
            $kode = 'PG-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
        } while (Application::withoutGlobalScopes()->where('kode', $kode)->exists());

        return $kode;
    }
}
