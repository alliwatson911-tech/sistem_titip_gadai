<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Announcement;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Product;
use App\Models\User;
use App\Services\ApplicationFlow;
use App\Services\LoanService;
use App\Services\PengingatService;
use App\Support\Pii;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * DATA FIKTIF UNTUK UJI COBA PENGGUNA (hanya bila LINYGADAI_MODE_UJI=true).
 * Semua nama, NIK, alamat, kontak, dan tautan adalah karangan; tidak ada yang nyata. Gadai dibuat lewat service yang sama
 * dengan aplikasi, dengan tanggal dimundurkan, sehingga setiap status (aktif, jatuh tempo, tenggang, lelang, ditebus, selesai) ada contohnya.
 * Kata sandi anggota demo diacak dan dicetak sekali; tidak ada kredensial di kode.
 */
class DemoSeeder extends Seeder
{
    private Carbon $hariIni;

    private array $baris = [];

    public function run(): void
    {
        if (! config('linygadai.mode_uji')) {
            $this->command?->warn('DemoSeeder dilewati: LINYGADAI_MODE_UJI bukan true.');

            return;
        }
        if (Loan::withoutGlobalScopes()->exists()) {
            $this->command?->warn('Data demo sudah ada; dilewati.');

            return;
        }

        $this->hariIni = now()->startOfDay();
        $this->kontakDanKonten();
        $this->pengumumanDanFaq();
        $m = $this->anggotaDemo();
        $this->skenario($m);
        Carbon::setTestNow();
        auth()->logout();

        $this->command?->warn('Anggota DEMO (fiktif). Catat kata sandinya sekarang; tidak ditampilkan lagi:');
        $this->command?->table(['Email', 'Nama', 'Cabang', 'Status keanggotaan', 'Kata sandi'], $this->baris);
    }

    // ---------------------------------------------------------------- konten

    private function kontakDanKonten(): void
    {
        $tanda = ' (alamat fiktif untuk uji coba)';
        foreach ([
            'PUSAT' => ['alamat' => 'Jl. Contoh Raya No. 1, Makassar'.$tanda, 'telepon' => '0411 000 0001', 'whatsapp' => '081200000001', 'email' => 'halo@linygadai.test', 'lat' => -5.147665, 'lng' => 119.432731, 'jam_buka' => 'Senin–Sabtu 08.00–16.00 WITA'],
            'A' => ['alamat' => 'Jl. Contoh Barat No. 10, Makassar'.$tanda, 'telepon' => '0411 000 0010', 'whatsapp' => '081200000010', 'email' => 'cabang.a@linygadai.test', 'lat' => -5.1350, 'lng' => 119.4100, 'jam_buka' => 'Senin–Sabtu 08.00–16.00 WITA'],
            'B' => ['alamat' => 'Jl. Contoh Timur No. 20, Gowa'.$tanda, 'telepon' => '0411 000 0020', 'whatsapp' => '081200000020', 'email' => 'cabang.b@linygadai.test', 'lat' => -5.1900, 'lng' => 119.4600, 'jam_buka' => 'Senin–Jumat 08.00–15.30 WITA'],
        ] as $kode => $d) {
            Branch::where('kode', $kode)->update($d);
        }

        $upsert = fn (string $k, string $v) => DB::table('site_contents')->updateOrInsert(['branch_id' => null, 'kunci' => $k], ['nilai' => $v, 'created_at' => now(), 'updated_at' => now()]);
        $upsert('sosmed', json_encode(['instagram' => 'https://example.com/linygadai-instagram', 'facebook' => 'https://example.com/linygadai-facebook', 'tiktok' => 'https://example.com/linygadai-tiktok', 'youtube' => 'https://example.com/linygadai-youtube']));
        $upsert('izin_usaha', '[DATA UJI COBA — BUKAN IZIN SEBENARNYA] Bagian ini akan berisi nomor dan lembaga penerbit izin usaha setelah diverifikasi pengelola. Contoh format: "Izin usaha pergadaian No. XXX/YYYY, diterbitkan oleh lembaga berwenang pada tanggal ...".');
    }

