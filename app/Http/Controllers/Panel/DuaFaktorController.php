<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Pengaturan 2FA (TOTP) untuk staf: aktifkan, nonaktifkan, kode pemulihan. */
class DuaFaktorController extends Controller
{
    private function staf(Request $r): User
    {
        abort_unless($r->user()->role->isStaff(), 403);

        return $r->user();
    }

    public function tampil(Request $r)
    {
        $u = $this->staf($r);
        $menunggu = $u->two_factor_secret && ! $u->two_factor_confirmed_at;

        return view('panel.akun.dua-faktor', [
            'u' => $u, 'wajib' => $this->diwajibkan($u), 'menunggu' => $menunggu,
            'rahasia' => $menunggu ? $u->two_factor_secret : null,
            'uri' => $menunggu ? Totp::uri($u->two_factor_secret, $u->email) : null,
            'sisaKode' => count($u->two_factor_recovery_codes ?? []),
        ]);
    }

    public function mulai(Request $r): RedirectResponse
    {
        $u = $this->staf($r);
        if ($u->duaFaktorAktif()) {
            return back()->withErrors(['umum' => '2FA sudah aktif.']);
        }
        $u->forceFill(['two_factor_secret' => Totp::buatRahasia(), 'two_factor_confirmed_at' => null, 'two_factor_last_step' => null])->saveQuietly();

        return redirect()->route('panel.akun.2fa');
    }

    public function konfirmasi(Request $r): RedirectResponse
    {
        $u = $this->staf($r);
        $d = $r->validate(['kode' => ['required', 'string', 'max:10']], ['kode.required' => 'Masukkan kode 6 digit dari aplikasi autentikator.']);
        if (! $u->two_factor_secret || $u->two_factor_confirmed_at) {
            return redirect()->route('panel.akun.2fa');
        }
        $langkah = Totp::verifikasi($u->two_factor_secret, $d['kode']);
        if ($langkah === null) {
            return back()->withErrors(['kode' => 'Kode salah. Pastikan jam ponsel Anda tepat, lalu coba lagi.']);
        }

        $kode = $this->buatKodePemulihan();
        $u->forceFill([
            'two_factor_confirmed_at' => now(), 'two_factor_last_step' => $langkah,
            'two_factor_recovery_codes' => array_map(fn ($k) => User::hashKodePemulihan($k), $kode),
        ])->saveQuietly();
        DB::table('sessions')->where('user_id', $u->id)->where('id', '!=', $r->session()->getId())->delete();
        Audit::log('auth.2fa_aktif', $u);

        return redirect()->route('panel.akun.2fa')->with('status', '2FA aktif. Perangkat lain dikeluarkan.')->with('kode_pemulihan', $kode);
    }

    public function nonaktifkan(Request $r): RedirectResponse
    {
        $u = $this->staf($r);
        $d = $r->validate(['sandi' => ['required', 'string'], 'kode' => ['required', 'string', 'max:10']], ['sandi.required' => 'Isi kata sandi Anda.', 'kode.required' => 'Isi kode 6 digit.']);

        if ($this->diwajibkan($u)) {
            return back()->withErrors(['umum' => '2FA wajib untuk peran Anda dan tidak dapat dinonaktifkan.']);
        }
        if (! Hash::check($d['sandi'], $u->password)) {
            return back()->withErrors(['sandi' => 'Kata sandi salah.']);
        }
        if (! $u->duaFaktorAktif() || Totp::verifikasi($u->two_factor_secret, $d['kode'], $u->two_factor_last_step) === null) {
            return back()->withErrors(['kode' => 'Kode salah atau sudah dipakai.']);
        }

        $u->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null, 'two_factor_last_step' => null])->saveQuietly();
        Audit::log('auth.2fa_nonaktif', $u);

        return redirect()->route('panel.akun.2fa')->with('status', '2FA dinonaktifkan.');
    }

    public function kodeBaru(Request $r): RedirectResponse
    {
        $u = $this->staf($r);
        $d = $r->validate(['sandi' => ['required', 'string']], ['sandi.required' => 'Isi kata sandi Anda.']);
        if (! $u->duaFaktorAktif()) {
            return redirect()->route('panel.akun.2fa');
        }
        if (! Hash::check($d['sandi'], $u->password)) {
            return back()->withErrors(['sandi' => 'Kata sandi salah.']);
        }

        $kode = $this->buatKodePemulihan();
        $u->forceFill(['two_factor_recovery_codes' => array_map(fn ($k) => User::hashKodePemulihan($k), $kode)])->saveQuietly();
        Audit::log('auth.2fa_kode_baru', $u);

        return redirect()->route('panel.akun.2fa')->with('status', 'Kode pemulihan baru dibuat; kode lama tidak berlaku.')->with('kode_pemulihan', $kode);
    }

    private function diwajibkan(User $u): bool
    {
        return config('linygadai.wajib_2fa') && in_array($u->role->value, config('linygadai.peran_2fa'), true);
    }

    /** 8 kode, format xxxx-xxxx, tampil sekali. */
    private function buatKodePemulihan(): array
    {
        return array_map(fn () => strtolower(Str::random(4).'-'.Str::random(4)), range(1, 8));
    }
}
