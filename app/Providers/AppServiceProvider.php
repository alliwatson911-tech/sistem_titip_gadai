<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Setiap izin di config/permissions.php menjadi Gate: @can('anggota.lihat'), middleware can:anggota.lihat.
        foreach (array_keys(config('permissions')) as $izin) {
            Gate::define($izin, fn (User $user) => $user->hasPermission($izin));
        }

        Password::defaults(fn () => Password::min(10)->letters()->numbers());
    }
}
