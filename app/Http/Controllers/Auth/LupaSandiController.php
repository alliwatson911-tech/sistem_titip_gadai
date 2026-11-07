<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/** Permintaan tautan atur ulang. Jawaban SELALU sama agar tidak membocorkan email mana yang terdaftar. */
class LupaSandiController extends Controller
{
    private const PESAN = 'Jika email tersebut terdaftar dan akunnya aktif, kami sudah mengirim tautan atur ulang kata sandi. Periksa kotak masuk dan folder spam. Tautan berlaku 60 menit.';

    public function create()
    {
        return view('auth.lupa-sandi');
    }

    public function store(Request $r): RedirectResponse
    {
        $d = $r->validate(['email' => ['required', 'email', 'max:255']], ['email.required' => 'Email wajib diisi.', 'email.email' => 'Format email tidak valid.']);

        $kunci = 'lupa-sandi|'.Str::lower($d['email']).'|'.$r->ip();
        if (RateLimiter::tooManyAttempts($kunci, 3)) {
            return back()->with('status', self::PESAN); // tidak membedakan: tetap jawaban yang sama
        }
        RateLimiter::hit($kunci, 900);

        $user = User::where('email', $d['email'])->first();
        if ($user && $user->isActive()) {
            Password::broker()->sendResetLink(['email' => $user->email]);
            Audit::log('sandi.lupa_diminta', $user, null, null, $user->id);
        }

        return back()->with('status', self::PESAN);
    }
}
