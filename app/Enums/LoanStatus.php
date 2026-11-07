<?php

namespace App\Enums;

enum LoanStatus: string
{
    case Aktif = 'aktif';
    case Tenggang = 'tenggang';
    case SiapLelang = 'siap_lelang';
    case Dilelang = 'dilelang';
    case Ditebus = 'ditebus';
    case SelesaiLelang = 'selesai_lelang';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Tenggang => 'Masa tenggang',
            self::SiapLelang => 'Siap lelang',
            self::Dilelang => 'Sudah dilelang',
            self::Ditebus => 'Ditebus',
            self::SelesaiLelang => 'Selesai (lelang)',
        };
    }

    public function nada(): string
    {
        return match ($this) {
            self::Aktif => 'sukses',
            self::Tenggang => 'proses',
            self::SiapLelang, self::Dilelang => 'gagal',
            self::Ditebus, self::SelesaiLelang => 'netral',
        };
    }

    /** Gadai yang masih dapat dibayar (perpanjang/cicil/tebus). */
    public static function dapatDibayar(): array
    {
        return [self::Aktif, self::Tenggang, self::SiapLelang];
    }
}
