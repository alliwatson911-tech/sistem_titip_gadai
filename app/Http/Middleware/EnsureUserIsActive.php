<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Pengguna yang dinonaktifkan langsung dikeluarkan, termasuk dari sesi yang sedang berjalan. */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('masuk')->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi admin cabang.']);
        }

        return $next($request);
    }
}
