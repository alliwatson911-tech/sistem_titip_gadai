# linygadai — Panduan Tahap 1 (Fondasi)

Isi tahap ini: Laravel 13.40 (PHP 8.5), 19 tabel (semua migrasi tahap 0 sudah dibuat), peran dan cabang, login/registrasi, izin berbasis matriks, isolasi data per cabang, jejak audit, seeder, dan 68 uji otomatis.

## Menjalankan

Prasyarat: XAMPP dengan MySQL/MariaDB menyala, PHP di `C:\XAMPP\php`, Composer.

```powershell
cd C:\XAMPP\htdocs\linygadai
C:\XAMPP\php\php.exe artisan migrate:fresh --seed
C:\XAMPP\php\php.exe artisan serve
```

- `--seed` mencetak **kata sandi acak** tiap akun satu kali di konsol. Catat saat itu juga; tidak disimpan di mana pun. Menjalankan `db:seed` lagi tidak mengubah sandi akun yang sudah ada.
- Buka http://127.0.0.1:8000 .
- `.env` (tidak masuk repositori): database **`linygadai_app`** (bukan `linygadai`, yang di komputer ini dipakai aplikasi lain; lihat docs/02), pengguna `root` tanpa sandi (bawaan XAMPP, **ubah untuk server sungguhan**), `PII_HASH_KEY` sudah diisi acak. **Jangan hilangkan kunci ini**: hash NIK lama tak bisa dicocokkan lagi bila kunci berganti.
- Pertama kali di komputer lain: `composer install`, salin `.env.example` ke `.env`, `php artisan key:generate`, lalu isi `DB_*`, `PII_HASH_KEY` (`base64:` + 32 byte acak), dan buat database `linygadai`.

## Akun seeder (email; sandi dari konsol)

| Peran | Cabang A | Cabang B |
|---|---|---|
| Owner | `owner@linygadai.test` (semua cabang) | |
| Manager | `manager.a@…` | `manager.b@…` |
| Admin | `admin.a@…` | `admin.b@…` |
| Teller | `teller.a@…` | `teller.b@…` |
| Analis | `analis.a@…` | `analis.b@…` |
| Anggota contoh | `anggota.a@…` | `anggota.b@…` |

Domain `.test` dicadangkan dan tidak pernah mengirim surat. NIK anggota contoh palsu.

## Pengujian

```powershell
C:\XAMPP\php\php.exe artisan test
```

Memakai database `linygadai_test` (sudah dibuat). Mencakup: login/keluar/batas percobaan/akun nonaktif/NIK terenkripsi/peran tak bisa disetel dari form, 6 peran x halaman terlindungi, 25 titik kritis matriks izin, isolasi cabang dan anggota, audit tak bisa diubah.

## Yang ada di kode

| Bagian | Berkas |
|---|---|
| Migrasi 19 tabel | `database/migrations/` |
| Peran | `app/Enums/Role.php` |
| Matriks izin (sumber tunggal) | `config/permissions.php` |
| Gate dari matriks | `app/Providers/AppServiceProvider.php` |
| Isolasi cabang/anggota | `app/Models/Concerns/BelongsToBranch.php` |
| Audit otomatis dan manual | `app/Models/Concerns/Auditable.php`, `app/Services/Audit.php`, `app/Models/AuditLog.php` |
| Hash NIK/IMEI | `app/Support/Pii.php` |
| Auth | `app/Http/Controllers/Auth/` |
| Panel awal | `app/Http/Controllers/Panel/`, `resources/views/panel/` |
| Seeder | `database/seeders/` |

## Catatan keamanan yang sudah ditegakkan

- Kata sandi di-hash; kunci dan kata sandi hanya di `.env` atau dibuat acak saat seeding.
- `role`, `branch_id`, `status` pengguna dan `status`/`pokok_saldo` transaksi tidak bisa diisi massal dari input.
- NIK terenkripsi (`nik_enc`) + HMAC (`nik_hash`); tampilan memakai NIK tersamar.
- Login: pesan galat tunggal, batas 5 percobaan/menit per email+IP, sesi diregenerasi, akun nonaktif langsung keluar di tengah sesi.
- Header keamanan dasar; halaman terautentikasi `no-store`.
- Audit log tidak bisa diubah/dihapus lewat aplikasi (belum ada pembatasan di level database; lihat daftar belum selesai).

## Belum selesai / disengaja ditunda

- Izin ke tingkat catatan (Policy per model) untuk modul transaksi: Tahap 3-4.
- Pembatasan `UPDATE/DELETE` pada `audit_logs` di level database (hak akses pengguna MySQL terpisah): Tahap 5.
- Pengguna manajemen (tambah/ubah/nonaktifkan oleh Owner) baru daftar baca saja.
- Unggah KTP (disk privat), verifikasi anggota oleh Admin: Tahap 3.
- Pemulihan kata sandi, verifikasi email, dan 2FA staf: belum dirancang; perlu keputusan Anda.
- Fon, sistem desain, dan halaman publik: Tahap 2 (gaya sementara memakai font sistem).
