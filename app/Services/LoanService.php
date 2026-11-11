<?php

namespace App\Services;

use App\Enums\ApplicationStatus as S;
use App\Enums\LoanStatus as L;
use App\Models\Appraisal;
use App\Models\Application;
use App\Models\Auction;
use App\Models\Collateral;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanExtension;
use App\Models\LoanStatusHistory;
use App\Models\Payment;
use App\Models\Redemption;
use App\Models\User;
use App\Support\Rupiah;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Siklus hidup gadai: pencairan, tagihan, pembayaran (perpanjang/cicil/tebus), verifikasi transfer, pembalikan,
 * pengeluaran barang, status harian, dan lelang. Semua uang bilangan bulat rupiah; semua rumus lewat LoanCalculator.
 *
 * ATURAN (ASUMSI, lihat docs/04): sewa modal dan administrasi dibayar di akhir periode (saat perpanjang/cicil/tebus);
 * perpanjang memajukan jatuh tempo dari jatuh tempo lama (bukan tanggal bayar); cicil = perpanjang + setor pokok dan
 * menyetel ulang hitungan perpanjangan; tebus lebih awal dikenai sewa per blok hari; keterlambatan dikenai sewa harian
 * (+ denda uang bila diaktifkan). Kekurangan hasil lelang TIDAK ditagihkan ke anggota; kelebihan menjadi hak anggota.
 */
class LoanService
{
    public function cairkan(Application $a, User $teller, string $metode, ?string $referensi): Loan
    {
        return DB::transaction(function () use ($a, $teller, $metode, $referensi) {
            $a = Application::withoutGlobalScopes()->whereKey($a->id)->lockForUpdate()->firstOrFail();
            if ($a->status !== S::Disetujui) {
                throw new DomainException('Pengajuan belum disetujui atau sudah dicairkan.');
            }
            if (! in_array($metode, ['tunai', 'transfer'], true)) {
                throw new DomainException('Metode pencairan tidak valid.');
            }
            if ($metode === 'transfer' && blank($referensi)) {
                throw new DomainException('Isi nomor referensi transfer pencairan.');
            }
            $jaminan = Collateral::withoutGlobalScopes()->where('application_id', $a->id)->where('status', 'diterima')->get();
            if ($jaminan->isEmpty()) {
                throw new DomainException('Barang jaminan belum tercatat diterima.');
            }

            $p = $a->product;
            $kb = Situs::kebijakan();
            $syarat = LoanCalculator::dariProduk($p) + [
                'denda_harian' => (int) ($kb['denda_harian'] ?? 0),
                'masa_tenggang_hari' => (int) ($kb['masa_tenggang_hari'] ?? 3),
                'maks_perpanjangan' => (int) ($kb['maks_perpanjangan'] ?? 3),
                'cicilan_min' => (int) ($kb['cicilan_min'] ?? 50_000),
                'hari_pemberitahuan_lelang' => (int) ($kb['hari_pemberitahuan_lelang'] ?? 7),
            ];
            $tgl = today();
            $jt = $tgl->copy()->addDays($syarat['tenor_hari']);

            $loan = new Loan([
                'no_sbg' => $this->nomor('SBG', $a->branch->kode, Loan::class, 'no_sbg'),
                'application_id' => $a->id, 'member_id' => $a->member_id, 'branch_id' => $a->branch_id, 'product_id' => $p->id,
                'tgl_akad' => $tgl, 'snapshot_syarat' => $syarat,
            ]);
            $loan->forceFill([
                'pokok_awal' => $a->pinjaman_disetujui, 'pokok_saldo' => $a->pinjaman_disetujui, 'jatuh_tempo' => $jt,
                'tenggang_sampai' => $jt->copy()->addDays($syarat['masa_tenggang_hari']), 'perpanjangan_ke' => 0, 'status' => L::Aktif,
            ])->save();

            Disbursement::create(['loan_id' => $loan->id, 'branch_id' => $a->branch_id, 'jumlah' => $a->pinjaman_disetujui, 'metode' => $metode,
                'referensi' => $referensi, 'teller_id' => $teller->id, 'dicairkan_at' => now()]);
            foreach ($jaminan as $c) {
                $c->forceFill(['status' => 'tersimpan'])->save();
            }
            $a->forceFill(['status' => S::Dicairkan])->save();
            $this->riwayat($loan, null, L::Aktif, $teller, 'Pencairan');

            return $loan;
        });
    }

