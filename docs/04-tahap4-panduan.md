# linygadai — Panduan Tahap 4 (Uang: pencairan sampai lelang)

Semua logika uang dan status gadai ada di satu tempat: [`app/Services/LoanService.php`](../app/Services/LoanService.php), memakai rumus dari `LoanCalculator` (yang sama dengan simulasi publik). Controller hanya memvalidasi masukan dan memeriksa izin.

## Alur

```
disetujui --cairkan(teller)--> AKTIF --lewat jatuh tempo--> TENGGANG --lewat tenggang--> SIAP LELANG --jadwal+hasil--> DILELANG --kelebihan dibayar--> SELESAI LELANG
                                  |                              |                            |                         \--kekurangan/tanpa sisa--> SELESAI LELANG
                                  +-- perpanjang / cicil (tetap AKTIF, jatuh tempo maju) --------------+
                                  +-- tebus --> DITEBUS --keluarkan barang(teller)--> barang dikeluarkan
```
Perpindahan `aktif → tenggang → siap_lelang` dijalankan perintah `gadai:perbarui-status` (terjadwal 00:10 setiap hari). Pengajuan kedaluwarsa (Tahap 3) berjalan 01:00. Di server, jalankan `php artisan schedule:run` setiap menit.

## Rumus dan aturan (semua [ASUMSI] yang dapat diubah lewat produk dan `policy_settings`)

Contoh: pokok Rp 2.000.000, sewa modal 0,15%/hari (Rp 3.000/hari), administrasi 1% (Rp 20.000), tenor 30 hari.

| Kejadian | Yang dibayar | Akibat |
|---|---|---|
| **Pencairan** | tidak ada biaya di muka; dana cair = pokok | jatuh tempo = akad + 30 hari; tenggang 3 hari setelahnya |
| **Perpanjang** | sewa 30 hari (Rp 90.000) + administrasi (Rp 20.000) = Rp 110.000 | jatuh tempo maju 30 hari **dari jatuh tempo lama**; pokok tetap; maksimal 3 kali berturut-turut |
| **Cicil** | sewa + administrasi + setoran pokok (minimal Rp 50.000, kurang dari saldo) | pokok berkurang; jatuh tempo maju 30 hari; hitungan perpanjangan **disetel ulang ke 0** |
| **Tebus sebelum jatuh tempo** | pokok + sewa per blok 15 hari (10 hari berjalan = 15 hari = Rp 45.000) + administrasi | status ditebus; barang diserahkan lewat langkah terpisah |
| **Tebus saat jatuh tempo** | pokok + sewa 30 hari + administrasi | sama |
| **Terlambat** | sewa harian tambahan (terlambat 2 hari: +Rp 6.000) + denda uang bila `denda_harian` > 0 (awal: 0) | sewa tetap berjalan sampai lunas atau lelang |
| **Terlambat terlalu lama** | jatuh tempo baru sudah lewat → hanya tebus | |

- Angka diambil dari **salinan syarat saat akad** (`snapshot_syarat`), jadi mengubah tarif produk tidak mengubah gadai yang sedang berjalan.
- **Transfer manual**: bukti diunggah; pembayaran baru berlaku setelah **petugas lain** (Manager atau teller lain) menyetujui; selama menunggu, tidak boleh ada pembayaran lain pada gadai itu. Jika keadaan gadai berubah sebelum verifikasi, verifikasi ditolak.
- **Pembalikan** (Manager, alasan wajib): hanya pembayaran sah **terakhir**, dan hanya sebelum barang keluar atau dilelang. Keadaan gadai dipulihkan dari catatan "sebelum" yang disimpan pada tiap pembayaran.
- **Pengeluaran barang**: wajib centang nota asli dan KTP asli diperiksa; oleh orang lain wajib surat kuasa (file) dan NIK penebus (terenkripsi).

## Lelang

1. Saat tenggang berakhir, gadai menjadi **siap lelang** dan tercatat tanggal pemberitahuan. Anggota melihat peringatan di panelnya dan **masih dapat menebus, memperpanjang, atau mencicil**, yang otomatis membatalkan antrean lelang.
2. Manager/Owner menjadwalkan: tanggal paling cepat = pemberitahuan + 7 hari; harga limit tidak boleh di bawah saldo pokok.
3. Pada atau setelah tanggal lelang, Manager atau teller mencatat hasil. **Laku**: kewajiban (pokok + sewa sampai tanggal lelang + administrasi) dan biaya lelang dilunasi dari hasil; **selisih positif adalah hak anggota** (dibayarkan teller, lalu status selesai); **selisih negatif tidak ditagihkan** ke anggota. **Tidak laku**: kembali ke antrean untuk dijadwalkan ulang.
4. Seluruh langkah masuk audit dan riwayat status.