    private function pengumumanDanFaq(): void
    {
        $a = Branch::where('kode', 'A')->value('id');
        $b = Branch::where('kode', 'B')->value('id');
        foreach ([
            [$a, 'Antrean lebih singkat di pagi hari', 'Cabang A melayani penaksiran paling cepat pukul 08.00–10.00. Bawa KTP asli dan kelengkapan barang untuk mempercepat proses.'],
            [$b, 'Loket penebusan dipisah', 'Cabang B kini memisahkan loket penebusan dan pencairan agar antrean tidak bercampur.'],
            [null, 'Cek tanggal jatuh tempo di akun Anda', 'Masuk ke akun anggota untuk melihat jatuh tempo, tagihan perpanjang, dan riwayat pembayaran. Kami juga mengirim email pengingat sebelum jatuh tempo.'],
            [null, 'Waspada penipuan', 'Petugas linygadai tidak pernah meminta kata sandi, kode verifikasi, atau transfer ke rekening pribadi. Pembayaran hanya melalui teller cabang.'],
        ] as [$cabang, $judul, $isi]) {
            Announcement::firstOrCreate(
                ['judul' => $judul],
                ['isi' => $isi, 'is_published' => true, 'branch_id' => $cabang]
            );
        }

        foreach ([
            ['faq', 'Apakah barang bisa ditebus di cabang lain?', 'Penebusan dilakukan di cabang tempat akad dibuat, karena barang disimpan di cabang tersebut. (Jawaban contoh untuk uji coba.)'],
            ['faq', 'Bagaimana jika nota gadai saya hilang?', 'Segera hubungi cabang. Penebusan tanpa nota asli memerlukan pemeriksaan tambahan dan persetujuan Manager cabang. (Jawaban contoh untuk uji coba.)'],
            ['faq', 'Apakah menebus lebih awal lebih murah?', 'Sewa modal dihitung per blok 15 hari, jadi menebus lebih awal membayar lebih sedikit daripada menunggu jatuh tempo. Lihat simulasi untuk perkiraannya.'],
            ['faq', 'Bisakah saya menitipkan lebih dari satu barang?', 'Bisa, dengan pengajuan terpisah untuk tiap barang. Jumlah pengajuan yang berjalan bersamaan dibatasi; selesaikan atau batalkan salah satu bila batas tercapai.'],
            ['informasi', 'Apa yang terjadi bila saya lupa kata sandi?', 'Gunakan "Lupa kata sandi" di halaman masuk. Tautan atur ulang dikirim ke email Anda dan berlaku 60 menit.'],
            ['informasi', 'Bagaimana data identitas saya diperlakukan?', 'NIK disimpan terenkripsi, foto KTP hanya dapat dilihat Anda dan petugas cabang Anda, dan setiap akses petugas tercatat.'],
        ] as $i => [$kel, $q, $a2]) {
            Faq::create(['kelompok' => $kel, 'pertanyaan' => $q, 'jawaban' => $a2, 'urutan' => 20 + $i, 'is_published' => true]);
        }
    }

    // ---------------------------------------------------------------- anggota

