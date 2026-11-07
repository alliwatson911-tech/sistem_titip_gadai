<?php

return [
    // Kunci HMAC untuk hash NIK/IMEI (pencarian & cegah duplikat tanpa menyimpan nilai asli). Isi di .env.
    'pii_hash_key' => env('PII_HASH_KEY'),

    // Batas percobaan masuk per menit (per email+IP).
    'login_attempts_per_minute' => 5,

    // Autentikasi dua faktor (TOTP) untuk staf. Bila diwajibkan, peran di 'peran_2fa' dipaksa mengaktifkannya sebelum memakai panel.
    'wajib_2fa' => (bool) env('LINYGADAI_2FA_WAJIB', false),
    'peran_2fa' => ['owner', 'manager'],

    // Mode uji coba: menampilkan spanduk "data fiktif" di seluruh situs dan mengisi data demo saat seeding.
    'mode_uji' => (bool) env('LINYGADAI_MODE_UJI', false),
];
