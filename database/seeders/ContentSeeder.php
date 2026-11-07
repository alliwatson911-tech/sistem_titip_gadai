<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Konten awal situs publik, ditulis ulang dengan kata sendiri (bukan salinan situs lain).
 * Token {tenor}, {tenggang}, {lelang_hari}, {maks_perpanjangan} diganti saat tampil dari pengaturan kebijakan.
 * TIDAK ADA klaim izin usaha atau asuransi: kolom izin_usaha sengaja kosong sampai diverifikasi pengelola.
 * Tagline di bawah adalah USULAN sementara (pilihan 1 dari docs/00-rancangan-tahap0.md §5).
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $konten = [
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

        foreach ($konten as $kunci => $nilai) {
            DB::table('site_contents')->updateOrInsert(
                ['branch_id' => null, 'kunci' => $kunci],
                ['nilai' => is_array($nilai) ? json_encode($nilai, JSON_UNESCAPED_UNICODE) : $nilai, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        $faq = [
            ['Barang apa saja yang diterima?', 'Barang elektronik: ponsel, laptop, kamera, televisi, smartwatch, dan konsol game. Barang harus milik Anda sendiri, dapat dinyalakan, dan bebas dari kunci akun.'],
            ['Apa saja syarat menitipkan barang?', 'KTP asli yang masih berlaku, barang beserta kelengkapannya, dan akun anggota yang sudah terverifikasi bila Anda mengajukan lewat situs.'],
            ['Berapa lama jatuh tempo?', 'Tenor {tenor} hari sejak tanggal akad. Sebelum jatuh tempo, Anda dapat memperpanjang sampai {maks_perpanjangan} kali berturut-turut.'],
            ['Bagaimana dengan legalitas linygadai?', 'Informasi perizinan resmi ditampilkan di bagian Tentang Kami setelah diverifikasi dan dilengkapi pengelola. Anda berhak meminta penjelasan dan dokumen perizinan kepada petugas di cabang mana pun.'],
            ['Bagaimana cara memperpanjang?', 'Datang ke cabang sebelum jatuh tempo membawa nota gadai dan KTP, lalu bayar sewa modal dan biaya administrasi periode berjalan. Pokok pinjaman tidak berubah dan jatuh tempo bergeser {tenor} hari.'],
            ['Bolehkah orang lain menebus barang saya?', 'Boleh, dengan surat kuasa bermaterai. Penebus membawa nota gadai asli, KTP asli penggadai, dan KTP asli penebus.'],
            ['Di mana lokasi cabangnya?', 'Kami memiliki Cabang A, Cabang B, dan kantor pusat. Alamat serta jam layanan ada di bagian Tentang Kami.'],
            ['Bagaimana mengetahui perkiraan taksiran?', 'Gunakan simulasi di halaman ini untuk memperkirakan pinjaman dan biaya. Nilai taksiran pasti hanya ditetapkan analis setelah barang diperiksa langsung.'],
            ['Apakah barang saya aman?', 'Barang diberi nomor segel, disimpan di cabang, dan tidak digunakan selama masa titip. Barang hanya keluar saat ditebus dengan nota asli dan identitas yang cocok.'],
        ];
        $info = [
            ['Apa itu sewa modal?', 'Sewa modal adalah biaya untuk menggunakan dana pinjaman selama masa titip. Besarnya dihitung dari pokok pinjaman dan lama titip, dan tercantum di nota.'],
            ['Apa beda perpanjang, cicil, dan tebus?', 'Perpanjang: bayar sewa modal dan administrasi, pokok tetap, jatuh tempo bergeser. Cicil: bayar sewa modal, administrasi, dan sebagian pokok sehingga pinjaman berkurang. Tebus: lunasi seluruhnya lalu barang diserahkan.'],
            ['Bagaimana bila lewat jatuh tempo?', 'Ada masa tenggang {tenggang} hari kalender; sewa modal tetap berjalan. Jika tidak ada pembayaran sampai masa itu berakhir, kami memberi pemberitahuan lebih dulu, minimal {lelang_hari} hari sebelum barang masuk proses lelang.'],
            ['Apa yang terjadi bila barang dilelang?', 'Hasil lelang dipakai untuk melunasi pokok, sewa modal, dan biaya lelang. Jika masih ada sisa, sisanya menjadi hak Anda dan dikembalikan.'],
            ['Apakah taksiran bisa dinilai ulang?', 'Bisa. Saat perpanjangan, analis dapat menaksir ulang barang. Bila nilainya turun di bawah pinjaman, Anda diminta menyetor sebagian pokok.'],
        ];

        if (Faq::count() === 0) {
            foreach ($faq as $i => [$q, $a]) {
                Faq::create(['kelompok' => 'faq', 'pertanyaan' => $q, 'jawaban' => $a, 'urutan' => $i + 1, 'is_published' => true]);
            }
            foreach ($info as $i => [$q, $a]) {
                Faq::create(['kelompok' => 'informasi', 'pertanyaan' => $q, 'jawaban' => $a, 'urutan' => $i + 1, 'is_published' => true]);
            }
        }

        if (Announcement::count() === 0) {
            Announcement::create([
                'judul' => 'Pengajuan online sudah dibuka',
                'isi' => 'Daftar dan ajukan titip gadai dari akun anggota, lalu bawa barang Anda ke cabang. Antrean di cabang jadi lebih singkat karena data Anda sudah kami terima.',
                'is_published' => true,
            ]);
            Announcement::create([
                'judul' => 'Perpanjang sebelum jatuh tempo',
                'isi' => 'Barang yang diperpanjang atau ditebus tepat waktu tidak masuk proses lelang. Cek tanggal jatuh tempo Anda di nota atau di akun anggota.',
                'is_published' => true,
            ]);
        }
    }
}
