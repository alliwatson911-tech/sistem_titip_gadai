<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\DB;

/** Pembaca konten dan kebijakan situs publik (satu sumber: tabel site_contents dan policy_settings). */
class Situs
{
    /** Pastikan data master publik (cabang, kebijakan, produk, konten) otomatis terisi bila database masih kosong. */
    public static function pastikanDataAwal(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        try {
            if (Branch::count() === 0 && class_exists(BranchSeeder::class)) {
                (new BranchSeeder)->run();
            }
            if (DB::table('policy_settings')->count() === 0 && class_exists(PolicySeeder::class)) {
                (new PolicySeeder)->run();
            }
            if (Product::count() === 0 && class_exists(ProductSeeder::class)) {
                (new ProductSeeder)->run();
            }
            if (DB::table('site_contents')->count() === 0 && class_exists(ContentSeeder::class)) {
                (new ContentSeeder)->run();
            }
        } catch (\Throwable) {
            // Lanjut jika tabel belum termigrasi atau koneksi terbatas
        }
    }

    /** Konten bawaan sebagai fallback jika database belum terisi. */
    public static function defaultKonten(): array
    {
        return [
            'tagline' => 'Titip hari ini, tenang sampai tebus.',
            'sambutan' => 'Selamat datang di PERSIDEN HP Titip Gadai. Terpercaya sejak tahun 2005 dalam melayani masyarakat Makassar dan sekitarnya. Kami membantu Anda mendapatkan dana tunai dengan menitipkan smartphone, laptop, tablet, kamera, BPKB kendaraan bermotor, sertifikat properti, dan surat berharga dengan perhitungan jasa penitipan yang transparan dan aman.',
            'ringkasan' => 'PERSIDEN HP Titip Gadai melayani penitipan barang berharga dan surat berharga (elektronik, BPKB kendaraan, sertifikat tanah & rumah, dan surat berharga) melalui jaringan 7 loket cabang strategis di Kota Makassar dan Kabupaten Gowa. Berpengalaman lebih dari 20 tahun sejak 2005, setiap barang titipan diperiksa cermat di hadapan Anda, diberi segel khusus, dan disimpan aman tanpa digunakan selama masa penitipan.',
            'standar_layanan' => [
                ['judul' => 'Taksiran yang dijelaskan', 'isi' => 'Petugas menyampaikan dasar penilaian barang atau dokumen Anda secara transparan sebelum Anda memutuskan setuju atau tidak.'],
                ['judul' => 'Jasa penitipan dirinci di awal', 'isi' => 'Nilai yang diterima, tarif jasa penitipan, dan biaya administrasi ditulis terpisah di nota. Tidak ada potongan tersembunyi.'],
                ['judul' => 'Barang aman & BPKB tetap jalan', 'isi' => 'Barang elektronik disimpan bersegel di tempat penyimpanan khusus cabang. Pada titip BPKB kendaraan, unit fisik tetap digunakan penitip.'],
                ['judul' => 'Identitas & kepemilikan diperiksa', 'isi' => 'Kami memeriksa e-KTP asli dan bukti kepemilikan sah demi melindungi hak barang penitip.'],
                ['judul' => 'Jatuh tempo yang pasti', 'isi' => 'Tenor {tenor} hari (atau mingguan 1-2 minggu) dan dapat diperpanjang. Tanggal jatuh tempo tercantum pasti di nota fisik dan portal penitip.'],
                ['judul' => 'Tiga pilihan penyelesaian', 'isi' => 'Perpanjang masa titip, bayar sebagian pokok, atau tebus lunas kapan saja sebelum jatuh tempo.'],
            ],
            'panduan_anggota' => [
                ['judul' => 'Datang ke Loket', 'isi' => 'Bawa e-KTP fisik asli dan barang berharga yang ingin dititipkan ke cabang PERSIDEN HP terdekat.'],
                ['judul' => 'Pencatatan Akun oleh Staf', 'isi' => 'Petugas loket cabang mendaftarkan data diri Anda dan membuat akun penitip di sistem.'],
                ['judul' => 'Pantau Transaksi', 'isi' => 'Masuk ke portal penitip menggunakan nomor WhatsApp dan kata sandi untuk melihat detail transaksi dan jatuh tempo.'],
            ],
            'panduan_gadai' => [
                ['judul' => 'Hitung perkiraan', 'isi' => 'Gunakan kalkulator Simulasi Jasa di situs ini untuk melihat perkiraan nilai yang diterima dan jasa penitipan.'],
                ['judul' => 'Kunjungi loket cabang', 'isi' => 'Bawa barang jaminan beserta kelengkapannya (charger/aksesori) dan e-KTP asli ke loket cabang PERSIDEN HP.'],
                ['judul' => 'Pemeriksaan & taksiran', 'isi' => 'Petugas memeriksa kondisi fisik barang dan kelengkapan secara terbuka di hadapan Anda.'],
                ['judul' => 'Penerbitan nota & pencairan', 'isi' => 'Setelah Anda setuju dengan taksiran dan jasa penitipan, nota dicetak, barang disegel, dan dana diserahkan.'],
                ['judul' => 'Pantau & selesaikan', 'isi' => 'Sebelum tanggal jatuh tempo, Anda dapat memperpanjang, membayar sebagian pokok, atau menebus lunas barang.'],
            ],
            'syarat_online' => [
                'Membawa e-KTP fisik asli yang masih berlaku saat datang ke loket cabang.',
                'Barang milik pribadi yang sah dan bebas dari sengketa pihak mana pun.',
                'Untuk smartphone, tablet, dan laptop: kondisi dapat menyala normal dan sudah logout dari seluruh kunci akun (iCloud, Google, sandi layar).',
                'Membawa kelengkapan pengisi daya (charger adaptor) dan perlengkapan unit jika ada.',
                'Penitipan diproses langsung di loket cabang oleh petugas resmi PERSIDEN HP.',
            ],
            'tata_cara' => [
                ['judul' => 'Datang ke cabang', 'isi' => 'Kunjungi loket cabang PERSIDEN HP terdekat dengan membawa barang jaminan dan e-KTP asli.'],
                ['judul' => 'Pemeriksaan di loket', 'isi' => 'Petugas memeriksa kondisi fisik dan kelengkapan secara langsung di hadapan Anda.'],
                ['judul' => 'Penaksiran nilai', 'isi' => 'Petugas menetapkan taksiran nilai yang diterima dan menjelaskan rincian jasa penitipan.'],
                ['judul' => 'Pencatatan & penyegelan', 'isi' => 'Data dicatat ke sistem, nota penitipan diterbitkan, dan barang disimpan aman dalam segel khusus.'],
                ['judul' => 'Pencairan dana', 'isi' => 'Dana tunai diserahkan seketika di loket cabang sesuai nilai yang diterima tanpa potongan tersembunyi.'],
                ['judul' => 'Perpanjang atau tebus', 'isi' => 'Lakukan perpanjangan, pembayaran sebagian pokok, atau tebus lunas kapan saja sebelum jatuh tempo.'],
            ],
            'sosmed' => [
                'instagram' => 'https://www.instagram.com/persidenhp.official/',
                'facebook' => '',
                'tiktok' => '',
                'youtube' => '',
            ],
            'izin_usaha' => '',
        ];
    }

