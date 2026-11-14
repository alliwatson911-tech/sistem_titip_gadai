<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ResetSandiAkun extends Command
{
    protected $signature = 'akun:sandi
                            {email? : Alamat email akun yang ingin diubah kata sandinya}
                            {sandi? : Kata sandi baru (minimal 10 karakter)}
                            {--standar : Setel seluruh akun uji/demo ke kata sandi standar (LinyGadai2026!)}';

    protected $description = 'Lihat daftar akun terdaftar atau ubah kata sandi akun pengguna';

    public function handle(): int
    {
        if ($this->option('standar')) {
            $sandiStandar = 'LinyGadai2026!';
            /** @var Collection<int, User> $users */
            $users = User::all();
            if ($users->isEmpty()) {
                $this->warn('Belum ada akun di basis data. Menjalankan UserSeeder...');
                if (class_exists(UserSeeder::class)) {
                    (new UserSeeder)->run();
                    /** @var Collection<int, User> $users */
                    $users = User::all();
                }
            }

            foreach ($users as $user) {
                /** @var User $user */
                $user->password = $sandiStandar;
                $user->status = 'aktif';
                $user->saveQuietly();
                RateLimiter::clear(Str::lower($user->email).'|127.0.0.1');
            }

            $this->info('Seluruh akun ('.$users->count()." akun) telah disetel ke kata sandi standar: {$sandiStandar}");
            $this->tampilkanTabel();

            return self::SUCCESS;
        }

        $email = (string) $this->argument('email');
        $sandi = (string) $this->argument('sandi');

        if (! $email) {
            $this->tampilkanTabel();
            $this->line('');
            $this->comment('Untuk mengubah kata sandi akun tertentu:');
            $this->line('  php artisan akun:sandi email@anda.com PasswordBaru123');
            $this->comment('Untuk menyetel semua akun uji ke sandi standar (LinyGadai2026!):');
            $this->line('  php artisan akun:sandi --standar');

            return self::SUCCESS;
        }

        /** @var User|null $user */
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("Akun dengan email '{$email}' tidak ditemukan di basis data.");

            return self::FAILURE;
        }

        if (! $sandi) {
            $sandi = (string) $this->secret('Masukkan kata sandi baru (minimal 10 karakter)');
        }

        if (mb_strlen($sandi) < 10) {
            $this->error('Kata sandi harus minimal 10 karakter.');

            return self::FAILURE;
        }

        $user->password = $sandi;
        $user->status = 'aktif';
        $user->saveQuietly();

        // Bersihkan rate limiter untuk email ini
        RateLimiter::clear(Str::lower($user->email).'|127.0.0.1');

        $this->info("Kata sandi untuk {$email} ({$user->role->label()}) berhasil diperbarui.");

        return self::SUCCESS;
    }

    private function tampilkanTabel(): void
    {
        /** @var Collection<int, User> $users */
        $users = User::with('branch')->get();
        $baris = $users->map(fn (User $u) => [
            'ID' => $u->id,
            'Email' => $u->email,
            'Nama' => $u->name,
            'Peran' => $u->role->label(),
            'Cabang' => $u->branch?->nama ?? 'Semua Cabang',
            'Status' => $u->status,
            '2FA' => $u->duaFaktorAktif() ? 'Aktif' : 'Nonaktif',
        ])->all();

        if (empty($baris)) {
            $this->warn('Belum ada pengguna di dalam basis data.');

            return;
        }

        $this->table(['ID', 'Email', 'Nama', 'Peran', 'Cabang', 'Status', '2FA'], $baris);
    }
}