    /** Rincian tagihan pada tanggal tertentu (default hari ini). */
    public function tagihan(Loan $l, ?CarbonInterface $tgl = null): array
    {
        $tgl = ($tgl ?? today())->copy()->startOfDay();
        $s = $l->snapshot_syarat;
        $tenor = $s['tenor_hari'];
        $blok = 15;
        $jt = $l->jatuh_tempo->copy()->startOfDay();
        $mulai = $jt->copy()->subDays($tenor);

        $terlambat = max(0, (int) $jt->diffInDays($tgl, false));
        $berjalan = max(1, min($tenor, (int) $mulai->diffInDays($tgl, false)));
        $hariTebus = $terlambat > 0 ? $tenor + $terlambat : min($tenor, (int) (ceil($berjalan / $blok) * $blok));

        $saldo = (int) $l->pokok_saldo;
        $sewaPeriode = LoanCalculator::sewaModal($s, $saldo, $tenor) + ($terlambat > 0 ? LoanCalculator::sewaModal($s, $saldo, $terlambat) : 0);
        $sewaTebus = LoanCalculator::sewaModal($s, $saldo, $hariTebus);
        $admin = LoanCalculator::administrasi($s, $saldo);
        $denda = $terlambat * (int) ($s['denda_harian'] ?? 0);
        $jtBaru = $jt->copy()->addDays($tenor);

        $dapatDibayar = in_array($l->status, L::dapatDibayar(), true);
        $lanjutOk = $dapatDibayar && $jtBaru->gt($tgl);
        $alasanLanjut = ! $dapatDibayar ? 'Gadai ini sudah tidak dapat dibayar.' : (! $lanjutOk ? 'Terlambat terlalu lama untuk diperpanjang atau dicicil; silakan tebus.' : null);
        // Taksir ulang: bila saldo pokok melebihi plafon baru, selisihnya wajib disetor (cicil) sebelum boleh lanjut.
        $selisih = $l->plafon_ulang !== null ? max(0, $saldo - (int) $l->plafon_ulang) : 0;
        $bolehPerpanjang = $lanjutOk && $selisih === 0 && $l->perpanjangan_ke < (int) ($s['maks_perpanjangan'] ?? 3);
        $min = max((int) ($s['cicilan_min'] ?? 50_000), $selisih);
        $alasanPerpanjang = $alasanLanjut ?? ($selisih > 0
            ? 'Taksiran ulang menurun: pinjaman melebihi plafon baru ('.Rupiah::format((int) $l->plafon_ulang).'). Setor pokok minimal '.Rupiah::format($selisih).' lewat cicil, atau tebus.'
            : 'Batas perpanjangan berturut-turut tercapai. Cicil sebagian pokok atau tebus.');

        return [
            'tanggal' => $tgl->toDateString(),
            'terlambat_hari' => $terlambat,
            'saldo' => $saldo,
            'administrasi' => $admin,
            'denda' => $denda,
            'jatuh_tempo_baru' => $jtBaru->toDateString(),
            'plafon_ulang' => $l->plafon_ulang !== null ? (int) $l->plafon_ulang : null,
            'selisih_taksir' => $selisih,
            'perpanjang' => ['boleh' => $bolehPerpanjang, 'alasan' => $bolehPerpanjang ? null : $alasanPerpanjang, 'sewa_modal' => $sewaPeriode, 'total' => $sewaPeriode + $admin + $denda],
            'cicil' => ['boleh' => $lanjutOk && $saldo > $min, 'alasan' => $alasanLanjut, 'sewa_modal' => $sewaPeriode, 'biaya_periode' => $sewaPeriode + $admin + $denda, 'minimum' => $min],
            'tebus' => ['boleh' => $dapatDibayar, 'alasan' => $dapatDibayar ? null : 'Gadai ini sudah tidak dapat ditebus.', 'sewa_modal' => $sewaTebus, 'hari_ditagih' => $hariTebus, 'total' => $saldo + $sewaTebus + $admin + $denda],
        ];
    }

