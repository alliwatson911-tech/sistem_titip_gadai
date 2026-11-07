<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as AturanSandi;

class AturUlangSandiController extends Controller
{
    private const GALAT = 'Tautan tidak valid atau sudah kedaluwarsa. Minta tautan baru.';

    public function create(Request $r, string $token)
    {
        return view('auth.atur-ulang-sandi', ['token' => $token, 'email' => (string) $r->query('email')]);
    }

    public function store(Request $r): RedirectResponse
    {
        $d = $r->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', AturanSandi::defaults()],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.', 'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 10 karakter.', 'password.letters' => 'Kata sandi harus memuat huruf.', 'password.numbers' => 'Kata sandi harus memuat angka.',
        ]);

        // Akun nonaktif tidak boleh memulihkan akses lewat tautan.
        $user = User::where('email', $d['email'])->first();
        if (! $user || ! $user->isActive()) {
            return back()->withInput($r->only('email'))->withErrors(['email' => self::GALAT]);
        }

        $status = Password::broker()->reset(
            ['email' => $d['email'], 'password' => $d['password'], 'password_confirmation' => $r->input('password_confirmation'), 'token' => $d['token']],
            function (User $u, string $sandi) {
                $u->password = $sandi;
                $u->setRememberToken(Str::random(60));
                $u->save();
                DB::table('sessions')->where('user_id', $u->id)->delete(); // semua perangkat dikeluarkan
                Audit::log('sandi.diatur_ulang', $u, null, null, $u->id);
                event(new PasswordReset($u));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($r->only('email'))->withErrors(['email' => self::GALAT]);
        }

        return redirect()->route('masuk')->with('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
    }
}
