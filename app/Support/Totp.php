<?php

namespace App\Support;

/**
 * TOTP (RFC 6238, HMAC-SHA1, 30 detik, 6 digit) tanpa pustaka luar; cocok dengan Google Authenticator, Authy, dsb.
 * Pemakaian ulang kode dicegah dengan menyimpan langkah waktu terakhir yang dipakai.
 */
final class Totp
{
    private const ABJAD = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function buatRahasia(int $byte = 20): string
    {
        return self::kodeBase32(random_bytes($byte));
    }

    public static function kodeBase32(string $biner): string
    {
        $bit = '';
        foreach (str_split($biner) as $c) {
            $bit .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $hasil = '';
        foreach (str_split($bit, 5) as $potong) {
            $hasil .= self::ABJAD[bindec(str_pad($potong, 5, '0', STR_PAD_RIGHT))];
        }

        return $hasil;
    }

    private static function dariBase32(string $s): string
    {
        $bit = '';
        foreach (str_split(strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $s))) as $c) {
            $bit .= str_pad(decbin((int) strpos(self::ABJAD, $c)), 5, '0', STR_PAD_LEFT);
        }
        $biner = '';
        foreach (str_split($bit, 8) as $potong) {
            if (strlen($potong) === 8) {
                $biner .= chr(bindec($potong));
            }
        }

        return $biner;
    }

    public static function langkah(?int $waktu = null): int
    {
        return intdiv($waktu ?? time(), 30);
    }

    public static function kode(string $rahasia, int $langkah, int $digit = 6): string
    {
        $hash = hash_hmac('sha1', pack('J', $langkah), self::dariBase32($rahasia), true);
        $o = ord($hash[19]) & 0x0F;
        $angka = ((ord($hash[$o]) & 0x7F) << 24) | (ord($hash[$o + 1]) << 16) | (ord($hash[$o + 2]) << 8) | ord($hash[$o + 3]);

        return str_pad((string) ($angka % (10 ** $digit)), $digit, '0', STR_PAD_LEFT);
    }

    /**
     * @return int|null langkah waktu yang cocok (untuk disimpan sebagai "terakhir dipakai"), atau null bila salah/sudah dipakai.
     *                  Toleransi satu langkah (30 detik) sebelum/sesudah untuk selisih jam.
     */
    public static function verifikasi(string $rahasia, string $kode, ?int $langkahTerakhir = null, ?int $waktu = null): ?int
    {
        $kode = preg_replace('/\s+/', '', $kode);
        if (! preg_match('/^\d{6}$/', $kode)) {
            return null;
        }
        $sekarang = self::langkah($waktu);
        foreach ([0, -1, 1] as $geser) {
            $l = $sekarang + $geser;
            if ($langkahTerakhir !== null && $l <= $langkahTerakhir) {
                continue; // kode untuk langkah ini (atau sebelumnya) sudah pernah dipakai
            }
            if (hash_equals(self::kode($rahasia, $l), $kode)) {
                return $l;
            }
        }

        return null;
    }

    public static function uri(string $rahasia, string $akun, string $penerbit = 'linygadai'): string
    {
        return 'otpauth://totp/'.rawurlencode("$penerbit:$akun").'?secret='.$rahasia.'&issuer='.rawurlencode($penerbit).'&algorithm=SHA1&digits=6&period=30';
    }
}
