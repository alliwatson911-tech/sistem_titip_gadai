<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Payment;
use App\Services\LoanService;
use App\Support\Rupiah;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function __construct(private LoanService $gadai) {}

    public function store(Request $r, Loan $gadai): RedirectResponse
    {
        Gate::authorize('pembayaran.catat');
        $r->merge(['pokok' => Rupiah::digit($r->input('pokok'))]);
        $d = $r->validate([
            'tipe' => ['required', 'in:perpanjangan,cicil,tebus'],
            'metode' => ['required', 'in:tunai,transfer'],
            'pokok' => ['nullable', 'integer', 'min:1', 'max:1000000000'],
            'bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], ['tipe.required' => 'Pilih jenis pembayaran.', 'metode.required' => 'Pilih metode pembayaran.', 'bukti.image' => 'Bukti harus berupa gambar JPG, PNG, atau WebP.', 'bukti.max' => 'Ukuran bukti maksimal 4 MB.']);

        try {
            $p = $this->gadai->bayar($gadai, $r->user(), $d['tipe'], $d['metode'], isset($d['pokok']) ? (int) $d['pokok'] : null, $r->file('bukti'));
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.gadai.show', $gadai)->with('status', $p->status === 'sah'
            ? 'Pembayaran dicatat. Kwitansi '.$p->no_kwitansi.' diterbitkan.'
            : 'Transfer dicatat dan menunggu verifikasi petugas lain.');
    }

    public function verifikasi(Request $r, Payment $pembayaran): RedirectResponse
    {
        Gate::authorize('pembayaran.verifikasi');
        $d = $r->validate(['keputusan' => ['required', 'in:setuju,tolak'], 'alasan' => ['nullable', 'string', 'max:255']]);

        return $this->jalankan($pembayaran, fn () => $this->gadai->verifikasi($pembayaran, $r->user(), $d['keputusan'] === 'setuju', $d['alasan'] ?? null), 'Verifikasi tersimpan.');
    }

    public function batalkan(Request $r, Payment $pembayaran): RedirectResponse
    {
        Gate::authorize('pembayaran.batalkan');
        $d = $r->validate(['alasan' => ['required', 'string', 'max:255']], ['alasan.required' => 'Isi alasan pembalikan.']);

        return $this->jalankan($pembayaran, fn () => $this->gadai->batalkan($pembayaran, $r->user(), $d['alasan']), 'Pembayaran dibalikkan.');
    }

    public function kwitansi(Payment $pembayaran)
    {
        Gate::authorize('pembayaran.catat');
        abort_unless(in_array($pembayaran->status, ['sah', 'batal'], true), 404);

        return view('panel.gadai.kwitansi', ['p' => $pembayaran->load(['loan.member', 'loan.product', 'teller'])]);
    }

    public function bukti(Payment $pembayaran)
    {
        abort_unless(auth()->user()->hasPermission('pembayaran.verifikasi') || auth()->user()->hasPermission('pembayaran.catat'), 403);
        abort_unless($pembayaran->bukti_path && Storage::exists($pembayaran->bukti_path), 404);

        return Storage::response($pembayaran->bukti_path);
    }

    private function jalankan(Payment $p, callable $aksi, string $pesan): RedirectResponse
    {
        try {
            $aksi();
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.gadai.show', $p->loan_id)->with('status', $pesan);
    }
}
