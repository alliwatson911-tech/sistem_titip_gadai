<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.masuk');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $kunci = Str::lower($data['email']).'|'.$request->ip();
        $batas = config('linygadai.login_attempts_per_minute');

        if (RateLimiter::tooManyAttempts($kunci, $batas)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($kunci).' detik.',
            ]);
        }

        // Pesan gagal sengaja tunggal agar tidak membocorkan apakah email terdaftar atau akun nonaktif.
        $user = User::where('email', $data['email'])->first();
        $sah = $user && $user->isActive() && Hash::check($data['password'], $user->password);
        if (! $sah) {
            RateLimiter::hit($kunci, 60);
            Audit::log('auth.gagal', null, null, ['email' => mb_substr($data['email'], 0, 120)]);

            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.']);
        }

        RateLimiter::clear($kunci);

        // Staf dengan 2FA aktif belum dianggap masuk: lanjut ke tantangan kode.
        if ($user->duaFaktorAktif()) {
            $request->session()->regenerate();
            $request->session()->put('2fa', ['id' => $user->id, 'ingat' => $request->boolean('ingat'), 'sampai' => now()->addMinutes(10)->timestamp]);

            return redirect()->route('dua-faktor.tantangan');
        }

        Auth::login($user, $request->boolean('ingat'));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        Audit::log('auth.masuk');

        return redirect()->intended(route('panel'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Audit::log('auth.keluar');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
