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
            'sambutan' => 'Selamat datang di linygadai. Kami membantu Anda mendapatkan dana tunai dengan menitipkan barang elektronik yang Anda miliki. Sejak awal kami jelaskan semuanya: berapa pinjaman yang bisa Anda terima, berapa biayanya, kapan jatuh tempo, dan bagaimana cara menebus barang Anda kembali.',
            'ringkasan' => 'linygadai melayani titip gadai barang elektronik lewat dua cabang, Cabang A dan Cabang B, yang dikelola dari satu kantor pusat. Setiap barang diperiksa, ditaksir oleh analis, disegel, lalu disimpan tanpa dipakai selama masa titip. Anggota dapat mengajukan lewat situs ini, memantau jatuh tempo, dan melihat riwayat pembayaran dari akunnya.',
            'standar_layanan' => [
                ['judul' => 'Taksiran yang dijelaskan', 'isi' => 'Analis menyampaikan dasar penilaian barang Anda sebelum Anda memutuskan setuju atau tidak.'],
                ['judul' => 'Biaya dirinci dari awal', 'isi' => 'Pokok pinjaman, sewa modal, dan biaya administrasi ditulis terpisah di nota. Tidak ada biaya yang muncul belakangan.'],
                ['judul' => 'Barang disegel dan tidak dipakai', 'isi' => 'Barang jaminan diberi nomor segel, disimpan di cabang, dan tidak digunakan selama masa titip.'],
                ['judul' => 'Identitas diperiksa', 'isi' => 'Kami memeriksa KTP setiap penggadai. Ini melindungi Anda dan melindungi barang yang bukan milik penitip.'],
                ['judul' => 'Jatuh tempo yang jelas', 'isi' => 'Tenor {tenor} hari dan dapat diperpanjang. Tanggal jatuh tempo tercantum di nota dan di akun anggota.'],
                ['judul' => 'Tiga pilihan bayar', 'isi' => 'Perpanjang, cicil sebagian pokok, atau tebus. Anda memilih sesuai kemampuan, dan setiap pembayaran ada kwitansinya.'],
            ],
            'panduan_anggota' => [
                ['judul' => 'Daftar akun', 'isi' => 'Isi nama, NIK, nomor telepon, dan email sesuai KTP, lalu pilih cabang yang paling dekat dengan Anda.'],
                ['judul' => 'Tunggu verifikasi', 'isi' => 'Admin cabang mencocokkan data Anda. Bila diperlukan, Anda diminta datang membawa KTP asli.'],
                ['judul' => 'Akun aktif', 'isi' => 'Setelah terverifikasi, Anda dapat mengajukan titip gadai, memantau jatuh tempo, dan melihat riwayat pembayaran.'],
            ],
            'panduan_gadai' => [
                ['judul' => 'Hitung perkiraan', 'isi' => 'Pakai simulasi di halaman ini untuk melihat perkiraan pinjaman dan total yang harus ditebus.'],
                ['judul' => 'Ajukan online', 'isi' => 'Pilih jenis barang dan jumlah pinjaman yang Anda inginkan. Pengajuan online membuat antrean Anda lebih cepat di cabang.'],
                ['judul' => 'Datang ke cabang', 'isi' => 'Bawa barang beserta kelengkapannya (dus, pengisi daya) dan KTP asli. Pencairan tidak dilakukan tanpa bertemu langsung.'],
                ['judul' => 'Barang diperiksa dan ditaksir', 'isi' => 'Petugas memeriksa keaslian barang dan identitas Anda, lalu analis menetapkan taksiran.'],
                ['judul' => 'Setuju dan dana diterima', 'isi' => 'Jika Anda setuju dengan taksiran dan biayanya, nota gadai diterbitkan dan dana dicairkan.'],
                ['judul' => 'Pantau dan bayar', 'isi' => 'Sebelum jatuh tempo, pilih perpanjang, cicil, atau tebus. Barang diserahkan kembali setelah penebusan.'],
            ],
            'syarat_online' => [
                'Akun anggota sudah terverifikasi oleh admin cabang.',
                'Memiliki KTP yang masih berlaku, dan membawa aslinya saat datang ke cabang.',
                'Barang milik sendiri, dapat dinyalakan, dan sudah bebas dari kunci akun (misalnya akun perangkat atau kode kunci layar).',
                'Membawa kelengkapan barang bila ada: dus, pengisi daya, kabel, atau aksesori.',
                'Jumlah pinjaman berada dalam batas produk yang dipilih; angka pastinya ditetapkan analis setelah barang diperiksa.',
            ],
            'tata_cara' => [
                ['judul' => 'Pengajuan', 'isi' => 'Anda mengajukan lewat akun anggota. Status pengajuan dapat dipantau kapan saja.'],
                ['judul' => 'Barang diserahkan', 'isi' => 'Anda datang ke cabang. Teller memeriksa identitas, menerima barang, lalu menyegelnya.'],
                ['judul' => 'Penaksiran', 'isi' => 'Analis menilai kondisi dan nilai barang, lalu memberi rekomendasi.'],
                ['judul' => 'Persetujuan', 'isi' => 'Pinjaman dalam batas biasa disetujui di cabang. Pinjaman di atas batas disetujui Manager cabang.'],
                ['judul' => 'Pencairan', 'isi' => 'Teller menerbitkan nota gadai dan mencairkan dana. Tanggal jatuh tempo tertulis di nota.'],
                ['judul' => 'Perpanjang, cicil, atau tebus', 'isi' => 'Bayar sewa modal dan administrasi untuk memperpanjang, tambahkan pokok untuk mencicil, atau lunasi untuk menebus. Setelah tebus, barang diserahkan.'],
            ],
            'sosmed' => ['instagram' => '', 'facebook' => '', 'tiktok' => '', 'youtube' => ''],
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
