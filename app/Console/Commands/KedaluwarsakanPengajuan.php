<?php

namespace App\Console\Commands;

use App\Services\ApplicationFlow;
use Illuminate\Console\Command;

class KedaluwarsakanPengajuan extends Command
{
    protected $signature = 'pengajuan:kedaluwarsa';

    protected $description = 'Tandai pengajuan yang barangnya tidak dibawa sampai batas waktu sebagai kedaluwarsa';

    public function handle(ApplicationFlow $alur): int
    {
        $this->info($alur->kedaluwarsakan().' pengajuan dikedaluwarsakan.');

        return self::SUCCESS;
    }
}