    /** @return array<string,Member> */
    private function anggotaDemo(): array
    {
        $kotak = [
            'siti' => ['Siti Rahma (contoh)', 'A', 'terverifikasi', true, true],
            'andi' => ['Andi Pratama (contoh)', 'A', 'terverifikasi', true, true],
            'dewi' => ['Dewi Lestari (contoh)', 'A', 'calon', true, true],
            'budi' => ['Budi Santoso (contoh)', 'A', 'ditolak', true, true],
            'rahmat' => ['Rahmat Hidayat (contoh)', 'B', 'terverifikasi', true, true],
            'nur' => ['Nur Aisyah (contoh)', 'B', 'terverifikasi', true, true],
            'yusuf' => ['Yusuf Kalla (contoh)', 'B', 'calon', false, false],
            'lina' => ['Lina Marlina (contoh)', 'B', 'terverifikasi', true, true],
        ];
        $hasil = [];
        $n = 0;
        foreach ($kotak as $kunci => [$nama, $cabang, $status, $adaKtp, $emailOk]) {
            $n++;
            $sandi = Str::password(14, symbols: false);
            $email = "$kunci.demo@linygadai.test";
            $u = new User(['name' => $nama, 'email' => $email, 'password' => $sandi]);
            $u->forceFill(['role' => Role::Member, 'status' => 'aktif', 'email_verified_at' => $emailOk ? now() : null])->save();

            $nik = '73719999'.str_pad((string) $n, 8, '0', STR_PAD_LEFT); // NIK fiktif
            $m = new Member([
                'user_id' => $u->id, 'branch_id' => Branch::where('kode', $cabang)->value('id'), 'nama_lengkap' => $nama,
                'nik_enc' => $nik, 'nik_hash' => Pii::hash($nik), 'telepon' => '08120000'.str_pad((string) (100 + $n), 4, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Contoh No. '.$n.' (fiktif)', 'tgl_lahir' => '1990-0'.min(9, $n).'-15',
            ]);
            $m->forceFill(['status' => $status, 'verified_at' => $status === 'terverifikasi' ? now() : null])->save();
            if ($adaKtp) {
                $path = "ktp/{$m->id}/contoh.png";
                Storage::put($path, file_get_contents($this->gambar('KTP CONTOH - FIKTIF', $nama)));
                $m->forceFill(['ktp_path' => $path])->save();
            }
            $hasil[$kunci] = $m;
            $this->baris[] = [$email, $nama, $cabang, $status.($emailOk ? '' : ' (email belum diverifikasi)'), $sandi];
        }

        return $hasil;
    }

    // ---------------------------------------------------------------- skenario gadai

    private function pada(int $offset): void
    {
        Carbon::setTestNow($this->hariIni->copy()->addDays($offset)->setTime(9, 0));
    }

    private function staf(string $peran, string $cabang): User
    {
        return User::where('email', strtolower($peran).'.'.strtolower($cabang).'@linygadai.test')->firstOrFail();
    }

    private function gambar(string $judul, string $sub = ''): string
    {
        $im = imagecreatetruecolor(480, 300);
        imagefill($im, 0, 0, imagecolorallocate($im, 244, 239, 230));
        imagerectangle($im, 6, 6, 473, 293, imagecolorallocate($im, 27, 42, 47));
        imagestring($im, 5, 30, 120, $judul, imagecolorallocate($im, 27, 42, 47));
        imagestring($im, 4, 30, 150, $sub, imagecolorallocate($im, 127, 90, 14));
        imagestring($im, 3, 30, 270, 'DATA UJI COBA - BUKAN DOKUMEN ASLI', imagecolorallocate($im, 181, 71, 46));
        $p = tempnam(sys_get_temp_dir(), 'demo').'.png';
        imagepng($im, $p);
        imagedestroy($im);

        return $p;
    }

    private function foto(string $label): array
    {
        $f = [];
        foreach (['depan' => 'Tampak depan', 'segel' => 'Segel terpasang'] as $jenis => $ket) {
            $f[$jenis] = new UploadedFile($this->gambar("FOTO CONTOH: $label", $ket), "$jenis.png", 'image/png', null, true);
        }

        return $f;
    }

    private const BARANG = [
        'gadai-smartphone' => ['Samsung', 'Galaxy A54'], 'gadai-laptop' => ['Lenovo', 'ThinkPad T14'], 'gadai-kamera' => ['Canon', 'EOS M50'],
        'gadai-konsol-game' => ['Sony', 'PlayStation 5'], 'gadai-televisi' => ['LG', 'OLED C2 55"'], 'gadai-smartwatch' => ['Apple', 'Watch SE'],
    ];

    /** Ajukan → terima barang → taksir. Mengembalikan Application. */
    private function sampaiTaksir(Member $m, string $slug, int $pinjam, int $taksiran, string $rekom = 'setuju'): Application
    {
        $cabang = Branch::find($m->branch_id)->kode;
        $alur = app(ApplicationFlow::class);
        $produk = Product::where('slug', $slug)->firstOrFail();
        [$merek, $model] = self::BARANG[$slug];

        Auth::setUser($m->user);
        $a = $alur->ajukan($m, $produk, $pinjam, 'Pengajuan contoh uji coba', $m->user);
        Auth::setUser($this->staf('teller', $cabang));
        $alur->terimaBarang($a, Auth::user(), ['identitas_dicocokkan' => true, 'merek' => $merek, 'model' => $model, 'imei_serial' => (string) random_int(350000000000000, 359999999999999),
            'kelengkapan' => 'Dus dan pengisi daya', 'kondisi' => 'Baik, goresan halus', 'no_segel' => 'SG-'.strtoupper(Str::random(5)), 'lokasi_simpan' => 'Brankas '.random_int(1, 3)], $this->foto("$merek $model"));
        Auth::setUser($this->staf('analis', $cabang));
        $alur->taksir($a, Auth::user(), ['nilai_pasar' => (int) ($taksiran * 1.15), 'nilai_taksiran' => $taksiran, 'periksa' => ['identitas_cocok' => 1, 'barang_menyala' => 1, 'bebas_kunci' => 1, 'kelengkapan_sesuai' => 1, 'riwayat_baik' => 1],
            'rekomendasi' => $rekom, 'catatan' => $rekom === 'setuju' ? 'Kondisi baik, sesuai laporan (catatan contoh).' : 'Layar retak dan tidak sesuai laporan (catatan contoh).']);

        return $a->refresh();
    }

    private function cairkan(Application $a, string $cabang): Loan
    {
        Auth::setUser($this->staf('teller', $cabang));

        return app(LoanService::class)->cairkan($a->refresh(), Auth::user(), 'tunai', null);
    }

    private function skenario(array $m): void
    {
        $svc = app(LoanService::class);
        $alur = app(ApplicationFlow::class);

        // 1. Gadai baru (aktif, 5 hari lalu)
        $this->pada(-5);
        $this->cairkan($this->sampaiTaksir($m['siti'], 'gadai-smartphone', 2_000_000, 3_000_000), 'A');

        // 2. Gadai jatuh tempo 3 hari lagi (laptop)
        $this->pada(-27);
        $this->cairkan($this->sampaiTaksir($m['siti'], 'gadai-laptop', 4_500_000, 6_000_000), 'A');

        // 3. Gadai terlambat → masa tenggang
        $this->pada(-31);
        $this->cairkan($this->sampaiTaksir($m['andi'], 'gadai-kamera', 1_200_000, 2_000_000), 'A');

        // 4. Gadai siap lelang, lelang dijadwalkan (Cabang A)
        $this->pada(-40);
        $l4 = $this->cairkan($this->sampaiTaksir($m['andi'], 'gadai-konsol-game', 2_500_000, 3_500_000), 'A');
        $this->pada(-6);
        $svc->perbaruiStatus(); // lewat tenggang → siap lelang, diberitahukan hari ini (-6)
        $this->pada(0);
        Auth::setUser($this->staf('manager', 'A'));
        $svc->jadwalkanLelang($l4->refresh(), Auth::user(), $this->hariIni->copy()->addDays(2)->toDateString(), 2_600_000);

        // 5. Gadai ditebus + barang dikeluarkan
        $this->pada(-20);
        $l5 = $this->cairkan($this->sampaiTaksir($m['siti'], 'gadai-smartwatch', 1_000_000, 1_600_000), 'A');
        $this->pada(-10);
        Auth::setUser($this->staf('teller', 'A'));
        $svc->bayar($l5->refresh(), Auth::user(), 'tebus', 'tunai');
        $svc->keluarkanBarang($l5->refresh(), Auth::user(), ['penebus_tipe' => 'penggadai', 'nama_penebus' => $m['siti']->nama_lengkap, 'nota_asli_diperiksa' => true]);

        // 6. Cabang B: perpanjangan lalu cicil
        $this->pada(-65);
        $l6 = $this->cairkan($this->sampaiTaksir($m['rahmat'], 'gadai-smartphone', 3_000_000, 4_000_000), 'B');
        Auth::setUser($this->staf('teller', 'B'));
        $this->pada(-36);
        $svc->bayar($l6->refresh(), Auth::user(), 'perpanjangan', 'tunai');
        $this->pada(-6);
        $svc->bayar($l6->refresh(), Auth::user(), 'cicil', 'tunai', 500_000);

        // 7. Cabang B: lelang laku dengan kelebihan, sudah dibayarkan ke anggota
        $this->pada(-70);
        $l7 = $this->cairkan($this->sampaiTaksir($m['nur'], 'gadai-smartphone', 1_500_000, 2_500_000), 'B');
        $this->pada(-39);
        $svc->perbaruiStatus();
        $this->pada(-36);
        $svc->perbaruiStatus();
        Auth::setUser($this->staf('manager', 'B'));
        $svc->jadwalkanLelang($l7->refresh(), Auth::user(), $this->hariIni->copy()->addDays(-29)->toDateString(), 1_500_000);
        $this->pada(-29);
        Auth::setUser($this->staf('teller', 'B'));
        $svc->catatHasilLelang($l7->refresh(), Auth::user(), 'laku', 2_200_000, 50_000);
        $this->pada(-28);
        $svc->bayarKelebihan($l7->refresh(), Auth::user());

        // 8. Taksir ulang pada gadai aktif Cabang B (plafon turun → wajib setor selisih)
        $this->pada(-12);
        $l8 = $this->cairkan($this->sampaiTaksir($m['lina'], 'gadai-laptop', 4_000_000, 5_500_000), 'B');
        $this->pada(-2);
        Auth::setUser($this->staf('analis', 'B'));
        $svc->taksirUlang($l8->refresh(), Auth::user(), 4_000_000, 3_500_000, 'Harga pasar turun (catatan contoh).');

        // ---- pengajuan di berbagai tahap ----
        $this->pada(-2);
        Auth::setUser($m['andi']->user);
        $alur->ajukan($m['andi'], Product::where('slug', 'gadai-televisi')->first(), 3_000_000, 'Menunggu dibawa ke cabang', $m['andi']->user);

        $this->pada(-1);
        $cabangA = $this->staf('teller', 'A');
        Auth::setUser($m['siti']->user);
        $p = $alur->ajukan($m['siti'], Product::where('slug', 'gadai-kamera')->first(), 1_800_000, null, $m['siti']->user);
        Auth::setUser($cabangA);
        $alur->terimaBarang($p, $cabangA, ['identitas_dicocokkan' => true, 'merek' => 'Canon', 'model' => 'EOS M50', 'imei_serial' => (string) random_int(350000000000000, 359999999999999), 'no_segel' => 'SG-'.strtoupper(Str::random(5))], $this->foto('Canon EOS M50'));

        $this->pada(-3);
        $this->sampaiTaksir($m['rahmat'], 'gadai-laptop', 8_000_000, 12_000_000);         // menunggu persetujuan Manager

        $this->pada(-1);
        $this->sampaiTaksir($m['nur'], 'gadai-smartphone', 1_500_000, 2_500_000);          // disetujui, siap cair

        $this->pada(-4);
        $this->sampaiTaksir($m['lina'], 'gadai-smartwatch', 900_000, 1_400_000, 'tolak'); // ditolak; barang belum dikembalikan

        // ---- status terkini + email pengingat (dikirim ke log bila MAIL_MAILER=log) ----
        Carbon::setTestNow();
        $svc->perbaruiStatus();
        app(PengingatService::class)->jalankan();
    }
}
