# Matriks hak akses (dibuat otomatis)

Dibuat dari `config/permissions.php` oleh `php artisan izin:tabel`. Jangan diedit manual; ubah konfigurasinya lalu buat ulang.
Cakupan data (cabang sendiri / milik sendiri) ditegakkan terpisah oleh global scope `BelongsToBranch`.

| Izin | Owner | Manager Cabang | Admin Cabang | Teller | Analis | Anggota |
|---|:-:|:-:|:-:|:-:|:-:|:-:|
| `pengguna.kelola` | ✓ | — | — | — | — | — |
| `cabang.lihat` | ✓ | ✓ | — | — | — | — |
| `cabang.ubah` | ✓ | — | — | — | — | — |
| `kebijakan.lihat` | ✓ | ✓ | ✓ | ✓ | ✓ | — |
| `kebijakan.ubah` | ✓ | — | — | — | — | — |
| `produk.ubah_konten` | ✓ | — | ✓ | — | — | — |
| `anggota.lihat` | ✓ | ✓ | ✓ | ✓ | ✓ | — |
| `anggota.kelola` | ✓ | — | ✓ | — | — | — |
| `konten.lihat` | ✓ | ✓ | ✓ | — | — | — |
| `konten.kelola` | ✓ | — | ✓ | — | — | — |
| `cabang.kontak` | ✓ | — | ✓ | — | — | — |
| `konten.situs` | ✓ | — | — | — | — | — |
| `profil.ubah` | — | — | — | — | — | ✓ |
| `pengajuan.buat` | — | — | — | ✓ | — | ✓ |
| `pengajuan.lihat` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `jaminan.terima` | — | — | — | ✓ | — | — |
| `jaminan.keluarkan` | — | — | — | ✓ | — | — |
| `taksiran.buat` | — | — | — | — | ✓ | — |
| `taksiran.lihat` | ✓ | ✓ | — | ✓ | ✓ | ✓ |
| `persetujuan.putuskan` | ✓ | ✓ | — | — | — | — |
| `pencairan.catat` | — | — | — | ✓ | — | — |
| `pembayaran.catat` | — | — | — | ✓ | — | — |
| `pembayaran.verifikasi` | — | ✓ | — | ✓ | — | — |
| `pembayaran.batalkan` | — | ✓ | — | — | — | — |
| `lelang.kelola` | ✓ | ✓ | — | — | — | — |
| `lelang.catat` | — | ✓ | — | ✓ | — | — |
| `gadai.lihat` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `laporan.cabang` | ✓ | ✓ | — | — | — | — |
| `laporan.harian` | — | — | — | ✓ | — | — |
| `laporan.konsolidasi` | ✓ | — | — | — | — | — |
| `audit.lihat` | ✓ | ✓ | — | — | — | — |
