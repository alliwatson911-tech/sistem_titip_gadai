<?php

namespace App\Models\Concerns;

use App\Services\Audit;

/** Mencatat created/updated/deleted ke audit_logs secara otomatis. */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($m) => Audit::log(self::aksi($m, 'dibuat'), $m, null, $m->getAttributes()));

        static::updated(function ($m) {
            $berubah = $m->getChanges();
            unset($berubah['updated_at']);
            if ($berubah === []) {
                return;
            }
            $sebelum = array_intersect_key($m->getRawOriginal(), $berubah);
            Audit::log(self::aksi($m, 'diubah'), $m, $sebelum, $berubah);
        });

        static::deleted(fn ($m) => Audit::log(self::aksi($m, 'dihapus'), $m, $m->getAttributes()));
    }

    private static function aksi($m, string $kejadian): string
    {
        return strtolower(class_basename($m)).'.'.$kejadian;
    }
}