    /**
     * Taksir ulang barang yang sedang digadaikan (Analis). Menetapkan plafon baru; bila saldo pokok melebihi plafon,
     * anggota wajib menyetor selisihnya lewat cicil sebelum memperpanjang. Tebus tidak terpengaruh.
     */
    public function taksirUlang(Loan $l, User $analis, int $nilaiPasar, int $nilaiTaksiran, ?string $catatan = null): Appraisal
    {
        return DB::transaction(function () use ($l, $analis, $nilaiPasar, $nilaiTaksiran, $catatan) {
            $l = $this->kunciLoan($l);
            if (! in_array($l->status, [L::Aktif, L::Tenggang], true)) {
                throw new DomainException('Taksir ulang hanya untuk gadai yang aktif atau dalam masa tenggang.');
            }
            if ($nilaiPasar < 1 || $nilaiTaksiran < 1) {
                throw new DomainException('Nilai pasar dan nilai taksiran harus lebih dari nol.');
            }

            $s = $l->snapshot_syarat;
            $plafon = min(LoanCalculator::pokok($nilaiTaksiran, (int) $s['ltv_max_bp']), (int) $s['pinjaman_max']);
            $sebelumnya = Appraisal::where('application_id', $l->application_id)->where('is_current', true)->first();
            Appraisal::where('application_id', $l->application_id)->update(['is_current' => false]);

            $a = Appraisal::create([
                'application_id' => $l->application_id, 'analis_id' => $analis->id, 'tipe' => 'ulang',
                'nilai_pasar' => $nilaiPasar, 'nilai_taksiran' => $nilaiTaksiran, 'ltv_diterapkan_bp' => (int) $s['ltv_max_bp'],
                'plafon_maks' => $plafon, 'skor_kelayakan' => $sebelumnya?->skor_kelayakan ?? 0, 'rekomendasi' => 'setuju',
                'catatan' => $catatan, 'is_current' => true,
            ]);
            $l->forceFill(['plafon_ulang' => $plafon])->save();

            return $a;
        });
    }

    public function bayar(Loan $l, User $teller, string $tipe, string $metode, ?int $pokok = null, ?UploadedFile $bukti = null): Payment
    {
        return DB::transaction(function () use ($l, $teller, $tipe, $metode, $pokok, $bukti) {
            $l = $this->kunciLoan($l);
            if (! in_array($l->status, L::dapatDibayar(), true)) {
                throw new DomainException('Gadai ini tidak dapat dibayar (status: '.$l->status->label().').');
            }
            if (! in_array($tipe, ['perpanjangan', 'cicil', 'tebus'], true)) {
                throw new DomainException('Jenis pembayaran tidak valid.');
            }
            if (! in_array($metode, ['tunai', 'transfer'], true)) {
                throw new DomainException('Metode pembayaran tidak valid.');
            }
            if ($l->payments()->where('status', 'menunggu_verifikasi')->exists()) {
                throw new DomainException('Masih ada pembayaran transfer yang menunggu verifikasi.');
            }
            if ($metode === 'transfer' && ! $bukti) {
                throw new DomainException('Unggah bukti transfer.');
            }

            $t = $this->tagihan($l);
            $pokokBayar = 0;
            if ($tipe === 'perpanjangan') {
                $this->pastikan($t['perpanjang']);
                [$sewa, $admin, $denda] = [$t['perpanjang']['sewa_modal'], $t['administrasi'], $t['denda']];
            } elseif ($tipe === 'cicil') {
                $this->pastikan($t['cicil']);
                $min = $t['cicil']['minimum'];
                if ($pokok === null || $pokok < $min || $pokok >= $t['saldo']) {
                    throw new DomainException('Setoran pokok minimal '.Rupiah::format($min).' dan harus lebih kecil dari saldo pokok ('.Rupiah::format($t['saldo']).'). Untuk melunasi, pilih tebus.');
                }
                $pokokBayar = $pokok;
                [$sewa, $admin, $denda] = [$t['cicil']['sewa_modal'], $t['administrasi'], $t['denda']];
            } else {
                $this->pastikan($t['tebus']);
                $pokokBayar = $t['saldo'];
                [$sewa, $admin, $denda] = [$t['tebus']['sewa_modal'], $t['administrasi'], $t['denda']];
            }

            $p = new Payment();
            $p->forceFill([
                'no_kwitansi' => $this->nomor('KW', $l->branch->kode, Payment::class, 'no_kwitansi'),
                'loan_id' => $l->id, 'branch_id' => $l->branch_id, 'tipe' => $tipe, 'sewa_modal' => $sewa, 'administrasi' => $admin, 'denda' => $denda,
                'pokok_dibayar' => $pokokBayar, 'total' => $pokokBayar + $sewa + $admin + $denda, 'metode' => $metode,
                'bukti_path' => $bukti?->store("bukti/{$l->id}"), 'status' => 'menunggu_verifikasi', 'teller_id' => $teller->id, 'dibayar_at' => now(),
                'sebelum' => $this->keadaan($l),
            ])->save();

            if ($metode === 'tunai') {
                $this->terapkan($l, $p, $teller);
            }

            return $p;
        });
    }

