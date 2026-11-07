<?php

namespace App\Support;

/** Ekspor CSV aman: sel yang diawali = + - @ (atau tab/CR) diberi tanda petik agar tidak dieksekusi sebagai rumus di spreadsheet. */
class Csv
{
    public static function sel(mixed $nilai): string
    {
        $s = (string) $nilai;

        return $s !== '' && preg_match('/^[=+\-@\t\r]/', $s) ? "'".$s : $s;
    }

    /** Tulis seluruh baris ke aliran dengan BOM UTF-8 (agar Excel membaca aksara dengan benar). */
    public static function tulis(array $baris, $aliran): void
    {
        fwrite($aliran, "\xEF\xBB\xBF");
        foreach ($baris as $b) {
            fputcsv($aliran, array_map([self::class, 'sel'], $b));
        }
    }
}
