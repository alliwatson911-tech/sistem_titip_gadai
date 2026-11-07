# linygadai — Checklist pengujian dan kesiapan (Tahap 5)

Legenda: **Otomatis** = dicakup uji PHPUnit (238 uji, `php artisan test`); **Manual** = saya periksa langsung di browser pada sesi pengembangan; **Belum** = belum diuji, perlu Anda atau pihak lain.

## 1. Fungsional

| Area | Hasil | Cakupan |
|---|---|---|
| Registrasi calon anggota (NIK 16 digit, NIK ganda, cabang pusat ditolak, peran tak bisa disetel) | Otomatis | `AuthTest` |
| Masuk/keluar, pembatasan 5 percobaan, akun nonaktif keluar di tengah sesi | Otomatis | `AuthTest` |
| Ganti sandi, lupa sandi (token sekali pakai, 60 menit, jawaban seragam), atur ulang | Otomatis | `KataSandiTest` |
| Halaman publik: 9 bagian berurutan, produk dari DB, token kebijakan, kontak kosong tidak dikarang, XSS | Otomatis | `PublicPageTest` |
| Simulasi (rumus tunggal di server, batas produk, validasi) | Otomatis + Manual | `LoanCalculatorTest`, `PublicPageTest`; kalkulator dicek di browser |
| Pengajuan → terima barang → taksir → (Manager) → disetujui/ditolak/dibatalkan/kedaluwarsa | Otomatis | `PengajuanAlurTest` (24) |
| Verifikasi anggota, KTP privat, profil, NIK tersamar + audit | Otomatis | `PanelAdminTest` |
| Pengumuman, FAQ, kontak cabang, teks global, manajemen staf, reset sandi | Otomatis | `PanelAdminTest` (28) |
| Pencairan, tagihan (tebus dini/tepat/terlambat), perpanjang, cicil, batas perpanjangan | Otomatis | `GadaiAlurTest` (hitungan manual) |
| Transfer + verifikasi silang, pembalikan, pengeluaran barang (penggadai/kuasa) | Otomatis | `GadaiAlurTest` |
| Scheduler tenggang → siap lelang (idempoten), pembayaran saat lelang, lelang laku/kekurangan/tidak laku | Otomatis | `GadaiAlurTest` (33) |
| Email pengingat jatuh tempo/tenggang/lelang: waktu, tanpa ganda, siklus setelah perpanjang, gagal dicoba ulang (maks 3), akun nonaktif, isi tanpa data pribadi | Otomatis | `PengingatTest` (13) |
| Laporan cabang/konsolidasi/harian, rekonsiliasi kas, CSV aman | Otomatis | `LaporanAuditTest` |
| Penampil audit (per cabang, filter, detail) | Otomatis | `LaporanAuditTest` |
| Daftar pengajuan dan form terima barang tampil benar; halaman gadai; halaman lupa sandi | Manual | browser |

## 2. Hak akses

| Pemeriksaan | Hasil |
|---|---|
| 25 titik kritis matriks (owner tidak mencairkan, teller tidak membatalkan bayar, analis tidak menyetujui, dst.) | Otomatis (`AccessMatrixTest`) |
| 6 peran x tindakan terlarang → 403 (pengajuan, pembayaran, pencairan, lelang, laporan, audit, pengguna, konten) | Otomatis |
| Isolasi cabang dan anggota → 404 (pengajuan, gadai, anggota, berkas, pengumuman, kontak, audit) | Otomatis |
| Pemisahan tugas: pembuat ≠ pemutus; pencatat transfer ≠ verifikator; Manager saja yang membalikkan | Otomatis |
| Sapuan **semua** halaman GET panel untuk 6 peran: tidak ada galat 500, tamu selalu ke `/masuk` | Otomatis (`KeamananTest`) |
| Matriks lengkap izin x peran | [matriks-akses.md](matriks-akses.md) (dibuat dari konfigurasi) |

## 3. Keamanan

| Kontrol | Hasil |
|---|---|
| Kata sandi di-hash; tidak pernah di audit/log aplikasi | Otomatis |
| NIK, IMEI, NIK penebus terenkripsi; hash HMAC untuk pencarian; tampilan tersamar | Otomatis |
| Mass assignment: peran, status, saldo, jatuh tempo tidak bisa diisi dari input | Otomatis |
| Pesan galat masuk seragam; pembatasan laju masuk, daftar, lupa sandi, pembayaran, unggah | Otomatis |
| Sesi: regenerasi saat masuk; keluar dari semua perangkat saat ganti/atur ulang sandi/nonaktif | Otomatis |
| Unggahan: hanya JPG/PNG/WebP (+PDF surat kuasa), maks 4 MB, SVG/exe ditolak; disk privat, tanpa rute `/storage` | Otomatis |
| CSP ketat (skrip hanya dari asal sendiri), `X-Frame-Options`, `nosniff`, `Referrer-Policy`, HSTS pada HTTPS, `no-store` untuk halaman login | Otomatis; CSP diverifikasi aktif di browser tanpa pelanggaran |
| Tidak ada penangan/skrip inline di 13 halaman sampel | Otomatis |
| XSS: konten pengguna di-escape (pengumuman, audit, dll.) | Otomatis |
| `audit_logs` tak dapat diubah/dihapus, **termasuk lewat SQL langsung** (trigger) | Otomatis |
| Total pembayaran = jumlah komponen; saldo ≤ pokok awal (constraint database) | Otomatis |
| CSV tahan injeksi rumus spreadsheet | Otomatis |
| Pemeriksa konfigurasi `sistem:periksa` (debug, kunci, email, cookie secure, akun DB, scheduler, trigger) | Otomatis |
| Temuan yang sudah diperbaiki selama pengembangan | `config("permissions.x.y")` salah baca titik; validasi `exists` `!=` tak bekerja; rute `/storage` untuk disk privat; kontras warna |
| Pemindaian kerentanan dependensi (`composer audit`) | Manual: tidak ada advisori saat dijalankan; ulangi berkala |
| **Uji penetrasi pihak ketiga, uji beban** | **Belum** |