    /** Verifikasi transfer. Pencatat tidak boleh memverifikasi pembayarannya sendiri. */
    public function verifikasi(Payment $p, User $oleh, bool $setuju, ?string $alasan = null): void
    {
        DB::transaction(function () use ($p, $oleh, $setuju, $alasan) {
            $p = Payment::withoutGlobalScopes()->whereKey($p->id)->lockForUpdate()->firstOrFail();
            if ($p->status !== 'menunggu_verifikasi') {
                throw new DomainException('Pembayaran ini sudah diproses.');
            }
            if ($p->teller_id === $oleh->id) {
                throw new DomainException('Anda tidak dapat memverifikasi pembayaran yang Anda catat sendiri.');
            }
            if (! $setuju) {
                if (blank($alasan)) {
                    throw new DomainException('Isi alasan penolakan.');
                }
                $p->forceFill(['status' => 'batal', 'alasan' => $alasan, 'diverifikasi_oleh' => $oleh->id, 'diverifikasi_at' => now()])->save();

                return;
            }

            $l = $this->kunciLoan($p->loan);
            if ($this->keadaan($l) !== $p->sebelum) {
                throw new DomainException('Keadaan gadai berubah sejak pembayaran dicatat. Tolak pembayaran ini dan catat ulang.');
            }
            $this->terapkan($l, $p, $oleh);
        });
    }

    /** Pembalikan pembayaran sah (Manager). Hanya pembayaran terakhir, dan hanya bila gadai belum berlanjut. */
    public function batalkan(Payment $p, User $manager, string $alasan): void
    {
        DB::transaction(function () use ($p, $manager, $alasan) {
            $p = Payment::withoutGlobalScopes()->whereKey($p->id)->lockForUpdate()->firstOrFail();
            if ($p->status !== 'sah') {
                throw new DomainException('Hanya pembayaran yang sah yang dapat dibalikkan.');
            }
            if (blank($alasan)) {
                throw new DomainException('Isi alasan pembalikan.');
            }
            $l = $this->kunciLoan($p->loan);
            $terakhir = $l->payments()->where('status', 'sah')->max('id');
            if ((int) $terakhir !== (int) $p->id) {
                throw new DomainException('Hanya pembayaran sah yang terakhir yang dapat dibalikkan.');
            }
            if (Redemption::where('loan_id', $l->id)->exists() || in_array($l->status, [L::Dilelang, L::SelesaiLelang], true)) {
                throw new DomainException('Gadai sudah berlanjut (barang keluar atau dilelang); pembayaran tidak dapat dibalikkan.');
            }

            $sb = $p->sebelum;
            $l->forceFill([
                'jatuh_tempo' => $sb['jatuh_tempo'], 'tenggang_sampai' => $sb['tenggang_sampai'], 'pokok_saldo' => $sb['pokok_saldo'], 'perpanjangan_ke' => $sb['perpanjangan_ke'],
            ])->save();
            $this->setStatus($l, L::from($sb['status']), $manager, 'Pembayaran '.$p->no_kwitansi.' dibalikkan');
            LoanExtension::where('payment_id', $p->id)->delete();
            $p->forceFill(['status' => 'batal', 'alasan' => $alasan, 'diverifikasi_oleh' => $manager->id, 'diverifikasi_at' => now()])->save();
        });
    }

