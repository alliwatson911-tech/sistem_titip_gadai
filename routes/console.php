<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Pengajuan yang barangnya tidak dibawa sampai batas waktu -> kedaluwarsa (setiap hari, dini hari).
Schedule::command('pengajuan:kedaluwarsa')->dailyAt('01:00');

// Detak: dipakai `php artisan sistem:periksa` untuk memastikan scheduler benar-benar berjalan.
Schedule::call(fn () => \Illuminate\Support\Facades\Cache::put('scheduler:detak', now()->timestamp, 3600))->everyMinute()->name('scheduler-detak');

// Gadai lewat jatuh tempo -> tenggang; lewat tenggang -> siap lelang (setiap hari, tepat setelah tengah malam).
Schedule::command('gadai:perbarui-status')->dailyAt('00:10');

// Cadangan harian database + berkas pribadi (ZIP). Isi BACKUP_PASSWORD di .env agar terenkripsi.
Schedule::command('sistem:cadangan')->dailyAt('02:00');

// Email pengingat/pemberitahuan ke anggota, jam kerja agar terbaca (idempoten; gagal dicoba lagi hari berikutnya).
Schedule::command('gadai:kirim-pengingat')->dailyAt('08:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