## 4. Responsif, aksesibilitas, kinerja

| Pemeriksaan | Hasil |
|---|---|
| Halaman publik di lebar 375 px dan desktop: tidak ada luapan horizontal | Manual |
| Lighthouse halaman publik, desktop | Manual: Aksesibilitas 100, Best Practices 100, SEO 100 |
| Lighthouse halaman publik, mobile | Manual: Aksesibilitas 97 (satu masalah kontras, **sudah diperbaiki**), Best Practices 100, SEO 100. Mobile belum diulang sesudah perbaikan |
| Sentuhan minimal 44 px, fokus terlihat, `prefers-reduced-motion`, tautan lompat ke isi | Dirancang; sebagian lewat Lighthouse |
| Panel di layar ponsel | **Belum** dicek visual (nav menjadi baris geser, tabel dapat digeser) |
| Kinerja (Lighthouse performance/trace), uji beban, Safari/Firefox, pembaca layar | **Belum** |

## 5. Sebelum go-live (wajib, bukan opsional)

1. **Verifikasi hukum**: izin usaha dan status perizinan, aturan lelang dan hasilnya, batas biaya/sewa modal yang diizinkan, perlindungan data pribadi (UU PDP: dasar pemrosesan, masa simpan, hak subjek data, pemberitahuan insiden). Isi kolom izin di panel hanya dengan keterangan yang sudah diverifikasi. Semua angka di `policy_settings` dan produk adalah ASUMSI.
2. **Konfigurasi**: salin `.env.production.example`, isi semuanya, lalu `php artisan sistem:periksa` harus tanpa GAGAL/PERINGATAN. Terutama: HTTPS + `SESSION_SECURE_COOKIE=true`, SMTP sungguhan (bukan `log`), `APP_URL` benar, pengguna database khusus (bukan root), `APP_DEBUG=false`.
3. **Scheduler**: `php artisan schedule:run` tiap menit; periksa bahwa `sistem:periksa` melaporkan scheduler OK.
4. **Cadangan dan pemulihan**: cadangkan database **dan** `storage/app/private` (KTP, foto, bukti) secara terenkripsi, termasuk `PII_HASH_KEY` dan `APP_KEY` di tempat terpisah. **Uji pemulihan sekali** sebelum dipakai. Kehilangan `PII_HASH_KEY` merusak pencarian NIK; kehilangan `APP_KEY` membuat data terenkripsi tak terbaca.
5. **Bersihkan data contoh**: hapus akun `*.test` dan anggota contoh, atau ganti sandinya; buat akun Owner sungguhan dan nonaktifkan yang contoh.
6. **Konten asli**: kontak resmi, foto kantor, tagline final, gambar pratinjau tautan, pengumuman, FAQ yang sudah disetujui pemilik.
7. **Operasional**: SOP kas teller harian (cocokkan dengan laporan harian), prosedur pembalikan, siapa yang memantau jejak audit, dan pantau halaman gadai untuk pemberitahuan berstatus "gagal" (kirim ulang otomatis 3 kali; setelah itu perlu tindak lanjut manual, mis. menghubungi anggota).
8. **Uji penerimaan pengguna** oleh staf dan beberapa anggota sungguhan di lingkungan percobaan; **uji keamanan pihak ketiga**; ulangi `composer audit` saat go-live.

## 6. Keterbatasan yang diketahui

- Pemberitahuan jatuh tempo dan lelang hanya lewat **email** (WhatsApp/SMS belum ada). Pengiriman nyata belum diuji dengan SMTP sungguhan (uji memakai pemalsu dan driver array).
- Penaksiran ulang saat perpanjang, penebusan sebagian, verifikasi email, 2FA staf, pembayaran mandiri anggota (gateway) belum ada.
- Staf belum dapat membuka berkas surat kuasa/bukti pencairan lewat panel (tersimpan privat).
- Laporan memakai dasar kas dan portofolio keadaan saat ini; belum ada buku besar/akuntansi.
- Satu database `linygadai` lain di komputer pengembangan ini dipakai aplikasi berbeda; proyek ini memakai `linygadai_app`.