    /** @param array{penebus_tipe:string, nama_penebus:string, nik_penebus?:?string, nota_asli_diperiksa:bool} $d */
    public function keluarkanBarang(Loan $l, User $teller, array $d, ?UploadedFile $suratKuasa = null): Redemption
    {
        return DB::transaction(function () use ($l, $teller, $d, $suratKuasa) {
            $l = $this->kunciLoan($l);
            if ($l->status !== L::Ditebus) {
                throw new DomainException('Barang hanya dapat dikeluarkan setelah gadai ditebus.');
            }
            if (Redemption::where('loan_id', $l->id)->exists()) {
                throw new DomainException('Barang sudah dikeluarkan.');
            }
            if (empty($d['nota_asli_diperiksa'])) {
                throw new DomainException('Nota gadai asli dan KTP asli wajib diperiksa sebelum barang dikeluarkan.');
            }
            if ($d['penebus_tipe'] === 'kuasa' && (! $suratKuasa || blank($d['nik_penebus'] ?? null))) {
                throw new DomainException('Penebusan oleh orang lain wajib melampirkan surat kuasa bermaterai dan NIK penebus.');
            }
            $bayar = $l->payments()->where('tipe', 'tebus')->where('status', 'sah')->latest('id')->first();
            if (! $bayar) {
                throw new DomainException('Pembayaran tebus yang sah tidak ditemukan.');
            }

            $r = Redemption::create([
                'loan_id' => $l->id, 'payment_id' => $bayar->id, 'penebus_tipe' => $d['penebus_tipe'], 'nama_penebus' => $d['nama_penebus'],
                'nik_penebus_enc' => $d['nik_penebus'] ?? null, 'surat_kuasa_path' => $suratKuasa?->store("kuasa/{$l->id}"),
                'nota_asli_diperiksa' => true, 'dikeluarkan_oleh' => $teller->id, 'dikeluarkan_at' => now(),
            ]);
            foreach ($l->collaterals()->where('status', 'tersimpan')->get() as $c) {
                $c->forceFill([
                    'status' => 'dikeluarkan',
                    'dokumen_dikembalikan_at' => now(),
                    'diserahkan_oleh' => $teller->id,
                    'diserahkan_kepada' => $d['nama_penebus'],
                    'catatan_serah_terima' => 'Agunan dan dokumen diserahkan kembali setelah pelunasan tebus.',
                ])->save();
            }

            return $r;
        });
    }

    /** Dipanggil scheduler harian. @return array{tenggang:int, siap_lelang:int} */
    public function perbaruiStatus(?CarbonInterface $hari = null): array
    {
        $hari = ($hari ?? today())->copy()->startOfDay();
        $hasil = ['tenggang' => 0, 'siap_lelang' => 0];

        Loan::withoutGlobalScopes()->where('status', L::Aktif->value)->whereDate('jatuh_tempo', '<', $hari)->get()->each(function (Loan $l) use (&$hasil) {
            DB::transaction(function () use ($l, &$hasil) {
                $this->setStatus($l, L::Tenggang, null, 'Lewat jatuh tempo');
                $hasil['tenggang']++;
            });
        });

        Loan::withoutGlobalScopes()->where('status', L::Tenggang->value)->whereDate('tenggang_sampai', '<', $hari)->get()->each(function (Loan $l) use (&$hasil) {
            DB::transaction(function () use ($l, &$hasil) {
                $this->setStatus($l, L::SiapLelang, null, 'Masa tenggang berakhir');
                $c = Collateral::withoutGlobalScopes()->where('application_id', $l->application_id)->first();
                $au = Auction::firstOrNew(['loan_id' => $l->id]);
                $au->fill(['collateral_id' => $c->id, 'status' => 'dijadwalkan', 'diberitahukan_at' => now(), 'tgl_lelang' => null, 'harga_limit' => null,
                    'harga_laku' => null, 'biaya_lelang' => 0, 'kelebihan' => 0, 'kelebihan_status' => 'belum', 'disetujui_oleh' => null])->save();
                $hasil['siap_lelang']++;
            });
        });

        return $hasil;
    }