**[VERIFIKASI]** Aturan lelang dan pembagian hasil (termasuk larangan perusahaan mengambil keuntungan dari lelang jaminan, yang saya temukan dari pemberitaan OJK) wajib dicek ke konsultan hukum dan teks resmi peraturan sebelum dipakai.

## Pemberitahuan email ke anggota (ditambahkan setelah Tahap 5)

Perintah `gadai:kirim-pengingat` berjalan **setiap hari 08:00** lewat scheduler (setelah `gadai:perbarui-status` 00:10) dan mengirim email ke alamat akun anggota:

| Jenis | Kapan | Isi utama |
|---|---|---|
| `h7` | 2–7 hari sebelum jatuh tempo (sekali per jatuh tempo; menyusul bila scheduler sempat mati) | tanggal jatuh tempo, saldo, perkiraan biaya perpanjang dan tebus |
| `h1` | 0–1 hari sebelum jatuh tempo | sama, "jatuh tempo besok/hari ini" |
| `tenggang` | gadai masuk masa tenggang | batas tenggang, sewa modal tetap berjalan |
| `siap_lelang` | gadai masuk proses lelang | **masih dapat menebus/memperpanjang/mencicil sebelum lelang**; lelang paling cepat tanggal X; kelebihan hasil lelang hak anggota |
| `jadwal_lelang` | Manager menetapkan tanggal lelang | tanggal lelang dan hak untuk masih menebus |

- **Idempoten**: tabel `loan_notices` menyimpan satu baris per (gadai, jenis, tanggal dasar). Perintah dijalankan berapa kali pun, tidak ada email ganda. Setelah perpanjang, jatuh tempo baru memulai siklus pengingat baru.
- **Gagal kirim** (mis. SMTP mati) dicatat dan **dicoba lagi** pada jalannya berikutnya, maksimal 3 percobaan. Anggota nonaktif atau tanpa email dicatat "dilewati".
- **Bukti**: staf melihat daftar pemberitahuan (jenis, status, waktu) di halaman gadai. Ini penting untuk membuktikan pemberitahuan lelang sudah dikirim.
- Email **tidak memuat NIK atau data pribadi lain**, hanya nomor nota, barang, saldo, dan perkiraan biaya, plus peringatan bahwa petugas tidak pernah meminta kata sandi.
- Agar benar-benar terkirim, SMTP harus diisi di `.env` (lihat `.env.production.example`). Dengan `MAIL_MAILER=log` email hanya ditulis ke log.
- Menjalankan manual: `php artisan gadai:kirim-pengingat`.

## Hak akses yang ditegakkan

Pencairan, pembayaran, dan pengeluaran barang hanya Teller. Verifikasi transfer: Manager dan Teller **selain pencatatnya**. Pembalikan: Manager. Jadwal lelang: Manager dan Owner. Hasil dan pembayaran kelebihan: Manager dan Teller. Anggota hanya melihat gadai, tagihan, dan riwayat pembayarannya sendiri; kwitansi, bukti, dan riwayat status hanya untuk staf cabang yang sama. Gadai lintas cabang memberi 404.

## Pengujian

178 uji otomatis lulus (33 baru): hitungan manual tebus dini/tepat/terlambat, perpanjang dari jatuh tempo lama, cicil dan penyetelan ulang, batas perpanjangan, transfer dan verifikasi silang, pembalikan, scheduler (termasuk idempoten), pembayaran saat tenggang dan siap lelang, lelang dengan kelebihan/kekurangan/tidak laku, hak akses per peran, isolasi, rekonsiliasi (pokok dibayar + saldo = pokok awal), audit. Halaman gadai juga dicek di browser.

## Belum ada

- **Penaksiran ulang saat perpanjang** (rancangan Tahap 0 menyebutnya) dan **penebusan sebagian barang**: belum dibangun.
- **WhatsApp/SMS** untuk jatuh tempo dan lelang: belum ada (email sudah ada, lihat bagian "Pemberitahuan email" di bawah).
- **Buku kas harian dan rekonsiliasi kas teller**: laporan menyusul di Tahap 5.
- **Tampilan berkas** surat kuasa penebus dan bukti pencairan transfer oleh staf: tersimpan privat, belum ada halaman untuk membukanya.
- **Pembayaran anggota mandiri** (payment gateway) tidak ada; semua pembayaran lewat teller.
- Ganti/lupa kata sandi: sudah ditutup (lihat docs/03, bagian "Kata sandi"). Verifikasi email dan 2FA staf belum ada.
