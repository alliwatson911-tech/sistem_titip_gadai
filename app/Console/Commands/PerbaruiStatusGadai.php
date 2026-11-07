<?php

namespace App\Console\Commands;

use App\Services\LoanService;
use Illuminate\Console\Command;

class PerbaruiStatusGadai extends Command
{
    protected $signature = 'gadai:perbarui-status';

    protected $description = 'Pindahkan gadai yang lewat jatuh tempo ke masa tenggang, dan yang lewat tenggang ke siap lelang';

    public function handle(LoanService $svc): int
    {
        $h = $svc->perbaruiStatus();
        $this->info("{$h['tenggang']} gadai masuk masa tenggang, {$h['siap_lelang']} gadai siap lelang.");

        return self::SUCCESS;
    }
}
