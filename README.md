# linygadai

Situs publik dan sistem manajemen perusahaan **titip gadai** barang elektronik: dua cabang (A dan B) dan satu kantor pusat, enam peran (Owner, Manager, Admin, Teller, Analis, Anggota). Laravel 13, Blade, MariaDB/MySQL.

> **Status**: semua tahap rancangan selesai (fondasi, halaman publik, modul per peran, uang dan lelang, laporan, audit, hardening). **Belum dipakai produksi.** Semua angka bisnis adalah ASUMSI dan semua klaim hukum perlu diverifikasi. Baca [docs/05-checklist-pengujian.md](docs/05-checklist-pengujian.md) bagian "Sebelum go-live".

## Dokumen

| Berkas | Isi |
|---|---|
| [docs/00-rancangan-tahap0.md](docs/00-rancangan-tahap0.md) | Asumsi, ERD, alur, matriks awal, arah desain |
| [docs/01-tahap1-panduan.md](docs/01-tahap1-panduan.md) | Fondasi: auth, peran, cabang, audit |
| [docs/02-tahap2-panduan.md](docs/02-tahap2-panduan.md) | Halaman publik dan simulasi |
| [docs/03-tahap3-panduan.md](docs/03-tahap3-panduan.md) | Pengajuan, anggota, konten, kata sandi |
| [docs/04-tahap4-panduan.md](docs/04-tahap4-panduan.md) | Pencairan, perpanjang, cicil, tebus, lelang |
| [docs/05-checklist-pengujian.md](docs/05-checklist-pengujian.md) | Checklist fungsional, hak akses, keamanan, responsif; daftar go-live |
| [docs/06-panduan-uji-coba.md](docs/06-panduan-uji-coba.md) | Panduan penguji: akun, data demo fiktif, skenario per peran, batasan prototype |
| [docs/matriks-akses.md](docs/matriks-akses.md) | Matriks izin x peran (dibuat otomatis) |

## Memasang di komputer ini (Windows + XAMPP)

Prasyarat: XAMPP (MariaDB menyala), PHP 8.3+ (di sini `C:\XAMPP\php\php.exe`), Composer, Node.js hanya bila ingin mengganti font.

```powershell
cd C:\XAMPP\htdocs\linygadai
composer install
copy .env.example .env
C:\XAMPP\php\php.exe artisan key:generate
```

Edit `.env`:

- `DB_CONNECTION=mariadb`, `DB_DATABASE=linygadai_app` (buat database kosong itu dulu; **jangan** memakai nama yang sudah dipakai aplikasi lain), `DB_USERNAME`, `DB_PASSWORD`.
- `PII_HASH_KEY=base64:` diikuti 32 byte acak berenkode base64 (mis. `php -r "echo 'base64:'.base64_encode(random_bytes(32));"`). **Jangan diganti setelah ada data.**
- Opsional: `PUSAT_ALAMAT`, `PUSAT_WHATSAPP`, `PUSAT_EMAIL`, `PUSAT_LAT`, `PUSAT_LNG`, `PUSAT_JAM` (juga `A_...`, `B_...`) untuk kontak di situs.

```powershell
C:\XAMPP\php\php.exe artisan migrate --seed
C:\XAMPP\php\php.exe artisan serve
```

`--seed` membuat 3 cabang, 11 akun contoh (kata sandi acak **dicetak sekali** di konsol; catat), produk, kebijakan, dan konten awal. Buka http://127.0.0.1:8000.

**Mode uji coba**: dengan `LINYGADAI_MODE_UJI=true` di `.env`, `--seed` juga mengisi **data fiktif** (8 anggota demo, gadai di semua status, lelang, pengumuman, FAQ, kontak/sosmed contoh) dan situs menampilkan spanduk "VERSI UJI COBA". Skenario: [docs/06-panduan-uji-coba.md](docs/06-panduan-uji-coba.md). **Matikan sebelum produksi** (`sistem:periksa` menandainya).

Opsi `.env` lain: `LINYGADAI_2FA_WAJIB=true` (Owner/Manager wajib 2FA TOTP), `BACKUP_PASSWORD` (sandi ZIP cadangan), `MYSQLDUMP_PATH` (bila `mysqldump` tidak otomatis ditemukan).

## Menjalankan uji

Butuh database `linygadai_test` (kosong). Uji memakai `RefreshDatabase`, jadi isinya dihapus tiap kali.

```powershell
C:\XAMPP\php\php.exe artisan test
```

270 uji otomatis: auth, matriks izin, isolasi cabang, halaman publik, alur pengajuan, uang dan lelang, kata sandi, pengingat email, laporan, audit, keamanan, 2FA/TOTP, verifikasi email, taksir ulang, surat kuasa, cadangan.

## Perintah operasional

| Perintah | Fungsi |
|---|---|
| `php artisan schedule:run` | **Harus dijalankan tiap menit** (Task Scheduler/cron). Menggerakkan kedaluwarsa pengajuan (01:00) dan status gadai (00:10) |
| `php artisan gadai:kirim-pengingat` | Email pengingat jatuh tempo/tenggang/lelang ke anggota (terjadwal 08:00, aman dijalankan berulang) |
| `php artisan sistem:cadangan` | Cadangan database + berkas privat ke `storage/backups` (ZIP, AES-256 bila `BACKUP_PASSWORD` diisi; terjadwal 02:00; salin ke lokasi lain). Tidak menyertakan `.env`: simpan `APP_KEY` dan `PII_HASH_KEY` terpisah |
| `scripts\daftar-scheduler.ps1` | Mendaftarkan Task Scheduler Windows untuk `schedule:run` (jalankan sebagai Administrator) |
| `php artisan sistem:periksa` | Pemeriksa konfigurasi keamanan dan operasional (kode keluar 1 bila ada yang gagal) |
| `php artisan izin:tabel --tulis=docs\matriks-akses.md` | Membuat ulang matriks hak akses dari `config/permissions.php` |

## Struktur folder (ringkas)

```
app/
  Enums/            Role, ApplicationStatus, LoanStatus
  Models/           model + Concerns/{BelongsToBranch,Auditable}
  Services/         ApplicationFlow, LoanService, LoanCalculator, LaporanService, Audit, Situs
  Http/Controllers/ Auth/, Panel/, Publik/
  Http/Middleware/  EnsureUserIsActive, SecurityHeaders (CSP)
  Console/Commands/ jadwal status, sistem:periksa, izin:tabel
  Notifications/    AturUlangSandi
config/permissions.php   matriks izin (sumber tunggal)
database/{migrations,seeders,factories}
resources/views/{layouts,publik,panel,auth,components}
public/{css,js,fonts}    tanpa langkah build
storage/app/private/     KTP, foto barang, bukti, surat kuasa (tidak punya URL publik)
tests/{Feature,Unit}
docs/
```

## Prinsip yang dijaga

Uang bilangan bulat rupiah; semua rumus di `LoanCalculator`; status hanya berubah lewat `ApplicationFlow` dan `LoanService`; data per cabang dan per anggota dibatasi global scope; kata sandi di-hash; NIK, IMEI, dan NIK penebus terenkripsi (+ HMAC untuk pencarian); berkas pribadi hanya lewat controller berotorisasi; `audit_logs` tidak dapat diubah atau dihapus (model dan trigger database).