    /** Kebijakan bawaan sebagai fallback jika database belum terisi. */
    public static function defaultKebijakan(): array
    {
        return [
            'limit_persetujuan_manager' => 5_000_000,
            'pinjaman_min' => 100_000,
            'pinjaman_max' => 25_000_000,
            'masa_tenggang_hari' => 3,
            'denda_harian' => 0,
            'maks_perpanjangan' => 3,
            'cicilan_min' => 50_000,
            'hari_pemberitahuan_lelang' => 7,
            'batas_bawa_barang_hari' => 7,
            'maks_pengajuan_terbuka' => 3,
        ];
    }

    /** @return array<string,mixed> kunci => nilai (JSON otomatis didekode). Hanya konten global (branch_id null). */
    public static function konten(): array
    {
        try {
            $dariDb = DB::table('site_contents')->whereNull('branch_id')->pluck('nilai', 'kunci')
                ->map(function ($v) {
                    $v = (string) $v;
                    if ($v !== '' && in_array($v[0], ['[', '{'], true)) {
                        $d = json_decode($v, true);

                        return json_last_error() === JSON_ERROR_NONE ? $d : $v;
                    }

                    return $v;
                })->all();

            return array_merge(self::defaultKonten(), $dariDb);
        } catch (\Throwable) {
            return self::defaultKonten();
        }
    }

    /** @return array<string,mixed> nilai kebijakan yang berlaku hari ini (yang terbaru per kunci). */
    public static function kebijakan(): array
    {
        try {
            $hasil = [];
            foreach (DB::table('policy_settings')->where('berlaku_mulai', '<=', today())->orderBy('berlaku_mulai')->orderBy('id')->get() as $r) {
                $hasil[$r->key] = json_decode($r->value, true);
            }

            return array_merge(self::defaultKebijakan(), $hasil);
        } catch (\Throwable) {
            return self::defaultKebijakan();
        }
    }

    /** Ganti token {nama} pada teks dengan nilai kebijakan, agar angka di teks selalu mengikuti pengaturan. */
    public static function isi(?string $teks, array $nilai): string
    {
        $teks = (string) $teks;
        foreach ($nilai as $k => $v) {
            $teks = str_replace('{'.$k.'}', (string) $v, $teks);
        }

        return $teks;
    }
}
