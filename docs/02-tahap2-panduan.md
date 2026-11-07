# linygadai — Panduan Tahap 2 (Halaman Publik)

## Yang dibangun

Satu halaman di `/` dengan urutan: header navigasi, beranda (sambutan, ringkasan, papan pengumuman, standar layanan, FAQ dan informasi dalam accordion), produk, panduan, simulasi beserta tata cara, tentang kami, dan masuk akun (masuk dan daftar).

| Bagian | Berkas |
|---|---|
| Halaman dan partial | `resources/views/publik/` (`beranda.blade.php` + `partials/`) |
| Tata letak dan SEO kepala | `resources/views/layouts/publik.blade.php` |
| Gaya dan skrip | `public/css/situs.css`, `public/js/situs.js` |
| Font (dihosting sendiri, lisensi OFL) | `public/fonts/` |
| Data halaman | `app/Http/Controllers/Publik/BerandaController.php` |
| Rumus biaya (satu sumber) | `app/Services/LoanCalculator.php` |
| Endpoint simulasi | `GET /simulasi` (`SimulasiController`) |
| Konten awal | `database/seeders/ContentSeeder.php` |

## Keputusan yang berbeda dari rancangan Tahap 0

- **Tanpa Tailwind dan Alpine.** Rancangan menyebutnya, tetapi saya memakai CSS dan JavaScript biasa. Alasannya: tidak perlu Node/Vite untuk menjalankan situs, halaman lebih ringan (empat berkas font totalnya sekitar 96 KB), dan sistem desain lebih mudah dirawat. Bila Anda tetap ingin Tailwind, itu bisa ditambahkan tanpa mengubah markup.
- **Simulasi dihitung di server**, bukan di browser, supaya angkanya pasti sama dengan tagihan nyata di Tahap 4. Konsekuensinya, simulasi butuh koneksi ke server (ada pesan galat bila gagal).

## Mengisi kontak dan konten (belum ada panel admin)

Panel Admin untuk konten, pengumuman, dan FAQ dibangun di Tahap 3. Sementara itu:

- **Kontak dan lokasi**: isi di `.env`, lalu jalankan `php artisan db:seed --class=BranchSeeder`. Variabel: `PUSAT_ALAMAT`, `PUSAT_TELEPON`, `PUSAT_WHATSAPP`, `PUSAT_EMAIL`, `PUSAT_LAT`, `PUSAT_LNG`, `PUSAT_JAM`, dan hal yang sama untuk `A_...` dan `B_...`. Butir yang kosong otomatis disembunyikan, peta tampil setelah koordinat diisi.
- **Media sosial dan izin usaha**: tabel `site_contents`, kunci `sosmed` (JSON, hanya URL `https://` yang tampil) dan `izin_usaha` (teks). Sampai kolom izin diisi, situs menampilkan catatan netral dan **tidak mengklaim izin apa pun**.
- **Angka di teks** (tenor, masa tenggang, hari pemberitahuan lelang, maksimal perpanjangan) memakai token seperti `{tenor}` yang diganti dari `policy_settings`, jadi ikut berubah bila kebijakan diubah.

## Pemeriksaan yang sudah dijalankan

- 93 uji otomatis lulus. Tambahan Tahap 2 ada 25: 17 untuk halaman publik dan endpoint simulasi, 8 untuk kalkulator. Isinya: urutan 9 bagian, satu `h1`, produk dari database, token kebijakan, kontak kosong tidak dikarang, tautan sosmed non-https ditolak, pengumuman kedaluwarsa tersembunyi, escape XSS, JSON-LD valid, sitemap, header keamanan, dan validasi simulasi.
- Dicek di browser: desktop dan lebar 375 px tanpa luapan horizontal, tanpa galat konsol, kalkulator menghasilkan Rp 2.532.000 untuk contoh taksiran Rp 3 juta (sama dengan hitungan manual).
- **Belum dijalankan**: audit Lighthouse, uji pembaca layar, dan uji di Safari/Firefox.

## Catatan penting

- **Database `linygadai` di komputer ini berisi tabel aplikasi lain**, jadi proyek ini memakai `linygadai_app` (dan `linygadai_test` untuk uji). Satu tabel `members` sisa dari migrasi saya yang gagal masih ada di `linygadai`; penghapusannya ditolak oleh pemeriksa izin, jadi saya biarkan untuk Anda putuskan.
- **Tagline** "Titip hari ini, tenang sampai tebus." masih usulan; ganti lewat `site_contents.tagline`.
- **Belum ada foto asli** (kantor, staf, barang) dan gambar Open Graph untuk pratinjau tautan. Ilustrasi yang ada adalah SVG buatan sendiri.
- **Standar layanan hanya memuat hal yang dijamin sistem** (segel, tidak dipakai, biaya dirinci). Klaim asuransi dan izin sengaja tidak ditulis sampai Anda memverifikasinya.
- `robots.txt` belum memuat baris `Sitemap:` karena membutuhkan alamat domain; daftarkan `/sitemap.xml` di Google Search Console setelah domain ada.
