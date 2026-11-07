<?php

namespace App\Http\Controllers\Panel;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use App\Support\Rupiah;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LelangController extends Controller
{
    public function __construct(private LoanService $svc) {}

    public function index()
    {
        abort_unless(Gate::any(['lelang.kelola', 'lelang.catat']), 403);

        $daftar = Loan::with(['member', 'product', 'auction'])->whereIn('status', [LoanStatus::SiapLelang->value, LoanStatus::Dilelang->value])->orderBy('jatuh_tempo')->get();

        return view('panel.lelang.index', ['daftar' => $daftar, 'tagihan' => fn (Loan $l) => $this->svc->tagihan($l)]);
    }

    public function jadwal(Request $r, Loan $gadai): RedirectResponse
    {
        Gate::authorize('lelang.kelola');
        $r->merge(['harga_limit' => Rupiah::digit($r->input('harga_limit'))]);
        $d = $r->validate(['tgl_lelang' => ['required', 'date', 'after_or_equal:today'], 'harga_limit' => ['required', 'integer', 'min:1', 'max:10000000000']], ['tgl_lelang.required' => 'Isi tanggal lelang.', 'harga_limit.required' => 'Isi harga limit.']);

        return $this->jalankan(fn () => $this->svc->jadwalkanLelang($gadai, $r->user(), $d['tgl_lelang'], (int) $d['harga_limit']), 'Lelang dijadwalkan.');
    }

    public function hasil(Request $r, Loan $gadai): RedirectResponse
    {
        Gate::authorize('lelang.catat');
        $r->merge(['harga_laku' => Rupiah::digit($r->input('harga_laku')), 'biaya_lelang' => Rupiah::digit($r->input('biaya_lelang')) ?? '0']);
        $d = $r->validate(['hasil' => ['required', 'in:laku,tidak_laku'], 'harga_laku' => ['nullable', 'integer', 'min:1', 'max:10000000000'], 'biaya_lelang' => ['required', 'integer', 'min:0', 'max:10000000000']]);

        return $this->jalankan(fn () => $this->svc->catatHasilLelang($gadai, $r->user(), $d['hasil'], isset($d['harga_laku']) ? (int) $d['harga_laku'] : null, (int) $d['biaya_lelang']), 'Hasil lelang dicatat.');
    }

    public function kelebihan(Request $r, Loan $gadai): RedirectResponse
    {
        Gate::authorize('lelang.catat');

        return $this->jalankan(fn () => $this->svc->bayarKelebihan($gadai, $r->user()), 'Kelebihan hasil lelang dicatat sudah dibayarkan ke anggota.');
    }

    private function jalankan(callable $aksi, string $pesan): RedirectResponse
    {
        try {
            $aksi();
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return back()->with('status', $pesan);
    }
}
