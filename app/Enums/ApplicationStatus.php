<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case MenungguBarang = 'menunggu_barang';
    case BarangDiterima = 'barang_diterima';
    case MenungguManager = 'menunggu_manager';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';
    case Kedaluwarsa = 'kedaluwarsa';
    case Dicairkan = 'dicairkan'; // diisi modul pencairan (Tahap 4)

    public function label(): string
    {
        return match ($this) {
            self::MenungguBarang => 'Menunggu barang',
            self::BarangDiterima => 'Menunggu taksiran',
            self::MenungguManager => 'Menunggu persetujuan Manager',
            self::Disetujui => 'Disetujui, siap cair',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
            self::Kedaluwarsa => 'Kedaluwarsa',
            self::Dicairkan => 'Dicairkan',
        };
    }

    /** Kelas warna lencana: proses | sukses | gagal | netral */
    public function nada(): string
    {
        return match ($this) {
            self::MenungguBarang, self::BarangDiterima, self::MenungguManager => 'proses',
            self::Disetujui, self::Dicairkan => 'sukses',
            self::Ditolak => 'gagal',
            self::Dibatalkan, self::Kedaluwarsa => 'netral',
        };
    }

    /** Status yang masih dihitung sebagai pengajuan terbuka milik anggota. */
    public static function terbuka(): array
    {
        return [self::MenungguBarang, self::BarangDiterima, self::MenungguManager, self::Disetujui];
    }
}
