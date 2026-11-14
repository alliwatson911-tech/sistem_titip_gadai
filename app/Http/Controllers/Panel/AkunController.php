<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/** Ganti kata sandi sendiri (semua peran). */
class AkunController extends Controller
{
    public function sandi()
    {
        return view('panel.akun.sandi');
    }

    public function ubahSandi(Request $r): RedirectResponse
    {
        $d = $r->validate([
            'sandi_lama' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'different:sandi_lama'],
        ], [
            'sandi_lama.required' => 'Isi kata sandi saat ini.', 'sandi_lama.current_password' => 'Kata sandi saat ini salah.',
            'password.required' => 'Kata sandi baru wajib diisi.', 'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 10 karakter.', 'password.letters' => 'Kata sandi harus memuat huruf.', 'password.numbers' => 'Kata sandi harus memuat angka.',
            'password.different' => 'Kata sandi baru harus berbeda dari yang lama.',
        ]);

        $u = $r->user();
        $u->password = $d['password'];
        $u->wajib_ganti_password = false;
        $u->setRememberToken(Str::random(60));
        $u->save();

        // Perangkat lain dikeluarkan; sesi ini tetap, dengan ID baru.
        DB::table('sessions')->where('user_id', $u->id)->where('id', '!=', $r->session()->getId())->delete();
        $r->session()->regenerate();
        Audit::log('auth.ganti_sandi', $u);

        return redirect()->route('panel.akun.sandi')->with('status', 'Kata sandi diubah. Perangkat lain telah dikeluarkan.');
    }
}
