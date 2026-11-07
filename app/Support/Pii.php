<?php

namespace App\Support;

use RuntimeException;

/** Hash deterministik (HMAC-SHA256) untuk data identitas: NIK, IMEI/serial. */
class Pii
{
    public static function hash(string $nilai): string
    {
        $key = (string) config('linygadai.pii_hash_key');
        if ($key === '') {
            throw new RuntimeException('PII_HASH_KEY belum diisi di .env.');
        }
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true) ?: '';
        }

        // Normalisasi: tanpa spasi/tanda hubung, huruf kecil.
        $bersih = strtolower(preg_replace('/[\s\-]+/', '', $nilai));

        return hash_hmac('sha256', $bersih, $key);
    }
}
