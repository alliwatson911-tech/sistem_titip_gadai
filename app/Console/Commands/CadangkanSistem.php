<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Cadangan harian: dump database + berkas pribadi (storage/app/private) dalam satu ZIP (opsional terenkripsi AES-256).
 * TIDAK menyertakan .env: simpan APP_KEY dan PII_HASH_KEY terpisah (tanpa keduanya, data terenkripsi tak dapat dipulihkan).
 * Pemulihan: unzip, `mysql nama_db < db-*.sql`, salin folder `private/` ke storage/app/private.
 */
class CadangkanSistem extends Command
{
    protected $signature = 'sistem:cadangan {--ke= : Folder tujuan (default storage/backups)} {--simpan=14 : Hari retensi} {--sandi= : Sandi ZIP (default env BACKUP_PASSWORD)}';

    protected $description = 'Buat cadangan database dan berkas pribadi (ZIP), lalu hapus cadangan yang melewati masa simpan';

    public function handle(): int
    {
        $tujuan = $this->option('ke') ?: storage_path('backups');
        File::ensureDirectoryExists($tujuan);
        $stempel = now()->format('Ymd-His');
        $sql = "$tujuan/db-$stempel.sql";
        $zip = "$tujuan/linygadai-$stempel.zip";

        $k = config('database.connections.'.config('database.default'));
        $dump = $this->cariMysqldump();
        if (! $dump) {
            $this->error('mysqldump tidak ditemukan. Isi MYSQLDUMP_PATH di .env.');

            return self::FAILURE;
        }

        $p = new Process([$dump, '--single-transaction', '--routines', '--triggers', '--default-character-set=utf8mb4',
            '-h', (string) $k['host'], '-P', (string) $k['port'], '-u', (string) $k['username'], '--result-file='.$sql, (string) $k['database']],
            null, ['MYSQL_PWD' => (string) ($k['password'] ?? '')]);
        $p->setTimeout(600);
        $p->run();
        if (! $p->isSuccessful() || ! is_file($sql) || filesize($sql) < 100) {
            @unlink($sql);
            $this->error('Dump database gagal: '.trim($p->getErrorOutput()));

            return self::FAILURE;
        }

        $z = new ZipArchive;
        if ($z->open($zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Tidak dapat membuat ZIP.');

            return self::FAILURE;
        }
        $sandi = (string) ($this->option('sandi') ?: env('BACKUP_PASSWORD', ''));
        $z->addFile($sql, 'db-'.$stempel.'.sql');
        $privat = storage_path('app/private');
        if (is_dir($privat)) {
            foreach (File::allFiles($privat) as $f) {
                $z->addFile($f->getPathname(), 'private/'.str_replace('\\', '/', $f->getRelativePathname()));
            }
        }
        if ($sandi !== '') {
            $z->setPassword($sandi);
            for ($i = 0; $i < $z->numFiles; $i++) {
                $z->setEncryptionIndex($i, ZipArchive::EM_AES_256);
            }
        }
        $z->close();
        @unlink($sql);

        $this->bersihkan($tujuan, (int) $this->option('simpan'));
        $this->info('Cadangan dibuat: '.$zip.' ('.round(filesize($zip) / 1024).' KB)'.($sandi === '' ? ' — TANPA sandi; isi BACKUP_PASSWORD.' : ', terenkripsi.'));

        return self::SUCCESS;
    }

    private function cariMysqldump(): ?string
    {
        foreach ([env('MYSQLDUMP_PATH'), 'C:\\XAMPP\\mysql\\bin\\mysqldump.exe', '/usr/bin/mysqldump', '/usr/local/bin/mysqldump'] as $c) {
            if ($c && is_file($c)) {
                return $c;
            }
        }
        $cek = new Process(PHP_OS_FAMILY === 'Windows' ? ['where', 'mysqldump'] : ['which', 'mysqldump']);
        $cek->run();

        return $cek->isSuccessful() ? trim(strtok($cek->getOutput(), "\r\n")) : null;
    }

    /** Hapus cadangan lebih tua dari $hari, tetapi selalu sisakan 3 yang terbaru. */
    private function bersihkan(string $dir, int $hari): void
    {
        $berkas = collect(File::glob("$dir/linygadai-*.zip"))->sortByDesc(fn ($f) => filemtime($f))->values();
        foreach ($berkas->slice(3) as $f) {
            if (filemtime($f) < now()->subDays($hari)->timestamp) {
                @unlink($f);
            }
        }
    }
}
