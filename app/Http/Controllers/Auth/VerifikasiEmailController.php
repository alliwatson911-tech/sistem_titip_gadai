<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifikasiEmail;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifikasiEmailController extends Controller
{
    /** Tautan bertanda tangan (middleware 'signed'); tidak perlu login. */
    public function verifikasi(Request $r, int $id, string $hash): RedirectResponse
    {
        $user = User::find($id);
        if (! $user || ! hash_equals(sha1($user->email), $hash)) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
            Audit::log('email.diverifikasi', $user, null, null, $user->id);
        }

        return redirect()->route(auth()->check() ? 'panel' : 'masuk')->with('status', 'Email Anda sudah terverifikasi. Terima kasih.');
    }

    public function kirimUlang(Request $r): RedirectResponse
    {
        $user = $r->user();
        if ($user->email_verified_at !== null) {
            return back()->with('status', 'Email Anda sudah terverifikasi.');
        }
        $user->notify(new VerifikasiEmail);

        return back()->with('status', 'Email verifikasi dikirim ulang. Periksa kotak masuk dan folder spam.');
    }
}
