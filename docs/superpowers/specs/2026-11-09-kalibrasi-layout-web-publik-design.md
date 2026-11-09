# Desain Kalibrasi Layout Halaman Web Publik linygadai

## 1. Latar Belakang & Tujuan
Halaman web utama linygadai saat ini telah memiliki fungsionalitas lengkap (simulasi pinjaman interaktif, produk gadai, panduan alur, standar layanan, pengumuman, tanya jawab, informasi cabang/kontak, serta autentikasi/pendaftaran anggota). Namun secara tata letak (*layout*) dan hierarki visual, halaman perlu dikalibrasi agar memiliki ritme, kerapian, dan komposisi modern layaknya referensi situs [rentalmobil.systemlji.com](https://rentalmobil.systemlji.com/) (`rentalmobil.html`).

## 2. Batasan Utama (Constraints)
1. **Integritas Konten & Alur Kerja**: Seluruh data dinamis dari database (produk, faq, cabang, pengumuman, pengaturan kebijakan), form autentikasi, serta kalkulator simulasi JavaScript (`situs.js`) tidak boleh diubah atau dirusak logikanya.
2. **Lingkup Halaman**: Hanya menyentuh halaman publik (`resources/views/publik/` dan `layouts/publik.blade.php`). Panel internal/stakeholder (owner, manager, admin, teller, analis, anggota) tidak disentuh.
3. **Palet Warna Asli Tetap Utuh**: Identitas warna khas linygadai (`--tinta: #1B2A2F`, `--kertas: #F4EFE6`, `--putih: #FFFCF5`, `--kuningan: #B8862B`, `--terakota: #B5472E`, `--lumut: #3E5C4A`) tetap digunakan tanpa mengubahnya menjadi skema warna biru referensi.
4. **Penambahan Fitur**:
   - Floating WhatsApp Call-to-Action (dengan tautan dinamis ke nomor cabang/pusat).
   - Tombol Back-to-Top melayang dengan transisi halus saat scroll.
   - Filter tab kategori produk (Semua, Ponsel, Laptop, Kamera, Televisi, Konsol, Smartwatch).
   - Banner registrasi anggota dengan kontras tinggi.

## 3. Komponen & Struktur Layout Baru
1. **Header (Kepala)**:
   - Sticky top navbar dengan backdrop blur.
   - Kiri: Logo linygadai + label status ("Makassar · Cabang A & B").
   - Tengah: Menu navigasi utama (`Beranda`, `Produk`, `Standar`, `Panduan`, `Simulasi`, `FAQ`, `Kontak`).
   - Kanan: Tombol aksi cepat `"Simulasi"` dan `"Masuk / Akun"`, serta tombol hamburger responsif untuk perangkat mobile.
2. **Hero Section**:
   - Kolom Kiri: Eyebrow badge (*"TITIP GADAI ELEKTRONIK RESMI · BIAYA JELAS DI MUKA"*), Judul H1 tebal & berwibawa, paragraf pengantar, checklist keunggulan dengan ikon centang, dan grup tombol CTA ganda (`"Hitung Simulasi"` dan `"Pelajari Syarat"`).
   - Kolom Kanan: *Offset Frame* kartu ringkasan interaktif yang menampilkan preview simulasi cepat & jaminan keamanan barang.
   - Bawah: Bar metrik/statistik layanan (Plafon s.d 80%, Tenor s.d 120 Hari, 2 Cabang Resmi, Taksiran 15 Menit).
3. **Katalog Produk Gadai**:
   - Bar filter kategori interaktif (tab pills).
   - Grid kartu 3 kolom seragam: Badge plafon, ikon barang, judul produk, chips syarat/spesifikasi (taksiran min-maks, tenor fleksibel), dan tombol *"Simulasi Produk Ini"*.
4. **Standar Layanan (Mengapa Memilih Kami)**:
   - Grid 4 kartu fitur dengan wadah ikon beraksen kuningan, judul standar, dan deskripsi transparan.
5. **Panduan Alur Gadai (3 Langkah Ringkas)**:
   - Kartu proses bernomor langkah besar (01 Datang & Taksir, 02 Akad & Terima Dana, 03 Tebus Kapan Saja).
6. **Banner Anggota**:
   - Banner kontras berlatar `--tinta` dengan aksen kuningan: mengajak pengunjung mendaftar untuk memantau status gadai dan perpanjangan secara online.
7. **Simulasi Pinjaman**:
   - Layout 2 kolom: Form input taksiran & tenor di kiri, Nota Rincian Biaya (struk gadai) yang *sticky* di kanan.
8. **Pengumuman & FAQ**:
   - Slider pengumuman ringkas dan akordeon FAQ modern yang responsif.
9. **Tentang Kami & Lokasi Cabang**:
   - Layout 2 kolom: Info legalitas, kontak & jam operasional di kiri, peta lokasi cabang di kanan.
10. **Footer (Kaki)**:
    - 4 kolom: Identitas & ringkasan linygadai, navigasi cepat, kontak & cabang, dan izin/regulasi, diakhiri dengan baris hak cipta.
11. **Fitur Tambahan Melayang**:
    - Tombol WhatsApp CS 24 Jam di sudut kanan bawah.
    - Tombol kembali ke atas (*Back to top*) yang otomatis tampil setelah scroll > 300px.