    // ---------------- lelang ----------------

    public function jadwalkanLelang(Loan $l, User $u, string $tglLelang, int $hargaLimit): Auction
    {
        return DB::transaction(function () use ($l, $u, $tglLelang, $hargaLimit) {
            $l = $this->kunciLoan($l);
            $au = $this->lelangDari($l, L::SiapLelang);
            $paling = $au->diberitahukan_at->copy()->startOfDay()->addDays((int) ($l->snapshot_syarat['hari_pemberitahuan_lelang'] ?? 7));
            $tgl = Carbon::parse($tglLelang)->startOfDay();
            if ($tgl->lt($paling)) {
                throw new DomainException('Tanggal lelang paling cepat '.$paling->translatedFormat('j F Y').' (masa pemberitahuan ke anggota).');
            }
            if ($hargaLimit < $l->pokok_saldo) {
                throw new DomainException('Harga limit tidak boleh di bawah saldo pokok ('.Rupiah::format((int) $l->pokok_saldo).').');
            }
            $au->forceFill(['tgl_lelang' => $tgl, 'harga_limit' => $hargaLimit, 'disetujui_oleh' => $u->id])->save();

            return $au;
        });
    }

    public function catatHasilLelang(Loan $l, User $u, string $hasil, ?int $hargaLaku, int $biaya): Auction
    {
        return DB::transaction(function () use ($l, $u, $hasil, $hargaLaku, $biaya) {
            $l = $this->kunciLoan($l);
            $au = $this->lelangDari($l, L::SiapLelang);
            if (! $au->tgl_lelang) {
                throw new DomainException('Jadwalkan lelang terlebih dahulu.');
            }
            if ($au->tgl_lelang->gt(today())) {
                throw new DomainException('Hasil lelang baru dapat dicatat pada atau setelah tanggal lelang.');
            }
            if ($hasil === 'tidak_laku') {
                // Kembali ke antrean untuk dijadwalkan ulang (tercatat di audit).
                $au->forceFill(['status' => 'dijadwalkan', 'tgl_lelang' => null, 'harga_limit' => null])->save();

                return $au;
            }
            if ($hasil !== 'laku' || ! $hargaLaku || $hargaLaku < 1) {
                throw new DomainException('Isi harga laku.');
            }
            if ($biaya < 0) {
                throw new DomainException('Biaya lelang tidak valid.');
            }

            $kewajiban = $this->tagihan($l, $au->tgl_lelang)['tebus']['total'];
            $selisih = $hargaLaku - $biaya - $kewajiban; // > 0: hak anggota; <= 0: kekurangan, tidak ditagihkan
            $au->forceFill(['status' => 'laku', 'harga_laku' => $hargaLaku, 'biaya_lelang' => $biaya, 'kelebihan' => $selisih,
                'kelebihan_status' => $selisih > 0 ? 'belum' : 'tidak_ada'])->save();
            foreach ($l->collaterals()->where('status', 'tersimpan')->get() as $c) {
                $c->forceFill(['status' => 'dilelang'])->save();
            }
            $this->setStatus($l, $selisih > 0 ? L::Dilelang : L::SelesaiLelang, $u, 'Hasil lelang dicatat');

            return $au;
        });
    }

    public function bayarKelebihan(Loan $l, User $teller): Auction
    {
        return DB::transaction(function () use ($l, $teller) {
            $l = $this->kunciLoan($l);
            $au = $this->lelangDari($l, L::Dilelang);
            if ($au->status !== 'laku' || $au->kelebihan <= 0 || $au->kelebihan_status !== 'belum') {
                throw new DomainException('Tidak ada kelebihan yang perlu dibayarkan.');
            }
            $au->forceFill(['kelebihan_status' => 'dibayarkan'])->save();
            $this->setStatus($l, L::SelesaiLelang, $teller, 'Kelebihan hasil lelang dibayarkan ke anggota');

            return $au;
        });
    }

