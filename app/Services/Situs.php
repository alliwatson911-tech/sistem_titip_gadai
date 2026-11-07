<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/** Pembaca konten dan kebijakan situs publik (satu sumber: tabel site_contents dan policy_settings). */
class Situs
{
    /** @return array<string,mixed> kunci => nilai (JSON otomatis didekode). Hanya konten global (branch_id null). */
    public static function konten(): array
    {
        return DB::table('site_contents')->whereNull('branch_id')->pluck('nilai', 'kunci')
            ->map(function ($v) {
                $v = (string) $v;
                if ($v !== '' && in_array($v[0], ['[', '{'], true)) {
                    $d = json_decode($v, true);

                    return json_last_error() === JSON_ERROR_NONE ? $d : $v;
                }

                return $v;
            })->all();
    }

    /** @return array<string,mixed> nilai kebijakan yang berlaku hari ini (yang terbaru per kunci). */
    public static function kebijakan(): array
    {
        $hasil = [];
        foreach (DB::table('policy_settings')->where('berlaku_mulai', '<=', today())->orderBy('berlaku_mulai')->orderBy('id')->get() as $r) {
            $hasil[$r->key] = json_decode($r->value, true);
        }

        return $hasil;
    }

    /** Ganti token {nama} pada teks dengan nilai kebijakan, agar angka di teks selalu mengikuti pengaturan. */
    public static function isi(?string $teks, array $nilai): string
    {
        $teks = (string) $teks;
        foreach ($nilai as $k => $v) {
            $teks = str_replace('{'.$k.'}', (string) $v, $teks);
        }

        return $teks;
    }
}
