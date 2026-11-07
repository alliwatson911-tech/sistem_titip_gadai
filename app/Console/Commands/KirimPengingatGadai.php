<?php

namespace App\Console\Commands;

use App\Services\PengingatService;
use Illuminate\Console\Command;

class KirimPengingatGadai extends Command
{
    protected $signature = 'gadai:kirim-pengingat';

    protected $description = 'Kirim email pengingat jatuh tempo, masa tenggang, dan pemberitahuan lelang ke anggota (idempoten)';

    public function handle(PengingatService $svc): int
    {
        $h = $svc->jalankan();
        $this->info("{$h['terkirim']} email terkirim, {$h['gagal']} gagal (akan dicoba lagi), {$h['dilewati']} dilewati.");

        return self::SUCCESS;
    }
}
