<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Master 7 Jenis Jaminan Resmi linygadai + Agunan Elektronik Pendukung.
 * Angka LTV, sewa modal, biaya administrasi, dan limit plafon adalah ASUMSI kebijakan
 * yang dapat dikonfigurasi langsung dari sistem manajemen tanpa mengubah kode.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            // Emas & Perhiasan
            [
                'nama' => 'Gadai Emas & Perhiasan',
                'slug' => 'gadai-emas-perhiasan',
                'kategori_barang' => 'emas',
                'kelompok_agunan' => 'emas',
                'deskripsi' => 'Terima jaminan emas batangan (Antam, UBS, Lotus Archi) dan perhiasan emas (kalung, cincin, gelang, anting) dengan uji kadar karat akurat, timbangan digital tersertifikasi, dan sewa modal ringan.',
                'pinjaman_min' => 500_000,
                'pinjaman_max' => 250_000_000,
                'ltv_max_bp' => 8800, // 88%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 10, // 0,10%/hari (~3%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 100, // 1%
                'admin_min' => 10_000,
                'admin_max' => 250_000,
                'badge_label' => 'Plafon s.d. 88%',
                'dokumen_wajib' => [
                    'KTP asli pemohon / pemilik',
                    'Nota pembelian toko emas / sertifikat Antam/UBS asli (jika ada)',
                ],
                'atribut_khusus' => [
                    ['name' => 'jenis_emas', 'label' => 'Bentuk & Jenis Emas', 'type' => 'select', 'options' => ['Logam Mulia Batangan (Antam/UBS/Galeri 24)', 'Perhiasan Cincin Emas', 'Perhiasan Kalung Emas', 'Perhiasan Gelang Emas', 'Perhiasan Anting / Liontin', 'Koin Dinar / Emas Koleksi'], 'required' => true],
                    ['name' => 'kadar_karat', 'label' => 'Perkiraan Kadar Karat', 'type' => 'select', 'options' => ['24 Karat / 99.9% (Fine Gold)', '22 Karat / 91.6%', '20 Karat / 83.3%', '18 Karat / 75.0% (Emas Tua)', '16 Karat / 66.7%', '9 Karat - 14 Karat (Emas Muda)', 'Belum Tahu (Dicek Analis Cabang)'], 'required' => true],
                    ['name' => 'berat_gram', 'label' => 'Perkiraan Berat Bersih (Gram)', 'type' => 'number', 'placeholder' => 'Contoh: 10.5 (gram)', 'required' => true],
                    ['name' => 'sertifikat_kelengkapan', 'label' => 'Kelengkapan / Dokumen', 'type' => 'select', 'options' => ['Ada Sertifikat Resmi (Antam CertiEye / UBS)', 'Ada Nota Pembelian Toko Emas Asli', 'Tanpa Surat Toko / Surat Hilang (Uji Karat Cabang)'], 'required' => true],
                    ['name' => 'kondisi', 'label' => 'Kondisi Fisik Emas', 'type' => 'textarea', 'placeholder' => 'Mulus tanpa cacat, batu permata utuh, atau ada sambungan/patah', 'required' => false],
                ],
                'syarat_ketentuan' => [
                    'Emas asli milik sendiri secara sah dan tidak dalam sengketa.',
                    'Bersedia dilakukan pengujian keaslian kadar karat dan penimbangan digital presisi di hadapan nasabah.',
                    'Emas batangan berkemasan CertiEye tidak dirusak segel kemasannya.',
                    'Disimpan dalam brankas besi khasanah tahan api berlapis ganda (fireproof safe vault) dengan nomor segel brankas khusus.',
                ],
                'proses_taksiran' => 'Pemeriksaan fisik visual, penimbangan digital tersertifikasi metrologi, pengujian berat jenis / densitas dan uji karatase (uji gores batu uji atau alat ukur karat presisi), serta validasi keaslian sertifikat cetak/aplikasi.',
                'ketentuan_penyimpanan' => 'Emas dimasukkan ke kantong beludru pelindung anti-gores, dimasukkan ke boks bersegel tamper-evident bernomor seri unik di depan nasabah, lalu disimpan di safe deposit box brankas khasanah terpusat berstandar keamanan tinggi.',
            ],

            // 1) Laptop
            [
                'nama' => 'Gadai Laptop',
                'slug' => 'gadai-laptop',
                'kategori_barang' => 'laptop',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Laptop, MacBook, dan notebook lengkap dengan adaptor pengisi daya asli. Taksiran dinilai dari spesifikasi prosesor, RAM, kesehatan baterai, dan kondisi fisik.',
                'pinjaman_min' => 500_000,
                'pinjaman_max' => 30_000_000,
                'ltv_max_bp' => 8000, // 80%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15, // 0,15%/hari
                'blok_hari' => 15,
                'admin_bp' => 100, // 1%
                'admin_min' => 10_000,
                'admin_max' => 150_000,
                'badge_label' => 'Plafon s.d. 80%',
                'dokumen_wajib' => [
                    'KTP asli pemilik/pemohon',
                    'Nota pembelian / kartu garansi resmi (jika ada)',
                ],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek Laptop', 'type' => 'text', 'placeholder' => 'Asus, Lenovo, Apple, Dell, HP, Acer', 'required' => true],
                    ['name' => 'model', 'label' => 'Tipe & Seri Model', 'type' => 'text', 'placeholder' => 'MacBook Air M2, ThinkPad T14, ROG Zephyrus', 'required' => true],
                    ['name' => 'spesifikasi', 'label' => 'Spesifikasi Utama (CPU/RAM/SSD)', 'type' => 'text', 'placeholder' => 'Core i7 Gen 12, RAM 16GB, SSD 512GB', 'required' => true],
                    ['name' => 'tahun', 'label' => 'Tahun Pembelian / Rilis', 'type' => 'number', 'placeholder' => 'Contoh: 2023', 'required' => false],
                    ['name' => 'imei_serial', 'label' => 'Nomor Seri (Serial Number)', 'type' => 'text', 'placeholder' => 'SN di bawah bodi laptop / sistem', 'required' => true],
                    ['name' => 'kelengkapan', 'label' => 'Kelengkapan Fisik', 'type' => 'text', 'placeholder' => 'Adaptor charger asli, tas laptop, dus bawaan', 'required' => true],
                    ['name' => 'kondisi', 'label' => 'Kondisi Layar & Baterai', 'type' => 'textarea', 'placeholder' => 'Layar normal tanpa dead pixel, baterai tahan 3-4 jam, bodi 95%', 'required' => false],
                ],
                'syarat_ketentuan' => [
                    'Unit dapat dinyalakan normal dan sistem operasi berfungsi.',
                    'Wajib membawa charger (adaptor) asli atau kompatibel berkualitas.',
                    'Bebas dari password BIOS, kunci firmware Apple ID / iCloud, atau BitLocker.',
                    'Barang disimpan di brankas bersegel resmi tanpa pernah dinyalakan/dipakai oleh staf.',
                ],
                'proses_taksiran' => 'Pemeriksaan fungsi layar sentuh/LCD, keyboard, trackpad, port charging/USB, kesehatan baterai, serta keaslian adaptor pengisi daya.',
                'ketentuan_penyimpanan' => 'Barang fisik disegel stiker bernomor seri unik di depan nasabah, lalu disimpan dalam brankas besi tahan api di kantor cabang terdaftar.',
            ],

            // 2) Smartphone (handphone dan tablet)
            [
                'nama' => 'Gadai Smartphone',
                'slug' => 'gadai-smartphone',
                'kategori_barang' => 'smartphone',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Ponsel pintar dan tablet berbagai merek ternama (iPhone, iPad, Samsung Galaxy, Xiaomi, Oppo, Vivo) dalam kondisi menyala dan terbebas dari kunci akun.',
                'pinjaman_min' => 100_000,
                'pinjaman_max' => 25_000_000,
                'ltv_max_bp' => 8000, // 80%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15, // 0,15%/hari
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 125_000,
                'badge_label' => 'Plafon s.d. 80%',
                'dokumen_wajib' => [
                    'KTP asli pemohon',
                    'Dus / kemasan asli (direkomendasikan untuk taksiran maksimal)',
                ],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek Perangkat', 'type' => 'text', 'placeholder' => 'Apple, Samsung, Xiaomi, Oppo, Vivo', 'required' => true],
                    ['name' => 'model', 'label' => 'Model & Varian', 'type' => 'text', 'placeholder' => 'iPhone 15 Pro, Galaxy S24, iPad Air 5', 'required' => true],
                    ['name' => 'kapasitas', 'label' => 'Kapasitas Memori (ROM/RAM)', 'type' => 'text', 'placeholder' => '128GB / 256GB / 512GB', 'required' => true],
                    ['name' => 'imei_serial', 'label' => 'Nomor IMEI 1 (*#06#)', 'type' => 'text', 'placeholder' => '15 digit IMEI resmi Kemenperin/Bea Cukai', 'required' => true],
                    ['name' => 'status_kunci', 'label' => 'Status Kunci Akun / Layar', 'type' => 'select', 'options' => ['Sudah Logout / Bebas Kunci', 'Akan Dilogout Bersama Petugas di Cabang'], 'required' => true],
                    ['name' => 'kelengkapan', 'label' => 'Kelengkapan', 'type' => 'text', 'placeholder' => 'Unit only / Fullset (dus + kabel)', 'required' => true],
                    ['name' => 'kondisi', 'label' => 'Kondisi Bodi & Fungsi', 'type' => 'textarea', 'placeholder' => 'TrueTone/FaceID/Fingerprint normal, kemulusan bodi 95%', 'required' => false],
                ],
                'syarat_ketentuan' => [
                    'Unit dapat dinyalakan normal, layar tidak pecah atau bergaris berat.',
                    'Sudah logout dari akun iCloud, Google Account, Mi Cloud, Samsung Account, serta kunci PIN/pola layar.',
                    'IMEI terdaftar resmi di basis data Kemenperin/Bea Cukai.',
                    'Unit fisik disimpan dalam kantong anti-statis bersegel di brankas cabang.',
                ],
                'proses_taksiran' => 'Pengecekan nomor IMEI, sensor biometrik (FaceID/Fingerprint), kamera, speaker, mikrofon, battery health, dan kelengkapan.',
                'ketentuan_penyimpanan' => 'Ponsel dimatikan di hadapan nasabah, dimasukkan ke dalam pouch anti-statis bersegel khusus, dan disimpan di brankas terpusat.',
            ],

            // 3) BPKB Mobil
            [
                'nama' => 'Gadai BPKB Mobil',
                'slug' => 'gadai-bpkb-mobil',
                'kategori_barang' => 'bpkb-mobil',
                'kelompok_agunan' => 'kendaraan',
                'deskripsi' => 'Pinjaman dana cepat dengan jaminan BPKB mobil pribadi maupun niaga. Dokumen BPKB disimpan aman di khasanah, dan mobil tetap dapat Anda kendarai sehari-hari.',
                'pinjaman_min' => 10_000_000,
                'pinjaman_max' => 500_000_000,
                'ltv_max_bp' => 8500, // 85%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 8, // 0,08%/hari (~2,4%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 75, // 0,75%
                'admin_min' => 100_000,
                'admin_max' => 1_500_000,
                'badge_label' => 'Plafon s.d. 85%',
                'dokumen_wajib' => [
                    'KTP asli pemohon & pasangan (jika menikah)',
                    'Kartu Keluarga (KK)',
                    'BPKB asli mobil',
                    'STNK asli yang masih berlaku & bukti pajak',
                    'Faktur / kuitansi pembelian asli (jika ada)',
                ],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek & Tipe Mobil', 'type' => 'text', 'placeholder' => 'Toyota Avanza 1.5 G, Honda CR-V Turbo, Mitsubishi Pajero', 'required' => true],
                    ['name' => 'tahun', 'label' => 'Tahun Pembuatan', 'type' => 'number', 'placeholder' => 'Contoh: 2018', 'required' => true],
                    ['name' => 'nomor_polisi', 'label' => 'Nomor Polisi (Plat)', 'type' => 'text', 'placeholder' => 'DD 1234 XX', 'required' => true],
                    ['name' => 'nomor_rangka', 'label' => 'Nomor Rangka (VIN)', 'type' => 'text', 'placeholder' => 'Sesuai STNK dan BPKB', 'required' => true],
                    ['name' => 'nomor_mesin', 'label' => 'Nomor Mesin', 'type' => 'text', 'placeholder' => 'Sesuai STNK dan BPKB', 'required' => true],
                    ['name' => 'nama_pemilik_bpkb', 'label' => 'Atas Nama di BPKB', 'type' => 'text', 'placeholder' => 'Nama pemilik terdaftar di BPKB', 'required' => true],
                    ['name' => 'masa_berlaku_pajak', 'label' => 'Masa Berlaku Pajak STNK', 'type' => 'text', 'placeholder' => 'Bulan/Tahun (contoh: 10/2026)', 'required' => true],
                    ['name' => 'status_kepemilikan', 'label' => 'Status Kepemilikan', 'type' => 'select', 'options' => ['Atas Nama Sendiri', 'Atas Nama Pasangan / Keluarga', 'Belum Balik Nama (Ada Kwitansi Jual Beli)'], 'required' => true],
                ],
                'syarat_ketentuan' => [
                    'Usia kendaraan maksimal 15 tahun terakhir (merek Jepang) atau 10 tahun (merek non-Jepang).',
                    'Membawa mobil dan BPKB asli ke cabang untuk cek fisik gesek nomor rangka dan mesin.',
                    'Pajak STNK aktif atau masa tenggang wajar.',
                    'Dokumen BPKB asli disimpan di brankas khasanah tahan api. Mobil tetap digunakan nasabah.',
                ],
                'proses_taksiran' => 'Cek fisik nomor rangka dan mesin di cabang, verifikasi keabsahan dokumen BPKB/STNK, dan penaksiran nilai pasar acuan kendaraan.',
                'ketentuan_penyimpanan' => 'Dokumen BPKB asli disimpan dalam safe deposit box khasanah tahan api dengan Berita Acara Serah Terima resmi (BAST). Mobil tetap dipakai nasabah.',
            ],

            // 4) BPKB Motor
            [
                'nama' => 'Gadai BPKB Motor',
                'slug' => 'gadai-bpkb-motor',
                'kategori_barang' => 'bpkb-motor',
                'kelompok_agunan' => 'kendaraan',
                'deskripsi' => 'Pinjaman dana tunai jaminan BPKB sepeda motor (matic, bebek, sport) dengan pencairan cepat 15 menit. Dokumen BPKB aman tersimpan, motor tetap Anda pakai.',
                'pinjaman_min' => 1_000_000,
                'pinjaman_max' => 50_000_000,
                'ltv_max_bp' => 8000, // 80%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 10, // 0,10%/hari (~3%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 25_000,
                'admin_max' => 250_000,
                'badge_label' => 'Plafon s.d. 80%',
                'dokumen_wajib' => [
                    'KTP asli pemohon',
                    'Kartu Keluarga (KK)',
                    'BPKB motor asli',
                    'STNK asli yang masih berlaku',
                ],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek & Tipe Motor', 'type' => 'text', 'placeholder' => 'Honda Vario 160, Yamaha NMAX 155, Scoopy, PCX', 'required' => true],
                    ['name' => 'tahun', 'label' => 'Tahun Pembuatan', 'type' => 'number', 'placeholder' => 'Contoh: 2020', 'required' => true],
                    ['name' => 'nomor_polisi', 'label' => 'Nomor Polisi (Plat)', 'type' => 'text', 'placeholder' => 'DD 5678 YY', 'required' => true],
                    ['name' => 'nomor_rangka', 'label' => 'Nomor Rangka', 'type' => 'text', 'placeholder' => 'Sesuai STNK', 'required' => true],
                    ['name' => 'nomor_mesin', 'label' => 'Nomor Mesin', 'type' => 'text', 'placeholder' => 'Sesuai STNK', 'required' => true],
                    ['name' => 'nama_pemilik_bpkb', 'label' => 'Atas Nama di BPKB', 'type' => 'text', 'placeholder' => 'Nama pemilik di BPKB', 'required' => true],
                    ['name' => 'masa_berlaku_pajak', 'label' => 'Masa Berlaku Pajak', 'type' => 'text', 'placeholder' => 'Contoh: 08/2026', 'required' => true],
                ],
                'syarat_ketentuan' => [
                    'Usia sepeda motor maksimal 10 tahun terakhir.',
                    'Membawa motor dan BPKB asli ke cabang untuk cek fisik gesek nomor rangka dan mesin.',
                    'Motor tidak dalam status sengketa atau pemblokiran kepolisian.',
                    'Dokumen BPKB disimpan di brankas dokumen; motor tetap digunakan nasabah.',
                ],
                'proses_taksiran' => 'Pemeriksaan fisik motor dan dokumen BPKB/STNK, cek keabsahan identitas kendaraan, penentuan nilai taksiran pasar terupdate.',
                'ketentuan_penyimpanan' => 'BPKB asli disimpan dalam amplop bersegel anti-rusak di dalam lemari besi dokumen kantor cabang berkeamanan tinggi.',
            ],

            // 5) Sertifikat Rumah
            [
                'nama' => 'Gadai Sertifikat Rumah',
                'slug' => 'gadai-sertifikat-rumah',
                'kategori_barang' => 'sertifikat-rumah',
                'kelompok_agunan' => 'properti',
                'deskripsi' => 'Fasilitas pinjaman bernilai besar dengan agunan Sertifikat Hak Milik (SHM) atau SHGB rumah tinggal. Rumah tetap ditempati keluarga dengan aman dan tenang.',
                'pinjaman_min' => 25_000_000,
                'pinjaman_max' => 1_000_000_000,
                'ltv_max_bp' => 7000, // 70%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 5, // 0,05%/hari (~1,5%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 50, // 0,5%
                'admin_min' => 250_000,
                'admin_max' => 3_000_000,
                'badge_label' => 'Plafon s.d. 70%',
                'dokumen_wajib' => [
                    'KTP pemohon & pasangan / ahli waris',
                    'Kartu Keluarga (KK) & Buku Nikah',
                    'Sertifikat Asli (SHM / SHGB)',
                    'SPPT PBB & Bukti Lunas PBB tahun berjalan',
                    'IMB / PBG (Persetujuan Bangunan Gedung)',
                ],
                'atribut_khusus' => [
                    ['name' => 'jenis_hak', 'label' => 'Jenis Hak Sertifikat', 'type' => 'select', 'options' => ['SHM (Sertifikat Hak Milik)', 'SHGB (Sertifikat Hak Guna Bangunan)'], 'required' => true],
                    ['name' => 'nomor_sertifikat', 'label' => 'Nomor Sertifikat Rumah', 'type' => 'text', 'placeholder' => 'No. SHM / SHGB tertera pada cover', 'required' => true],
                    ['name' => 'atas_nama', 'label' => 'Nama Pemegang Hak', 'type' => 'text', 'placeholder' => 'Nama lengkap sesuai buku tanah', 'required' => true],
                    ['name' => 'luas_tanah', 'label' => 'Luas Tanah (m²)', 'type' => 'number', 'placeholder' => 'Contoh: 120', 'required' => true],
                    ['name' => 'luas_bangunan', 'label' => 'Luas Bangunan (m²)', 'type' => 'number', 'placeholder' => 'Contoh: 90', 'required' => true],
                    ['name' => 'alamat_objek', 'label' => 'Alamat Lengkap Objek Rumah', 'type' => 'textarea', 'placeholder' => 'Jalan, RT/RW, Kelurahan, Kecamatan, Kota Makassar', 'required' => true],
                    ['name' => 'nop_pbb', 'label' => 'Nomor Objek Pajak (NOP PBB)', 'type' => 'text', 'placeholder' => 'NOP 18 digit pada SPPT PBB', 'required' => true],
                    ['name' => 'status_penghunian', 'label' => 'Status Penghunian', 'type' => 'select', 'options' => ['Dihuni Sendiri', 'Disewakan / Kontrak', 'Kosong'], 'required' => true],
                ],
                'syarat_ketentuan' => [
                    'Sertifikat atas nama pemohon sendiri atau keluarga inti dengan surat persetujuan tertulis.',
                    'Akses jalan dapat dilalui kendaraan roda empat dan lokasi bebas sengketa batas.',
                    'PBB lunas tahun berjalan dan memiliki izin mendirikan bangunan (IMB/PBG).',
                    'Sertifikat asli disimpan di khasanah safe deposit box. Nasabah tetap menempati rumah dengan tenang.',
                ],
                'proses_taksiran' => 'Verifikasi keabsahan sertifikat ke kantor BPN, survei fisik lokasi objek oleh analis lapangan, verifikasi NJOP dan nilai pasar wajar.',
                'ketentuan_penyimpanan' => 'Sertifikat asli disimpan di lemari besi khasanah tahan api berkunci ganda (dual key control) dan diasuransikan terhadap risiko kebakaran.',
            ],

            // 6) Sertifikat Tanah
            [
                'nama' => 'Gadai Sertifikat Tanah',
                'slug' => 'gadai-sertifikat-tanah',
                'kategori_barang' => 'sertifikat-tanah',
                'kelompok_agunan' => 'properti',
                'deskripsi' => 'Pinjaman dana produktif dan modal usaha dengan agunan Sertifikat Tanah (kavling pekarangan, tanah komersil, atau lahan produktif). Lahan tetap Anda kelola.',
                'pinjaman_min' => 20_000_000,
                'pinjaman_max' => 750_000_000,
                'ltv_max_bp' => 6500, // 65%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 5, // 0,05%/hari (~1,5%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 50,
                'admin_min' => 200_000,
                'admin_max' => 2_500_000,
                'badge_label' => 'Plafon s.d. 65%',
                'dokumen_wajib' => [
                    'KTP & KK pemilik sertifikat',
                    'Sertifikat Tanah Asli (SHM / SHGB)',
                    'SPPT PBB tahun berjalan & bukti setor lunas',
                    'Surat persetujuan pasangan / keluarga inti',
                ],
                'atribut_khusus' => [
                    ['name' => 'jenis_hak', 'label' => 'Jenis Hak Atas Tanah', 'type' => 'select', 'options' => ['SHM (Sertifikat Hak Milik)', 'SHGB (Sertifikat Hak Guna Bangunan)'], 'required' => true],
                    ['name' => 'nomor_sertifikat', 'label' => 'Nomor Sertifikat Tanah', 'type' => 'text', 'placeholder' => 'No. Hak Milik / HGB', 'required' => true],
                    ['name' => 'atas_nama', 'label' => 'Nama Pemegang Hak', 'type' => 'text', 'placeholder' => 'Nama di sertifikat tanah', 'required' => true],
                    ['name' => 'luas_tanah', 'label' => 'Luas Tanah (m²)', 'type' => 'number', 'placeholder' => 'Contoh: 300', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan / Zona Lahan', 'type' => 'select', 'options' => ['Pekarangan / Perumahan', 'Komersial / Pinggir Jalan Utama', 'Perkebunan / Pertanian Produktif'], 'required' => true],
                    ['name' => 'alamat_objek', 'label' => 'Lokasi / Alamat Lahan', 'type' => 'textarea', 'placeholder' => 'Lokasi tanah, batas patok, kelurahan & kecamatan', 'required' => true],
                    ['name' => 'nop_pbb', 'label' => 'Nomor Objek Pajak (NOP PBB)', 'type' => 'text', 'placeholder' => 'NOP SPPT PBB', 'required' => true],
                ],
                'syarat_ketentuan' => [
                    'Tanah berstatus kepemilikan jelas, memiliki patok batas resmi BPN, dan bebas sengketa waris atau pihak ketiga.',
                    'Memiliki akses jalan memadai.',
                    'Bebas dari pembebanan hak tanggungan bank/lembaga lain.',
                    'Sertifikat tanah asli disimpan di safe deposit khasanah cabang dengan tanda terima resmi bernomor.',
                ],
                'proses_taksiran' => 'Pengecekan keabsahan warkah di BPN, pengukuran batas tanah lapangan, dan analisis nilai pasar per meter persegi serta NJOP terkini.',
                'ketentuan_penyimpanan' => 'Sertifikat tanah asli disimpan di ruang khasanah dengan pengamanan 24 jam dan proteksi kelembaban serta kebakaran.',
            ],

            // 7) Barang dan Surat Berharga Lainnya
            [
                'nama' => 'Gadai Barang dan Surat Berharga Lainnya',
                'slug' => 'gadai-barang-dan-surat-berharga-lainnya',
                'kategori_barang' => 'lainnya',
                'kelompok_agunan' => 'berharga',
                'deskripsi' => 'Layanan titip gadai fleksibel untuk logam mulia/emas batangan (Antam/UBS), perhiasan emas berkwitansi, instrumen deposito/obligasi, atau perlengkapan bernilai tinggi seperti kamera profesional & drone studio.',
                'pinjaman_min' => 500_000,
                'pinjaman_max' => 100_000_000,
                'ltv_max_bp' => 8500, // 85%
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 12, // 0,12%/hari (~3,6%/bulan)
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 15_000,
                'admin_max' => 500_000,
                'badge_label' => 'Plafon s.d. 85%',
                'dokumen_wajib' => [
                    'KTP asli pemohon',
                    'Nota pembelian / sertifikat keaslian resmi (Antam/UBS/Toko Emas)',
                    'Bilyet asli / dokumen bukti kepemilikan sah',
                ],
                'atribut_khusus' => [
                    ['name' => 'jenis_aset', 'label' => 'Jenis Barang / Surat Berharga', 'type' => 'select', 'options' => ['Logam Mulia / Emas Batangan (Antam/UBS)', 'Perhiasan Emas Berkwitansi', 'Kamera Profesional & Lensa', 'Drone & Alat Studio Bernilai Tinggi', 'Bilyet Deposito / Surat Berharga'], 'required' => true],
                    ['name' => 'deskripsi_aset', 'label' => 'Rincian / Spesifikasi Aset', 'type' => 'textarea', 'placeholder' => 'Contoh: Emas Batangan Antam CertiEye 10 gram kadar 99.99%, atau Kamera Sony A7 IV + Lensa FE 24-70mm GM', 'required' => true],
                    ['name' => 'berat_atau_nominal', 'label' => 'Berat (gram) / Nominal Pokok (Rp)', 'type' => 'text', 'placeholder' => 'Contoh: 10 gram / Rp 25.000.000', 'required' => true],
                    ['name' => 'nomor_sertifikat', 'label' => 'Nomor Seri / Sertifikat Keaslian', 'type' => 'text', 'placeholder' => 'Nomor seri cetak atau nomor sertifikat resmi', 'required' => true],
                    ['name' => 'kelengkapan_dokumen', 'label' => 'Kelengkapan Dokumen & Kemasan', 'type' => 'text', 'placeholder' => 'Sertifikat press utuh, nota toko asli, box bawaan', 'required' => true],
                ],
                'syarat_ketentuan' => [
                    'Emas batangan berkemasan press tidak boleh cacat/rusak segelnya.',
                    'Perhiasan emas wajib memiliki kadar minimum 70% (16K - 24K) dan dilengkapi kwitansi resmi toko.',
                    'Peralatan elektronik profesional (kamera/drone) harus berfungsi normal tanpa kerusakan.',
                    'Barang dan surat berharga disimpan di brankas ganda tahan api bersegel resmi.',
                ],
                'proses_taksiran' => 'Uji keaslian non-destruktif (densimeter / timbangan presisi / pengujian karat), pengecekan harga acuan emas dunia/Antam terkini, atau verifikasi legalitas instrumen.',
                'ketentuan_penyimpanan' => 'Barang berharga dimasukkan ke dalam safety box ganda bersegel dan dicatat dalam register brankas utama.',
            ],

            // Produk elektronik pendukung yang sudah ada (dipertahankan agar kompatibel dengan tes lama)
            [
                'nama' => 'Gadai Kamera',
                'slug' => 'gadai-kamera',
                'kategori_barang' => 'kamera',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Kamera mirrorless dan DSLR beserta lensa dan baterai asli.',
                'pinjaman_min' => 500_000,
                'pinjaman_max' => 25_000_000,
                'ltv_max_bp' => 7500,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 10_000,
                'admin_max' => 125_000,
                'badge_label' => 'Plafon s.d. 75%',
                'dokumen_wajib' => ['KTP asli pemohon'],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek Kamera', 'type' => 'text', 'placeholder' => 'Sony, Canon, Fujifilm, Nikon', 'required' => true],
                    ['name' => 'model', 'label' => 'Tipe Kamera & Lensa', 'type' => 'text', 'placeholder' => 'Sony A7 III + Kit 28-70mm', 'required' => true],
                ],
                'syarat_ketentuan' => ['Sensor bersih tanpa jamur, shutter count wajar, baterai dan charger asli.'],
                'proses_taksiran' => 'Pemeriksaan sensor, lensa, autofokus, dan kondisi fisik.',
                'ketentuan_penyimpanan' => 'Disimpan dalam dry box brankas berpendingin untuk menjaga lensa dari jamur.',
            ],
            [
                'nama' => 'Gadai Konsol Game',
                'slug' => 'gadai-konsol-game',
                'kategori_barang' => 'konsol',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Konsol game dengan kontroler dan kabel bawaan (PlayStation, Nintendo Switch, Xbox).',
                'pinjaman_min' => 300_000,
                'pinjaman_max' => 15_000_000,
                'ltv_max_bp' => 7500,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 100_000,
                'badge_label' => 'Plafon s.d. 75%',
                'dokumen_wajib' => ['KTP asli pemohon'],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek Konsol', 'type' => 'text', 'placeholder' => 'Sony PlayStation, Nintendo, Xbox', 'required' => true],
                    ['name' => 'model', 'label' => 'Model Konsol', 'type' => 'text', 'placeholder' => 'PS5 Slim Disc Edition, Switch OLED', 'required' => true],
                ],
                'syarat_ketentuan' => ['Konsol berfungsi normal, stik kontroler responsif, tidak terkena banned online.'],
                'proses_taksiran' => 'Pengujian menyala, pembacaan disc/cartridge, dan stik kontroler.',
                'ketentuan_penyimpanan' => 'Disimpan di brankas elektronik bersegel.',
            ],
            [
                'nama' => 'Gadai Televisi',
                'slug' => 'gadai-televisi',
                'kategori_barang' => 'televisi',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Televisi layar datar LED/Smart TV dengan remote asli dan kaki/bracket bawaan.',
                'pinjaman_min' => 300_000,
                'pinjaman_max' => 15_000_000,
                'ltv_max_bp' => 7000,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 100_000,
                'badge_label' => 'Plafon s.d. 70%',
                'dokumen_wajib' => ['KTP asli pemohon'],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek TV', 'type' => 'text', 'placeholder' => 'Samsung, LG, Sony, TCL, Xiaomi', 'required' => true],
                    ['name' => 'model', 'label' => 'Ukuran & Model TV', 'type' => 'text', 'placeholder' => '43 Inch 4K Smart TV', 'required' => true],
                ],
                'syarat_ketentuan' => ['Layar utuh tidak retak, panel normal, remote asli berfungsi.'],
                'proses_taksiran' => 'Pemeriksaan panel layar, speaker, port HDMI, dan koneksi internet Smart TV.',
                'ketentuan_penyimpanan' => 'Disimpan dalam boks pelindung empuk di ruang khusus brankas cabang.',
            ],
            [
                'nama' => 'Gadai Smartwatch',
                'slug' => 'gadai-smartwatch',
                'kategori_barang' => 'smartwatch',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'Jam tangan pintar (Apple Watch, Galaxy Watch, Garmin) dengan pengisi daya asli.',
                'pinjaman_min' => 200_000,
                'pinjaman_max' => 15_000_000,
                'ltv_max_bp' => 7000,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 100_000,
                'badge_label' => 'Plafon s.d. 70%',
                'dokumen_wajib' => ['KTP asli pemohon'],
                'atribut_khusus' => [
                    ['name' => 'merek', 'label' => 'Merek Jam', 'type' => 'text', 'placeholder' => 'Apple, Samsung, Garmin', 'required' => true],
                    ['name' => 'model', 'label' => 'Model & Ukuran', 'type' => 'text', 'placeholder' => 'Apple Watch Series 9 45mm, Garmin Fenix 7', 'required' => true],
                ],
                'syarat_ketentuan' => ['Sudah unpair dan bebas iCloud/kunci akun, charger asli dibawa.'],
                'proses_taksiran' => 'Pemeriksaan layar sentuh, sensor detak jantung, kelayakan baterai, dan bodi.',
                'ketentuan_penyimpanan' => 'Disimpan dalam wadah khusus anti-benturan bersegel.',
            ],
        ];

        foreach ($produk as $i => $item) {
            Product::updateOrCreate(['slug' => $item['slug']], [
                'branch_id' => null,
                'nama' => $item['nama'],
                'kategori_barang' => $item['kategori_barang'],
                'kelompok_agunan' => $item['kelompok_agunan'],
                'deskripsi' => $item['deskripsi'],
                'pinjaman_min' => $item['pinjaman_min'],
                'pinjaman_max' => $item['pinjaman_max'],
                'ltv_max_bp' => $item['ltv_max_bp'],
                'tenor_hari' => $item['tenor_hari'],
                'sewa_modal_per_hari_bp' => $item['sewa_modal_per_hari_bp'],
                'blok_hari' => $item['blok_hari'] ?? 15,
                'admin_bp' => $item['admin_bp'],
                'admin_min' => $item['admin_min'],
                'admin_max' => $item['admin_max'],
                'urutan' => $i + 1,
                'is_active' => true,
                'badge_label' => $item['badge_label'] ?? null,
                'dokumen_wajib' => $item['dokumen_wajib'] ?? null,
                'atribut_khusus' => $item['atribut_khusus'] ?? null,
                'syarat_ketentuan' => $item['syarat_ketentuan'] ?? null,
                'proses_taksiran' => $item['proses_taksiran'] ?? null,
                'ketentuan_penyimpanan' => $item['ketentuan_penyimpanan'] ?? null,
            ]);
        }
    }
}
