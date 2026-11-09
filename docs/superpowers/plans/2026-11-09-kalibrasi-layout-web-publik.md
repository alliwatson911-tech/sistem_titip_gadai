# Kalibrasi Layout Halaman Web Publik linygadai Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengkalibrasi bentuk dan tata letak halaman web publik linygadai agar memiliki ritme, hierarki, dan tata letak modern seperti pada referensi `https://rentalmobil.systemlji.com/` (`rentalmobil.html`), dengan tetap mempertahankan palet warna asli, keutuhan konten, dan alur kerja aplikasi yang sudah berjalan.

**Architecture:** Memperbarui stylesheet [public/css/situs.css](file:///c:/XAMPP/htdocs/linygadai/public/css/situs.css) untuk mendukung layout grid modern, kontainer responsif, kartu terstandarisasi, dan komponen melayang; memperbarui template Blade publik di [resources/views/publik/](file:///c:/XAMPP/htdocs/linygadai/resources/views/publik/); serta menambahkan interaktivitas filter kategori dan tombol navigasi di [public/js/situs.js](file:///c:/XAMPP/htdocs/linygadai/public/js/situs.js).

**Tech Stack:** Laravel 12/11 Blade Templates, CSS3 Grid/Flexbox with CSS Custom Properties, Vanilla JavaScript (ES6+), PHP 8.5, MariaDB.

**Spec:** [docs/superpowers/specs/2026-11-09-kalibrasi-layout-web-publik-design.md](file:///c:/XAMPP/htdocs/linygadai/docs/superpowers/specs/2026-11-09-kalibrasi-layout-web-publik-design.md)

## Global Constraints

- Warna identitas tidak berubah: `--tinta: #1B2A2F`, `--kertas: #F4EFE6`, `--putih: #FFFCF5`, `--kuningan: #B8862B`, `--terakota: #B5472E`, `--lumut: #3E5C4A`.
- Hanya halaman web publik; area stakeholder / dashboard staf dan anggota tidak diubah.
- Semua nama field form, ID elemen simulasi (`#simulasi`, `#produk`, `#taksiran`, `#jangka`, `#persen`, `#nota`), CSRF tokens, dan handler JavaScript harus tetap berfungsi 100%.
- Seluruh 279 pengujian unit & fitur (`php artisan test`) harus tetap lulus dengan 0 kegagalan.

## Review Focus

1. Form simulasi tetap menghitung sewa modal, biaya administrasi, dan total tebus secara akurat saat memilih produk atau menggeser slider.
2. Tautan ke formulir pendaftaran dan formulir masuk (`#akun`) tetap dapat diakses dan divalidasi dengan benar.
3. Tampilan responsif pada ukuran mobile (< 768px) tidak mengalami *horizontal overflow* dan menu seluler dapat dibuka/tutup dengan lancar.
4. Tombol WhatsApp melayang memiliki nomor tujuan dinamis dari data cabang pusat/cabang aktif yang valid.
5. Filter tab kategori produk menyaring kartu tanpa menyebabkan halaman *reloading*.

---

### Task 1: CSS Layout & Design System Enhancement

**Files:**
- Modify: `public/css/situs.css`

**Interfaces:**
- Consumes: CSS variables `--tinta`, `--kertas`, `--putih`, `--kuningan`, `--terakota`, `--lumut`.
- Produces: Class baru untuk layout terkalibrasi: `.site-container`, `.hero-offset-frame`, `.car-card-grid`, `.card-modern`, `.feature-grid`, `.step-card`, `.floating-whatsapp-pill`, `.back-to-top`, `.banner-cta`, `.filter-tabs`.

- [ ] **Step 1: Definisikan utility layout modern dan komponen responsif di `public/css/situs.css`**
  Tambahkan kelas container terstandarisasi, grid kartu produk 3 kolom, badge status, chip spesifikasi, kartu langkah alur bernomor, banner kontras tinggi, tombol WhatsApp melayang, dan tombol kembali ke atas tanpa mengubah skema warna asli.
- [ ] **Step 2: Verifikasi syntax CSS tidak rusak dan loadable**
  Jalankan pengecekan file CSS dan pastikan tidak ada syntax error.
- [ ] **Step 3: Commit perubahan CSS**
  `git add public/css/situs.css && git commit -m "style: calibrate public layout styles and modern components"`

---

### Task 2: Header & Floating Action Components

**Files:**
- Modify: `resources/views/publik/partials/kepala.blade.php`
- Create: `resources/views/publik/partials/floating-cta.blade.php`
- Modify: `resources/views/layouts/publik.blade.php`

**Interfaces:**
- Consumes: Variabel `$pusat`, `$cabang`, `$konten` dari controller beranda.
- Produces: Header navbar modern dengan logo + status cabang + menu link + tombol CTA simulasi & login; serta floating WhatsApp & back-to-top.

- [ ] **Step 1: Kalibrasi `kepala.blade.php`**
  Perbarui struktur navbar: logo linygadai dengan subtitle lokasi cabang, menu navigasi dengan gaya teks rapi berjarak proporsional, tombol CTA *"Simulasi"* & *"Masuk"*, dan toggle hamburger mobile yang konsisten.
- [ ] **Step 2: Buat `floating-cta.blade.php`**
  Buat partial tombol WhatsApp melayang dengan tautan ke nomor WA cabang pusat (menggunakan format `https://wa.me/...`) dan tombol Back-to-Top dengan ikon chevron ke atas.
- [ ] **Step 3: Sertakan `floating-cta` di `layouts/publik.blade.php`**
  Tambahkan `@include('publik.partials.floating-cta')` sebelum penutup body di `layouts/publik.blade.php`.
- [ ] **Step 4: Commit perubahan Header & Floating CTA**
  `git add resources/views/publik/partials/kepala.blade.php resources/views/publik/partials/floating-cta.blade.php resources/views/layouts/publik.blade.php && git commit -m "feat: modernize public header and add floating action buttons"`

---

### Task 3: Hero Section & Service Highlights Calibration

**Files:**
- Modify: `resources/views/publik/partials/hero.blade.php`

**Interfaces:**
- Consumes: Variabel `$produk`, `$konten`, `$pusat` dari controller.
- Produces: Layout hero 2-kolom dengan eyebrow badge, headline tegas, checklist keunggulan berikon, dual CTA, offset floating card preview, dan bar statistik layanan.

- [ ] **Step 1: Perbarui struktur `hero.blade.php`**
  Implementasikan layout 2 kolom:
  - Sisi kiri: Pill badge *"TITIP GADAI ELEKTRONIK RESMI · BIAYA JELAS DI MUKA"*, judul H1, lead text, 3 poin keunggulan utama dengan ikon centang (`✓ Transparansi biaya di muka`, `✓ Barang aman di brankas bersegel`, `✓ Penebusan fleksibel kapan saja`), dan tombol ganda *"Hitung Simulasi Titip"* & *"Lihat Produk Gadai"*.
  - Sisi kanan: Offset floating card dengan visual ringkasan layanan & garansi keamanan barang.
  - Sisi bawah: Bar metrik statistik 4 kolom (Plafon s.d 80%, Tenor 30–120 Hari, 2 Cabang Resmi, Taksiran 15 Menit).
- [ ] **Step 2: Commit perubahan Hero**
  `git add resources/views/publik/partials/hero.blade.php && git commit -m "feat: calibrate hero section layout and metrics bar"`

---

### Task 4: Product Catalog Grid & Category Tabs

**Files:**
- Modify: `resources/views/publik/partials/produk.blade.php`

**Interfaces:**
- Consumes: Koleksi `$produk` dari database.
- Produces: Tab filter kategori barang dan grid kartu seragam 3-kolom modern dengan badge plafon, spesifikasi chips, dan tombol interaksi cepat.

- [ ] **Step 1: Kalibrasi `produk.blade.php`**
  Tambahkan bar filter kategori produk (`Semua`, `Ponsel`, `Laptop`, `Kamera`, `Televisi`, `Konsol`, `Lainnya`).
  Ubah grid kartu produk menjadi format grid 3-kolom seragam dengan:
  - Header kartu: Ikon produk + badge plafon persentase.
  - Body kartu: Nama produk, keterangan penerimaan barang, chip spesifikasi (rentang taksiran, tenor).
  - Footer kartu: Estimasi plafon dan tombol aksi ganda *"Simulasi Sekarang"* (mengisi pilihan produk di kalkulator) & *"Syarat & Ketentuan"*.
- [ ] **Step 2: Commit perubahan Produk**
  `git add resources/views/publik/partials/produk.blade.php && git commit -m "feat: calibrate product catalog grid with category filters"`

---

### Task 5: Feature Cards, Process Steps & Member Banner

**Files:**
- Modify: `resources/views/publik/partials/standar.blade.php`
- Modify: `resources/views/publik/partials/panduan.blade.php`
- Create: `resources/views/publik/partials/banner-member.blade.php`
- Modify: `resources/views/publik/beranda.blade.php`

**Interfaces:**
- Consumes: Data keunggulan, panduan syarat, dan banner ajakan anggota.
- Produces: Grid 4 kartu keunggulan berikon, 3 kartu langkah alur bernomor langkah, dan banner aksen gelap untuk ajakan bergabung anggota.

- [ ] **Step 1: Kalibrasi `standar.blade.php` (Mengapa Memilih Kami)**
  Ubah layout menjadi grid 4 kartu keunggulan modern dengan ikon dalam wadah aksen kuningan, judul standar, dan deskripsi transparan.
- [ ] **Step 2: Kalibrasi `panduan.blade.php` (Cara Titip Gadai 3 Langkah Ringkas)**
  Ubah alur menjadi kartu langkah proses bernomor besar (`01 Bawa Barang & KTP`, `02 Taksiran & Kesepakatan`, `03 Terima Dana & Nota`) disertai kartu syarat kelengkapan di sampingnya.
- [ ] **Step 3: Buat `banner-member.blade.php` dan sertakan di `beranda.blade.php`**
  Buat banner kontras tinggi dengan ajakan *"Daftar Anggota linygadai — Pantau Status Titip Gadai & Tagihan Secara Online"* lengkap dengan tombol ke `#akun`.
- [ ] **Step 4: Commit perubahan Fitur, Alur & Banner**
  `git add resources/views/publik/partials/standar.blade.php resources/views/publik/partials/panduan.blade.php resources/views/publik/partials/banner-member.blade.php resources/views/publik/beranda.blade.php && git commit -m "feat: modernize service standards, process steps, and add member banner"`

---

### Task 6: Simulation, FAQ, About & Modern Footer Layout

**Files:**
- Modify: `resources/views/publik/partials/simulasi.blade.php`
- Modify: `resources/views/publik/partials/faq.blade.php`
- Modify: `resources/views/publik/partials/tentang.blade.php`
- Modify: `resources/views/publik/partials/kaki.blade.php`

**Interfaces:**
- Consumes: Form ID `#simulasi`, `#nota`, `$faq`, `$pusat`, `$cabang`, `$konten`.
- Produces: Form simulasi 2-kolom dengan nota *sticky*, akordeon FAQ modern, tata letak kontak & peta 2-kolom, serta footer 4-kolom modern.

- [ ] **Step 1: Kalibrasi `simulasi.blade.php`**
  Pertahankan semua atribut `name`, `id`, `class="sim"`, `#nota`, `#taksiran`, `#jangka`, `#persen` agar JS simulasi tetap bekerja. Rapikan tata letak form menjadi kartu modern di sisi kiri dan nota struk di sisi kanan yang lengket saat di-scroll.
- [ ] **Step 2: Kalibrasi `faq.blade.php`**
  Tata akordeon FAQ dengan border halus, indikator toggle plus/minus yang tegas, dan transisi buka/tutup yang mulus.
- [ ] **Step 3: Kalibrasi `tentang.blade.php` & `kaki.blade.php`**
  Rapikan blok kontak, legalitas izin, dan peta cabang. Di footer (`kaki.blade.php`), tata menjadi 4 kolom bersih: Identitas Brand, Navigasi Cepat, Jam & Kontak Cabang, serta Informasi Regulasi & Hak Cipta.
- [ ] **Step 4: Commit perubahan Simulasi, FAQ, Tentang, dan Footer**
  `git add resources/views/publik/partials/simulasi.blade.php resources/views/publik/partials/faq.blade.php resources/views/publik/partials/tentang.blade.php resources/views/publik/partials/kaki.blade.php && git commit -m "feat: calibrate simulation, faq, about, and modern footer"`

---

### Task 7: JavaScript Interactions (Category Filter & Back-to-Top)

**Files:**
- Modify: `public/js/situs.js`

**Interfaces:**
- Consumes: Klik pada tab filter kategori dan event scroll jendela browser.
- Produces: Tampilan kartu terfilter secara instan dan visibilitas tombol kembali ke atas.

- [ ] **Step 1: Tambahkan interaktivitas filter kategori dan back-to-top di `public/js/situs.js`**
  - Implementasikan event listener pada filter tabs `.tab-kategori` untuk menampilkan/menyembunyikan kartu produk berdasarkan `data-kategori`.
  - Implementasikan event listener scroll window untuk menampilkan tombol `#back-to-top` saat scroll melewati 300px dan scroll ke atas secara halus saat diklik.
  - Pastikan seluruh logika kalkulasi simulasi (`hitung()`, `perbaruiPlafon()`, dll.) tetap berjalan tanpa modifikasi yang merusak.
- [ ] **Step 2: Commit perubahan JavaScript**
  `git add public/js/situs.js && git commit -m "feat: add category filter and back-to-top interactions in situs.js"`

---

### Task 8: Verification & Regression Testing

**Files:**
- Test: `tests/Feature/PublicPagesTest.php` (atau test suite yang ada)

- [ ] **Step 1: Jalankan `php artisan test` untuk memastikan seluruh 279 tes tetap lulus**
  Jalankan perintah pengujian lengkap dan pastikan tidak ada regresi.
- [ ] **Step 2: Lakukan verifikasi HTTP GET request ke `http://127.0.0.1:8000`**
  Pastikan response code 200 OK dan semua elemen utama (header, hero, produk, banner, simulasi, faq, footer, floating buttons) ter-render dalam HTML.
- [ ] **Step 3: Verifikasi responsivitas dan struktur akhir**
  Periksa kelengkapan elemen dan pastikan tidak ada script error di console.

