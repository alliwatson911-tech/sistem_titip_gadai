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

        foreach ($konten as $kunci => $nilai) {
            DB::table('site_contents')->updateOrInsert(
                ['branch_id' => null, 'kunci' => $kunci],
                ['nilai' => is_array($nilai) ? json_encode($nilai, JSON_UNESCAPED_UNICODE) : $nilai, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        $faq = [
            ['Barang apa saja yang bisa dititipkan di PERSIDEN HP Titip Gadai?', 'Kami menerima barang elektronik dan surat berharga: Laptop, HP (Smartphone), Tab (Tablet), Kamera, BPKB Mobil, BPKB Motor, Sertifikat Rumah & Tanah (SHM/SHGB), serta Barang & Surat Berharga lainnya.'],
            ['Apakah pada titip BPKB kendaraan (mobil/motor) fisik kendaraan ikut ditahan?', 'Tidak. Pada penitipan BPKB Mobil dan Motor, fisik kendaraan TETAP BISA DIGUNAKAN untuk mobilitas dan usaha Anda. Yang disimpan di kantor cabang kami hanyalah buku BPKB asli dan faktur pembelian, setelah dilakukan cek fisik kesesuaian nomor rangka dan nomor mesin.'],
            ['Apakah barang elektronik dan dokumen BPKB atau Sertifikat aman disimpan di cabang?', 'Sangat aman. Seluruh barang elektronik disimpan dalam tempat penyimpanan khusus cabang dan disegel stiker resmi dengan paraf penitip. Dokumen BPKB dan Sertifikat disimpan di tempat penyimpanan aman cabang dengan pengawasan ketat.'],
            ['Bagaimana proses penitipan Sertifikat Rumah atau Tanah?', 'Penitipan sertifikat memerlukan survei lokasi objek properti dan verifikasi keabsahan dokumen. Properti tetap ditempati atau dikelola pemilik seperti biasa tanpa pengosongan.'],
            ['Bagaimana cara pengambilan barang atau dokumen setelah pelunasan?', 'Setelah seluruh nilai pokok dan jasa penitipan dilunasi di loket teller cabang, petugas menyerahkan kembali fisik barang/dokumen asli seketika di hadapan Anda dengan menunjukkan fisik Nota Titip Gadai dan e-KTP asli.'],
            ['Apa syarat menitipkan smartphone atau laptop?', 'Unit berfungsi normal (layar, kamera, audio, baterai), membawa charger adaptor asli, dan wajib bebas dari seluruh kunci akun (iCloud, Apple ID, Google Account, Mi/Samsung Account, sandi layar) saat proses pemeriksaan di loket.'],
            ['Berapa lama jangka waktu titip gadai (tenor)?', 'Pilihan durasi 1 minggu (7 hari) atau 2 minggu (14 hari). Anda dapat memperpanjang masa simpan dengan melunasi jasa penitipan periode berjalan di loket cabang sebelum jatuh tempo.'],
            ['Bolehkah orang lain menebus jaminan atau mengambil dokumen saya?', 'Pengambilan barang diutamakan langsung oleh penitip yang bersangkutan dengan membawa e-KTP asli dan Nota Titip Gadai fisik asli demi keamanan barang Anda.'],
            ['Bagaimana cara mengetahui perkiraan nilai yang diterima?', 'Anda dapat memanfaatkan kalkulator Simulasi Jasa Penitipan di website ini untuk menghitung taksiran nilai yang diterima dan rincian jasa penitipan sesuai pilihan barang dan durasi.'],
        ];
        $info = [
            ['Apa itu jasa penitipan?', 'Jasa penitipan adalah imbal jasa atas pemeliharaan dan pengamanan barang berharga selama masa penitipan di loket cabang PERSIDEN HP. Dihitung secara transparan dan jelas di muka.'],
            ['Apa beda perpanjang, cicil sebagian pokok, dan tebus lunas?', 'Perpanjang: membayar jasa penitipan periode berjalan agar masa simpan diperpanjang. Cicil: membayar sebagian nilai pokok untuk memperkecil beban jasa penitipan berikutnya. Tebus: melunasi sisa nilai yang diterima dan jasa penitipan untuk mengambil kembali barang titipan Anda.'],
            ['Bagaimana jika mendekati atau melewati tanggal jatuh tempo?', 'Petugas kami akan memberikan informasi pengingat ramah sebelum batas akhir, dan Anda dapat segera melakukan perpanjangan atau pelunasan di loket cabang terdekat.'],
            ['Apakah nilai taksiran bisa dinilai kembali saat perpanjangan?', 'Petugas penaksir kami dapat melakukan penyesuaian objektif secara transparan bila terdapat perubahan kondisi unit atau kesepakatan baru di loket cabang.'],
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

        $pengumuman = [
            [
                'judul' => 'Layanan Titip Gadai Terpercaya Sejak 2005',
                'isi' => 'PERSIDEN HP melayani masyarakat Kota Makassar dan Kabupaten Gowa dengan komitmen Aman, Cepat, dan Terpercaya di 7 jaringan loket cabang strategis.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Perpanjang Sebelum Jatuh Tempo',
                'isi' => 'Barang yang diperpanjang atau ditebus tepat waktu tetap tersimpan aman. Cek tanggal jatuh tempo Anda di nota fisik atau di portal digital penitip.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Pantau Transaksi di Portal Penitip',
                'isi' => 'Masuk ke portal penitip menggunakan nomor WhatsApp dan kata sandi untuk melihat detail transaksi, rincian jasa penitipan, dan tanggal jatuh tempo.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Waspada Penipuan Mengatasnamakan PERSIDEN HP',
                'isi' => 'Petugas PERSIDEN HP tidak pernah meminta transfer ke rekening pribadi. Pembayaran transaksi hanya sah dilakukan langsung melalui teller di loket resmi cabang.',
                'branch_id' => null,
            ],
            [
                'judul' => 'Jam Layanan Cabang 08.00 - 22.00 WITA',
                'isi' => 'Seluruh 7 cabang PERSIDEN HP buka setiap hari Senin s.d. Minggu mulai pukul 08.00 hingga 22.00 WITA untuk kenyamanan konsultasi dan transaksi Anda.',
                'branch_id' => null,
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
