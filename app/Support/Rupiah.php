<?php

namespace App\Support;

class Rupiah
{
    /** "3.000.000" atau "Rp 3000000" -> "3000000" (hanya digit); null bila kosong. */
    public static function digit(mixed $nilai): ?string
    {
        $d = preg_replace('/\D/', '', (string) $nilai);

        return $d === '' ? null : $d;
    }

    /** 2500000 -> "Rp 2.500.000" */
    public static function format(int $angka): string
    {
        return 'Rp '.number_format($angka, 0, ',', '.');
    }

    /** 100000 -> "Rp 100 rb", 25000000 -> "Rp 25 jt", 1500000 -> "Rp 1,5 jt" */
    public static function ringkas(int $angka): string
    {
        if ($angka >= 1_000_000) {
            return 'Rp '.rtrim(rtrim(number_format($angka / 1_000_000, 1, ',', ''), '0'), ',').' jt';
        }
        if ($angka >= 1_000) {
            return 'Rp '.rtrim(rtrim(number_format($angka / 1_000, 1, ',', ''), '0'), ',').' rb';
        }

        return 'Rp '.$angka;
    }
}
