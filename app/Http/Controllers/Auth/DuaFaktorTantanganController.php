<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/** Tahap kedua masuk untuk staf dengan 2FA: kode aplikasi autentikator atau kode pemulihan sekali pakai. */
class DuaFaktorTantanganController extends Controller
{
    private function tantangan(Request $r): ?array
    {
        $t = $r->session()->get('2fa');

        return is_array($t) && ($t['sampai'] ?? 0) >= now()->timestamp ? $t : null;
    }

    public function create(Request $r)
    {
        if (! $this->tantangan($r)) {
            $r->session()->forget('2fa');

            return redirect()->route('masuk')->withErrors(['email' => 'Sesi verifikasi berakhir. Masuk kembali.']);
        }

        return view('auth.tantangan-2fa');
    }

    public function store(Request $r): RedirectResponse
    {
        $t = $this->tantangan($r);
        if (! $t) {
            $r->session()->forget('2fa');

            return redirect()->route('masuk')->withErrors(['email' => 'Sesi verifikasi berakhir. Masuk kembali.']);
        }
        $d = $r->validate(['kode' => ['required', 'string', 'max:32']], ['kode.required' => 'Masukkan kode dari aplikasi autentikator, atau kode pemulihan.']);

        $kunci = '2fa|'.$t['id'].'|'.$r->ip();
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $r->session()->forget('2fa');

            return redirect()->route('masuk')->withErrors(['email' => 'Terlalu banyak kode salah. Masuk kembali setelah beberapa menit.']);
        }

        $user = User::find($t['id']);
        if (! $user || ! $user->isActive() || ! $user->duaFaktorAktif()) {
            $r->session()->forget('2fa');

            return redirect()->route('masuk')->withErrors(['email' => 'Verifikasi gagal. Masuk kembali.']);
        }

        $jalur = $this->periksa($user, $d['kode']);
        if (! $jalur) {
            RateLimiter::hit($kunci, 300);
            Audit::log('auth.2fa_gagal', $user, null, null, $user->id);

            return back()->withErrors(['kode' => 'Kode salah atau sudah dipakai.']);
        }

        RateLimiter::clear($kunci);
        $r->session()->forget('2fa');
        Auth::login($user, (bool) $t['ingat']);
        $r->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        Audit::log('auth.masuk', null, null, ['2fa' => $jalur]);

        return redirect()->intended(route('panel'));
    }

    /** @return string|null 'totp' | 'pemulihan' bila kode sah */
    private function periksa(User $user, string $kode): ?string
    {
        $langkah = Totp::verifikasi((string) $user->two_factor_secret, $kode, $user->two_factor_last_step);
        if ($langkah !== null) {
            $user->forceFill(['two_factor_last_step' => $langkah])->saveQuietly();

            return 'totp';
        }

        $hash = User::hashKodePemulihan($kode);
        $daftar = $user->two_factor_recovery_codes ?? [];
        foreach ($daftar as $i => $h) {
            if (hash_equals($h, $hash)) {
                unset($daftar[$i]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($daftar)])->saveQuietly();

                return 'pemulihan';
            }
        }

        return null;
    }
}
