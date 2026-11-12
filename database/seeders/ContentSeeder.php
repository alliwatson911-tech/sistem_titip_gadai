<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Branch;
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
            'sambutan' => 'Selamat datang di linygadai. Kami membantu Anda mendapatkan dana tunai dengan menjaminkan laptop, smartphone, BPKB kendaraan bermotor, sertifikat properti, dan barang/surat berharga resmi. Sejak awal kami jelaskan semuanya: berapa pinjaman yang bisa Anda terima, berapa biayanya, kapan jatuh tempo, dan bagaimana cara menebus jaminan Anda kembali.',
            'ringkasan' => 'linygadai melayani titip gadai multi-agunan (elektronik, BPKB mobil & motor, sertifikat tanah & rumah, dan surat berharga) lewat dua cabang resmi di Makassar (Cabang A dan Cabang B) yang dikoordinasikan kantor pusat. Setiap agunan diperiksa, ditaksir oleh analis bersertifikat, diamankan sesuai standar legalitas dan khasanah tahan api, serta dicatat di sistem terintegrasi. Anggota dapat mengajukan lewat situs ini, memantau status jatuh tempo, dan mencicil atau menebus dengan mudah.',
            'standar_layanan' => [
                ['judul' => 'Taksiran yang dijelaskan', 'isi' => 'Analis menyampaikan dasar penilaian barang atau dokumen legal Anda sebelum Anda memutuskan setuju atau tidak.'],
                ['judul' => 'Biaya dirinci dari awal', 'isi' => 'Pokok pinjaman, sewa modal, dan biaya administrasi ditulis terpisah di nota. Tidak ada potongan tersembunyi yang muncul belakangan.'],
                ['judul' => 'Agunan aman & unit kendaraan tetap dipakai', 'isi' => 'Barang elektronik disimpan bersegel di brankas. Pada gadai BPKB kendaraan dan sertifikat properti, hanya dokumen sah yang disimpan di khasanah kantor, unit fisik tetap digunakan nasabah.'],
                ['judul' => 'Identitas & legalitas diperiksa', 'isi' => 'Kami memeriksa KTP, keabsahan kepemilikan, dan validitas dokumen jaminan demi melindungi hak kepemilikan nasabah.'],
                ['judul' => 'Jatuh tempo yang jelas', 'isi' => 'Tenor {tenor} hari dan dapat diperpanjang. Tanggal jatuh tempo tercantum di nota fisik dan di akun anggota secara real-time.'],
                ['judul' => 'Tiga pilihan bayar fleksibel', 'isi' => 'Perpanjang, cicil sebagian pokok, atau tebus lunas. Anda memilih sesuai kesiapan dana, dan setiap pembayaran diterbitkan kwitansi resmi.'],
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
            ['Jaminan apa saja yang bisa digadai di linygadai?', 'Kami menerima 5 kelompok agunan: (1) Gadai Emas & Perhiasan (batangan Antam/UBS & perhiasan), (2) Gadai Elektronik (Laptop, Smartphone, Tablet, Kamera, TV, Konsol), (3) Gadai BPKB Kendaraan (Mobil & Motor), (4) Gadai Sertifikat Properti (Rumah & Tanah SHM/SHGB), serta (5) Gadai Barang & Surat Berharga Lainnya.'],
            ['Apakah pada Gadai BPKB kendaraan (mobil/motor) fisik kendaraan ikut ditahan?', 'Tidak. Pada pinjaman Gadai BPKB Mobil dan Motor, fisik kendaraan TETAP BISA DIGUNAKAN untuk mendukung mobilitas dan usaha Anda. Yang disimpan di khasanah kantor cabang kami hanyalah buku BPKB asli dan faktur pembelian, setelah dilakukan cek fisik kesesuaian nomor rangka dan nomor mesin.'],
            ['Apakah dokumen asli BPKB atau Sertifikat aman disimpan di kantor cabang?', 'Sangat aman. Seluruh dokumen kepemilikan berharga (BPKB, STNK jika dititipkan, Sertifikat Hak Milik/SHGB) disimpan di dalam khasanah brankas tahan api (fireproof vault) dengan akses ganda bersandi dan pengawasan CCTV 24 jam.'],
            ['Bagaimana proses gadai Sertifikat Rumah atau Tanah?', 'Pengajuan sertifikat memerlukan survei on-site lokasi objek properti oleh tim penaksir dan verifikasi keabsahan warkah di Kantor Pertanahan (BPN). Properti tetap ditempati atau dikelola pemilik seperti biasa tanpa pengosongan.'],
            ['Bagaimana cara pengambilan dan pengembalian dokumen jaminan setelah pelunasan?', 'Setelah seluruh pinjaman pokok dan sewa modal dilunasi di loket teller, teller akan mencetak Berita Acara Serah Terima (BAST) pengembalian dokumen. Dokumen jaminan asli (BPKB/Sertifikat/Surat Berharga) diserahkan seketika di hadapan Anda dan tercatat lengkap pada audit trail sistem linygadai.'],
            ['Apa syarat menggadaikan emas, smartphone, atau laptop?', 'Untuk emas/perhiasan: membawa fisik emas dan KTP asli untuk uji karat & timbangan digital (nota toko diutamakan). Untuk elektronik: unit berfungsi normal, layar bekerja baik, membawa charger asli, dan wajib bebas dari kunci akun (iCloud, Google, pola/sandi).'],
            ['Berapa lama jangka waktu titip gadai (tenor)?', 'Tenor {tenor} hari sejak tanggal akad. Sebelum jatuh tempo, Anda dapat memperpanjang sampai {maks_perpanjangan} kali berturut-turut cukup dengan melunasi sewa modal dan biaya administrasi.'],
            ['Bolehkah orang lain menebus jaminan atau mengambil dokumen saya?', 'Boleh, dengan syarat membawa Surat Kuasa bermeterai Rp 10.000 asli bertandatangan pemilik sah, fisik Nota Gadai asli, KTP asli pemilik jaminan, serta KTP asli penerima kuasa.'],
            ['Bagaimana cara mengetahui perkiraan nilai pinjaman?', 'Anda dapat memanfaatkan kalkulator Simulasi Gadai interaktif di website ini untuk menghitung taksiran, biaya sewa modal per hari, dan total pelunasan sesuai parameter jenis agunan yang dipilih.'],
        ];
        $info = [
            ['Apa itu sewa modal?', 'Sewa modal adalah imbal jasa legal atas pemanfaatan fasilitas pinjaman selama masa titip. Di linygadai, sewa modal dihitung harian secara transparan tanpa denda siluman.'],
            ['Apa beda perpanjang, cicil pokok, dan tebus?', 'Perpanjang: membayar sewa modal & administrasi, pokok tetap, jatuh tempo diperpanjang. Cicil: membayar sewa modal ditambah sebagian pokok pinjaman untuk memperingan tanggungan. Tebus: melunasi sisa pinjaman secara utuh dan mengambil kembali barang/dokumen jaminan.'],
            ['Bagaimana jika terlambat melewati tanggal jatuh tempo?', 'Ada masa tenggang {tenggang} hari kalender; sewa modal tetap berjalan. Tim kami akan mengirimkan pengingat ramah sebelum batas akhir, dan pemberitahuan tertulis sekurang-kurangnya {lelang_hari} hari sebelum langkah tindak lanjut sesuai kesepakatan akad.'],
            ['Apakah nilai taksiran bisa ditinjau kembali?', 'Ya. Saat pengajuan perpanjangan atau perubahan kondisi pasar, analis bersertifikasi dapat melakukan penaksiran ulang secara objektif dan transparan.'],
        ];

        foreach ($faq as $i => [$q, $a]) {
            Faq::updateOrCreate(
                ['kelompok' => 'faq', 'pertanyaan' => $q],
                ['jawaban' => $a, 'urutan' => $i + 1, 'is_published' => true]
            );
        }
        foreach ($info as $i => [$q, $a]) {
            Faq::updateOrCreate(
                ['kelompok' => 'informasi', 'pertanyaan' => $q],
                ['jawaban' => $a, 'urutan' => $i + 1, 'is_published' => true]
            );
        }

        $cabangA = Branch::where('kode', 'A')->value('id');
        $cabangB = Branch::where('kode', 'B')->value('id');

        $pengumuman = [
            [
                'judul' => 'Pengajuan online sudah dibuka',
                'isi' => 'Daftar dan ajukan titip gadai dari akun anggota, lalu bawa barang atau dokumen Anda ke cabang. Antrean di cabang jadi lebih singkat karena data Anda sudah kami terima.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Perpanjang sebelum jatuh tempo',
                'isi' => 'Barang yang diperpanjang atau ditebus tepat waktu tidak masuk proses lelang. Cek tanggal jatuh tempo Anda di nota fisik atau di akun anggota.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Cek tanggal jatuh tempo di akun Anda',
                'isi' => 'Masuk ke akun anggota untuk melihat jatuh tempo, tagihan perpanjang, dan riwayat pembayaran. Kami juga mengirim email pengingat sebelum jatuh tempo.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Waspada penipuan',
                'isi' => 'Petugas linygadai tidak pernah meminta kata sandi, kode verifikasi, atau transfer ke rekening pribadi. Pembayaran transaksi hanya sah melalui teller resmi cabang.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Layanan Gadai Emas & Perhiasan Resmi',
                'isi' => 'Kini linygadai melayani titip gadai emas batangan (Antam, UBS) dan perhiasan emas dengan uji kadar karat akurat, timbangan digital tersertifikasi, dan perlindungan brankas tahan api.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Antrean lebih singkat di pagi hari',
                'isi' => 'Cabang Veteran Selatan melayani penaksiran paling cepat pukul 08.30–10.00 WITA. Bawa KTP asli dan kelengkapan agunan untuk mempercepat proses pencairan dana Anda.',
                'branch_id' => $cabangA,
            ],
            [
                'judul' => 'Loket penebusan dan pencairan dipisah',
                'isi' => 'Cabang Perintis Kemerdekaan kini memisahkan loket penebusan dan pencairan agar alur pelayanan nasabah lebih tertib, cepat, dan nyaman tanpa antre panjang.',
                'branch_id' => $cabangB,
            ],
        ];

        foreach ($pengumuman as $p) {
            Announcement::firstOrCreate(
                ['judul' => $p['judul']],
                [
                    'isi' => $p['isi'],
                    'branch_id' => $p['branch_id'],
                    'is_published' => true,
                ]
            );
        }
    }
}
