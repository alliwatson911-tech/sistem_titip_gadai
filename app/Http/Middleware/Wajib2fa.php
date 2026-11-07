<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Bila diwajibkan (LINYGADAI_2FA_WAJIB), peran tertentu harus mengaktifkan 2FA sebelum memakai panel. */
class Wajib2fa
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();

        if ($u && config('linygadai.wajib_2fa') && in_array($u->role->value, config('linygadai.peran_2fa'), true) && ! $u->duaFaktorAktif()
            && ! $request->routeIs('panel.akun.2fa*', 'keluar')) {
            return redirect()->route('panel.akun.2fa')->with('status', 'Peran Anda wajib mengaktifkan autentikasi dua faktor sebelum memakai panel.');
        }

        return $next($request);
    }
}
