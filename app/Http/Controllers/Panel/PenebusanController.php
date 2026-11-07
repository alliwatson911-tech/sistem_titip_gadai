<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Pengeluaran barang setelah gadai ditebus. */
class PenebusanController extends Controller
{
    public function store(Request $r, Loan $gadai, LoanService $svc): RedirectResponse
    {
        Gate::authorize('jaminan.keluarkan');
        $d = $r->validate([
            'penebus_tipe' => ['required', 'in:penggadai,kuasa'],
            'nama_penebus' => ['required', 'string', 'max:120'],
            'nik_penebus' => ['nullable', 'digits:16'],
            'nota_asli_diperiksa' => ['accepted'],
            'surat_kuasa' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ], [
            'nama_penebus.required' => 'Nama penebus wajib diisi.', 'nik_penebus.digits' => 'NIK penebus harus 16 digit.',
            'nota_asli_diperiksa.accepted' => 'Centang bahwa nota gadai asli dan KTP asli sudah diperiksa.', 'surat_kuasa.mimes' => 'Surat kuasa harus JPG, PNG, WebP, atau PDF.',
        ]);

        try {
            $svc->keluarkanBarang($gadai, $r->user(), $d + ['nota_asli_diperiksa' => true], $r->file('surat_kuasa'));
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.gadai.show', $gadai)->with('status', 'Barang dikeluarkan dan diserahkan kepada penebus.');
    }
}