    // ---------------- internal ----------------

    private function terapkan(Loan $l, Payment $p, User $oleh): void
    {
        $s = $l->snapshot_syarat;
        $p->forceFill(['status' => 'sah', 'diverifikasi_oleh' => $p->metode === 'transfer' ? $oleh->id : null, 'diverifikasi_at' => $p->metode === 'transfer' ? now() : null])->save();

        if ($p->tipe === 'tebus') {
            $l->forceFill(['pokok_saldo' => 0])->save();
            $this->setStatus($l, L::Ditebus, $oleh, 'Ditebus ('.$p->no_kwitansi.')');
        } else {
            $lama = $l->jatuh_tempo->copy();
            $baru = $lama->copy()->addDays($s['tenor_hari']);
            $ke = $p->tipe === 'perpanjangan' ? $l->perpanjangan_ke + 1 : 0; // cicil menyetel ulang hitungan perpanjangan
            if ($p->tipe === 'perpanjangan') {
                LoanExtension::create(['loan_id' => $l->id, 'payment_id' => $p->id, 'jatuh_tempo_lama' => $lama, 'jatuh_tempo_baru' => $baru, 'ke' => $ke]);
            }
            $l->forceFill([
                'pokok_saldo' => $l->pokok_saldo - $p->pokok_dibayar, 'jatuh_tempo' => $baru,
                'tenggang_sampai' => $baru->copy()->addDays($s['masa_tenggang_hari']), 'perpanjangan_ke' => $ke,
            ])->save();
            $this->setStatus($l, L::Aktif, $oleh, ucfirst($p->tipe).' ('.$p->no_kwitansi.')');
        }

        // Lunas/diperpanjang sebelum lelang -> pembatalan antrean lelang.
        Auction::where('loan_id', $l->id)->where('status', 'dijadwalkan')->get()->each(fn (Auction $a) => $a->forceFill(['status' => 'batal'])->save());
    }

    private function pastikan(array $bagian): void
    {
        if (! $bagian['boleh']) {
            throw new DomainException($bagian['alasan'] ?? 'Pembayaran ini tidak dapat dilakukan.');
        }
    }

    private function lelangDari(Loan $l, L $harus): Auction
    {
        if ($l->status !== $harus) {
            throw new DomainException('Status gadai bukan '.$harus->label().'.');
        }

        return Auction::where('loan_id', $l->id)->first() ?? throw new DomainException('Data lelang tidak ditemukan.');
    }

    private function keadaan(Loan $l): array
    {
        return [
            'jatuh_tempo' => $l->jatuh_tempo->toDateString(), 'tenggang_sampai' => $l->tenggang_sampai?->toDateString(),
            'pokok_saldo' => (int) $l->pokok_saldo, 'perpanjangan_ke' => (int) $l->perpanjangan_ke, 'status' => $l->status->value,
        ];
    }

    private function kunciLoan(Loan $l): Loan
    {
        return Loan::withoutGlobalScopes()->whereKey($l->id)->lockForUpdate()->firstOrFail();
    }

    private function setStatus(Loan $l, L $ke, ?User $oleh, ?string $alasan): void
    {
        $dari = $l->status;
        if ($dari === $ke) {
            return;
        }
        $l->forceFill(['status' => $ke])->save();
        $this->riwayat($l, $dari, $ke, $oleh, $alasan);
    }

    private function riwayat(Loan $l, ?L $dari, L $ke, ?User $oleh, ?string $alasan): void
    {
        LoanStatusHistory::create(['loan_id' => $l->id, 'dari' => $dari?->value, 'ke' => $ke->value, 'oleh' => $oleh?->id, 'alasan' => $alasan]);
    }

    private function nomor(string $awalan, string $kodeCabang, string $model, string $kolom): string
    {
        do {
            $n = "$awalan-$kodeCabang-".now()->format('ymd').'-'.strtoupper(Str::random(4));
        } while ($model::withoutGlobalScopes()->where($kolom, $n)->exists());

        return $n;
    }
}
