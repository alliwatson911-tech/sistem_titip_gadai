<?php

namespace App\Console\Commands;

use App\Enums\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/** Membuat matriks hak akses (izin x peran) dari config/permissions.php, sumber kebenaran tunggal. */
class TabelIzin extends Command
{
    protected $signature = 'izin:tabel {--tulis= : Tulis ke berkas Markdown ini}';

    protected $description = 'Cetak matriks hak akses dari config/permissions.php';

    public function handle(): int
    {
        $peran = Role::cases();
        $baris = ['| Izin | '.implode(' | ', array_map(fn ($r) => $r->label(), $peran)).' |', '|---|'.str_repeat(':-:|', count($peran))];
        foreach (config('permissions') as $izin => $boleh) {
            $baris[] = "| `$izin` | ".implode(' | ', array_map(fn ($r) => in_array($r, $boleh, true) ? '✓' : '—', $peran)).' |';
        }
        $md = implode("\n", $baris)."\n";

        if ($berkas = $this->option('tulis')) {
            File::put($berkas, "# Matriks hak akses (dibuat otomatis)\n\nDibuat dari `config/permissions.php` oleh `php artisan izin:tabel`. Jangan diedit manual; ubah konfigurasinya lalu buat ulang.\nCakupan data (cabang sendiri / milik sendiri) ditegakkan terpisah oleh global scope `BelongsToBranch`.\n\n".$md);
            $this->info("Ditulis ke $berkas");
        } else {
            $this->line($md);
        }

        return self::SUCCESS;
    }
}
