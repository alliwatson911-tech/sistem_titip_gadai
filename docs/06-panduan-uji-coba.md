# 06 — Panduan uji coba pengguna (prototype)

Dokumen ini untuk **penguji**: apa yang sudah terisi, akun apa yang dipakai, dan skenario per peran.
Semua isi situs pada mode uji coba adalah **karangan** (data fiktif) dan ditandai spanduk "VERSI UJI COBA".

## 1. Menyiapkan lingkungan uji

```powershell
cd C:\XAMPP\htdocs\linygadai
php artisan migrate:fresh --seed        # reset DB uji + isi data fiktif (HAPUS semua data lama)
php artisan serve --host=127.0.0.1 --port=8000
```

- Pastikan `.env` berisi `LINYGADAI_MODE_UJI=true` (spanduk + data demo). Data demo hanya dibuat bila nilai ini `true`.
- Seeder mencetak **kata sandi acak sekali** untuk 11 akun staf/anggota dan 8 anggota demo. Catat saat itu juga; tidak disimpan
  di kode. Pada mesin pengembang, hasilnya tersimpan lokal di `docs/akun-uji.txt` — **hapus berkas itu sebelum dibagikan**.
- Email keluar ke `storage/logs/laravel.log` (`MAIL_MAILER=log`). Untuk melihat tautan verifikasi/reset, buka log.
- Agar email pengingat dan status otomatis jalan: `scripts\daftar-scheduler.ps1` (Administrator) atau jalankan manual
  `php artisan gadai:perbarui-status` dan `php artisan gadai:kirim-pengingat`.

## 2. Akun

| Peran | Email | Cabang |
|---|---|---|
| Owner | owner@linygadai.test | semua |
| Manager | manager.a@ / manager.b@linygadai.test | A / B |
| Admin | admin.a@ / admin.b@linygadai.test | A / B |
| Teller | teller.a@ / teller.b@linygadai.test | A / B |
| Analis | analis.a@ / analis.b@linygadai.test | A / B |
| Anggota contoh | anggota.a@ / anggota.b@linygadai.test | A / B |
| Anggota demo (fiktif, punya riwayat gadai) | siti, andi, dewi, budi (A); rahmat, nur, yusuf, lina (B) `.demo@linygadai.test` | — |

Masuk lewat tombol **Masuk** di beranda (`/#akun`). Sandi: lihat keluaran seeder.

## 3. Data demo yang sudah ada (gunakan untuk melihat semua status)

| Skenario | Anggota | Cabang | Status |
|---|---|---|---|
| Gadai baru | Siti | A | Aktif (5 hari) |
| Mendekati jatuh tempo | Siti | A | Aktif, jatuh tempo ±3 hari |
| Terlambat | Andi | A | Tenggang |
| Siap lelang + lelang terjadwal | Andi | A | Siap lelang |
| Sudah ditebus, barang keluar | Siti | A | Ditebus |
| Perpanjang lalu cicil | Rahmat | B | Aktif |
| Lelang laku, kelebihan dibayar | Nur | B | Selesai lelang |
| Taksir ulang (plafon turun) | Lina | B | Aktif, wajib setor selisih |
| Pengajuan: menunggu barang / barang diterima / menunggu Manager / disetujui / ditolak | Andi, Siti, Rahmat, Nur, Lina | A/B | Berbagai |
| Anggota calon / ditolak / email belum diverifikasi | Dewi, Budi, Yusuf | A/B | — |

## 4. Skenario per peran

### Pengunjung (tanpa akun)
1. Buka beranda: sambutan, billboard pengumuman, FAQ/Informasi (accordion), produk, panduan, simulasi, tentang kami (alamat, peta, sosmed).
2. Simulasi: ganti produk/taksiran/persentase/lama titip; total tebus berubah. Bandingkan 30 vs 120 hari.
3. Daftar akun → cek email (di log) → klik tautan verifikasi.
4. Coba kata sandi salah 6x di Masuk: harus muncul pembatasan, pesan galat tidak membocorkan apakah email terdaftar.

### Anggota
1. Masuk sebagai `siti.demo`: lihat gadai aktif, jatuh tempo, tagihan perpanjang/cicil/tebus.
2. Ajukan pengajuan baru (produk + nominal). Akun `yusuf.demo` (email belum diverifikasi) harus **ditolak** mengajukan.
3. Coba buka gadai milik anggota lain lewat URL: harus 404/403.
4. Lupa kata sandi → ubah kata sandi → perangkat lain keluar.

### Teller (Cabang A)
1. Pengajuan `menunggu barang`: terima barang (cocokkan identitas, foto, segel).
2. Cairkan pengajuan `disetujui`; cetak/lihat nota.
3. Catat pembayaran: perpanjang, cicil (coba di bawah minimum → ditolak), tebus; keluarkan barang (coba penebus kuasa + unggah surat kuasa).
4. Buka gadai: tombol pesan WhatsApp (tautan, bukan pengiriman otomatis).

### Analis
1. Pengajuan `barang diterima`: isi taksiran + daftar periksa + rekomendasi.
2. **Taksir ulang** pada gadai aktif (Lina, Cabang B): turunkan taksiran → plafon turun, anggota wajib cicil selisih sebelum perpanjang.

### Manager
1. Pengajuan di atas limit (Rp5.000.000, Rahmat): setujui/tolak dengan catatan.
2. Lelang: jadwalkan lelang untuk gadai `siap lelang`, catat hasil (laku/tidak), bayarkan kelebihan ke anggota.
3. Aktifkan **2FA** di Akun → Keamanan 2FA (aplikasi autentikator), simpan kode pemulihan.

### Admin
1. Verifikasi anggota `calon` (Dewi): lihat KTP, setujui/tolak.
2. Kelola pengumuman dan FAQ cabang.

### Owner
1. Laporan lintas cabang (ringkasan, ekspor CSV), jejak audit, pengaturan kebijakan (syarat berlaku untuk gadai **baru**; gadai lama memakai snapshot saat akad).
2. Cek bahwa Owner/Manager diminta mengaktifkan 2FA bila `LINYGADAI_2FA_WAJIB=true`.

## 5. Hal yang sengaja diuji pada isolasi akses
- Teller/Analis Cabang A tidak melihat data Cabang B (URL langsung → 404).
- Anggota hanya melihat datanya; tidak ada menu staf.
- Foto KTP/barang tidak dapat diakses tanpa login (tidak ada `/storage/...`).
- Jejak audit tidak dapat diubah/dihapus (juga lewat SQL; ada trigger).

## 6. Yang BELUM dan tidak diuji di prototype ini
- Pembayaran online (gateway): pembayaran dicatat teller di loket (manual), sesuai rancangan.
- Pembukuan/akuntansi penuh, tanda tangan digital, integrasi WhatsApp otomatis.
- Email nyata (SMTP): uji coba memakai log. Isi SMTP sebelum go-live.
- Angka biaya/LTV/sewa/tenggang adalah **asumsi** yang harus disahkan pengelola; **izin usaha belum diverifikasi** (teks pada Tentang Kami adalah contoh).
- Beban/performansi di bawah banyak pengguna serentak.

## 7. Cara melaporkan temuan
Catat: peran, halaman/URL, langkah, hasil yang diharapkan vs yang terjadi, tangkapan layar. Jangan menyertakan data pribadi nyata.
