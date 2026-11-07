# linygadai — Panduan Tahap 3 (Modul per peran)

## Yang dibangun

Satu pengajuan titip gadai kini bisa berjalan dari anggota sampai **disetujui, siap cair**, dengan semua peran bekerja di panel `/panel`. Pencairan, pembayaran, dan penebusan menyusul di Tahap 4.

| Peran | Yang dapat dikerjakan |
|---|---|
| Anggota | Unggah KTP dan ubah kontak (profil), ajukan titip gadai, pantau status, batalkan selama menunggu barang |
| Admin cabang | Verifikasi, tolak, atau blokir anggota; lihat NIK lengkap (dicatat audit); kelola pengumuman, FAQ, dan kontak cabangnya |
| Teller | Buat pengajuan atas nama anggota, terima dan segel barang (foto, IMEI, nomor segel), catat barang dikembalikan bila ditolak |
| Analis | Taksir barang, isi daftar periksa kelayakan, beri rekomendasi setuju atau tolak |
| Manager | Putuskan pengajuan di atas limit; pantau seluruh pengajuan cabangnya |
| Owner | Semua cabang; kelola staf (tambah, ubah, nonaktifkan, reset sandi); kontak kantor pusat; tagline, sambutan, izin usaha, media sosial |

## Aturan alur (semua di `app/Services/ApplicationFlow.php`)

```
menunggu_barang -> barang_diterima -> (taksir) -> disetujui                 [pinjaman <= limit]
                                              -> menunggu_manager -> disetujui | ditolak   [pinjaman > limit]
                                              -> ditolak
menunggu_barang -> dibatalkan (anggota/teller) | kedaluwarsa (otomatis)
```

- **Pinjaman disetujui** = yang terkecil dari pinjaman diminta dan plafon (taksiran x batas pinjaman produk, maksimal batas produk). Bila plafon di bawah minimum produk, hanya bisa ditolak.
- **Limit persetujuan Manager** dan angka lain diambil dari `policy_settings` (nilai awal: Rp 5.000.000, barang dibawa dalam 7 hari, maksimal 3 pengajuan berjalan per anggota) **[ASUMSI]**.
- **Pemisahan tugas**: pembuat pengajuan tidak dapat memutuskannya sendiri; analis tidak menyetujui; teller tidak menaksir; setiap langkah hanya sah dari status asalnya (dikunci per baris, tidak bisa diulang atau dilompati).
- **IMEI/serial ganda**: barang dengan nomor yang sama yang masih dititipkan ditolak. IMEI disimpan terenkripsi; yang tampil hanya empat digit terakhir.
- **Skor kelayakan** = lima butir daftar periksa x 20 poin (identitas, barang menyala, bebas kunci, kelengkapan, riwayat anggota).
- **Kedaluwarsa**: perintah `pengajuan:kedaluwarsa` dijadwalkan harian 01:00. Agar jalan di server, jalankan `php artisan schedule:run` setiap menit (di Windows lewat Task Scheduler, di Linux lewat cron).

## Berkas pribadi

KTP dan foto barang disimpan di `storage/app/private`, tidak punya URL publik, dan hanya dilayani lewat `/panel/berkas/...` setelah otorisasi: KTP untuk pemilik dan staf cabangnya (dicatat audit saat staf membuka), foto barang hanya untuk staf cabang yang sama. Unggahan dibatasi JPG/PNG/WebP maksimal 4 MB; SVG dan PDF ditolak.

## Keputusan yang berbeda dari rancangan Tahap 0

- **Status `ditaksir` tidak menjadi status tersendiri.** Hasil taksiran langsung memindahkan pengajuan ke disetujui, menunggu Manager, atau ditolak.
- **Tiga izin baru** di `config/permissions.php`: `cabang.kontak` (Owner, Admin), `konten.situs` (Owner), `profil.ubah` (Anggota). Matriks di dokumen 00 perlu ditambah baris ini.
- **Konten per cabang**: Admin mengelola pengumuman dan FAQ cabangnya serta kontak cabangnya; konten global (tagline, izin usaha, kontak kantor pusat) hanya Owner. Halaman publik menampilkan pengumuman semua cabang dengan label cabang.
- **Masuk dan daftar** kini satu tempat: bagian `#akun` di halaman utama. `/masuk` dan `/daftar` mengalihkan ke sana.

## Kata sandi (ditambahkan setelah Tahap 4)

- **Ganti kata sandi sendiri** (semua peran): `/panel/akun/sandi`. Wajib sandi saat ini, sandi baru minimal 10 karakter berisi huruf dan angka dan berbeda dari yang lama. Setelah berhasil, **semua perangkat lain keluar** dan token "ingat saya" diputar.
- **Lupa kata sandi** (anggota dan staf): `/lupa-sandi`. Tautan dikirim lewat email, **berlaku 60 menit dan sekali pakai**. Jawaban halaman selalu sama baik email terdaftar, tidak terdaftar, maupun akun nonaktif (tidak membocorkan siapa yang terdaftar); akun nonaktif tidak dapat memakai tautan. Dibatasi 3 permintaan per 15 menit per email dan IP. Setelah atur ulang, semua sesi akun itu diakhiri.
- Semua kejadian masuk audit tanpa pernah memuat sandi (`auth.ganti_sandi`, `sandi.lupa_diminta`, `sandi.diatur_ulang`).
- **Email**: di `.env` saat ini `MAIL_MAILER=log`, artinya email **hanya ditulis ke `storage/logs/laravel.log`** (cukup untuk mencoba di komputer sendiri, tautan terbaca di log). Untuk dipakai sungguhan isi `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, dan `APP_URL` dengan alamat situs yang benar (tautan di email dibentuk dari `APP_URL`). Jangan memakai driver `log` di produksi karena tautan reset tersimpan di berkas log.
- Staf yang email-nya tidak dapat diakses tetap bisa dibantu Owner lewat Pengguna → Reset sandi.

## Pengujian

145 uji otomatis lulus (52 baru di tahap ini): alur lengkap di bawah dan di atas limit, penolakan, plafon, langkah ganda, IMEI ganda, kedaluwarsa, audit; 6 peran x tindakan terlarang (403); isolasi cabang dan anggota (404); berkas privat; verifikasi anggota; konten dan kontak per cabang; manajemen pengguna. Dicek juga di browser (daftar pengajuan dan form terima barang).

## Belum ada atau perlu keputusan Anda

- ~~Ganti dan lupa kata sandi belum ada~~ **Sudah ada** (lihat bagian "Kata sandi" di bawah).
- Verifikasi email dan 2FA untuk staf belum dirancang.
- Revisi taksiran, taksir ulang, dan pengembalian barang oleh peran selain Teller: Tahap 4 (taksir ulang terkait perpanjangan).
- Tampilan lama `resources/views/panel/anggota.blade.php` dan `panel/pengguna.blade.php` (Tahap 1) sudah tidak dipakai dan aman dihapus.
- Pencatatan kerusakan atau selisih barang saat dikembalikan belum ada.
