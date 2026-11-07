<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Audit
{
    /** Atribut yang tidak pernah ditulis ke audit. */
    private const SENSITIF = ['password', 'remember_token', 'nik_enc', 'imei_serial_enc', 'nik_penebus_enc'];

    public static function log(string $aksi, ?Model $subject = null, ?array $sebelum = null, ?array $sesudah = null, ?int $userId = null): AuditLog
    {
        $user = Auth::user();
        $request = app()->bound('request') ? request() : null;

        return AuditLog::create([
            'user_id' => $userId ?? $user?->id,
            'branch_id' => $subject?->branch_id ?? $user?->branch_id,
            'aksi' => $aksi,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'sebelum' => $sebelum ? self::bersihkan($sebelum) : null,
            'sesudah' => $sesudah ? self::bersihkan($sesudah) : null,
            'ip' => $request?->ip(),
            'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
        ]);
    }

    public static function bersihkan(array $data): array
    {
        return array_diff_key($data, array_flip(self::SENSITIF));
    }
}
