<?php

namespace App\Http\Controllers\Panel;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Loan;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GadaiController extends Controller
{
    public function __construct(private LoanService $gadai) {}

    public function index(Request $r)
    {
        Gate::authorize('gadai.lihat');
        $q = Loan::with(['member', 'product'])->orderBy('jatuh_tempo');
        if (($s = LoanStatus::tryFrom((string) $r->query('status')))) {
            $q->where('status', $s->value);
        }
        if ($r->boolean('segera')) {
            $q->whereIn('status', array_map(fn ($x) => $x->value, LoanStatus::dapatDibayar()))->whereDate('jatuh_tempo', '<=', today()->addDays(7));
        }
        if (($k = trim((string) $r->query('cari'))) !== '') {
            $q->where('no_sbg', 'like', '%'.addcslashes($k, '%_\\').'%');
        }

        return view('panel.gadai.index', ['daftar' => $q->paginate(20)->withQueryString(), 'semuaStatus' => LoanStatus::cases()]);
    }

    public function show(Loan $gadai)
    {
        Gate::authorize('gadai.lihat');
        $gadai->load(['member', 'product', 'application', 'disbursement', 'histories.pelaku', 'auction', 'redemption']);

        return view('panel.gadai.show', [
            'g' => $gadai,
            'tagihan' => in_array($gadai->status, LoanStatus::dapatDibayar(), true) ? $this->gadai->tagihan($gadai) : null,
            'pembayaran' => $gadai->payments()->with('teller')->latest('id')->get(),
        ]);
    }

    /** Nota gadai siap cetak. */
    public function nota(Loan $gadai)
    {
        Gate::authorize('gadai.lihat');
        $gadai->load(['member.branch', 'product', 'disbursement']);

        return view('panel.gadai.nota', ['g' => $gadai, 'barang' => $gadai->collaterals()->get()]);
    }

    public function taksirUlang(Request $r, Loan $gadai): RedirectResponse
    {
        Gate::authorize('taksiran.buat');
        $r->merge(['nilai_pasar' => \App\Support\Rupiah::digit($r->input('nilai_pasar')), 'nilai_taksiran' => \App\Support\Rupiah::digit($r->input('nilai_taksiran'))]);
        $d = $r->validate([
            'nilai_pasar' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'nilai_taksiran' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], ['nilai_pasar.required' => 'Isi nilai pasar saat ini.', 'nilai_taksiran.required' => 'Isi nilai taksiran ulang.']);

        try {
            $this->gadai->taksirUlang($gadai, $r->user(), (int) $d['nilai_pasar'], (int) $d['nilai_taksiran'], $d['catatan'] ?? null);
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return back()->with('status', 'Taksir ulang tersimpan. Plafon baru berlaku untuk pembayaran berikutnya.');
    }

    public function cairkan(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('pencairan.catat');
        $d = $r->validate(['metode' => ['required', 'in:tunai,transfer'], 'referensi' => ['nullable', 'string', 'max:100']], ['metode.required' => 'Pilih metode pencairan.']);

        try {
            $loan = $this->gadai->cairkan($pengajuan, $r->user(), $d['metode'], $d['referensi'] ?? null);
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.gadai.show', $loan)->with('status', 'Dana dicairkan. Nota gadai '.$loan->no_sbg.' diterbitkan.');
    }
}
