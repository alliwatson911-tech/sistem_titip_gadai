<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** FAQ dan Informasi (accordion beranda). Admin: cabangnya; Owner: semua atau global. */
class FaqController extends Controller
{
    public function index()
    {
        Gate::authorize('konten.lihat');

        return view('panel.konten.faq.index', ['daftar' => Faq::with('branch')->orderBy('kelompok')->orderBy('urutan')->paginate(30)]);
    }

    public function create(Request $r)
    {
        Gate::authorize('konten.kelola');

        return view('panel.konten.faq.form', ['f' => new Faq(['is_published' => true, 'kelompok' => 'faq']), 'cabang' => $this->pilihanCabang($r)]);
    }

    public function store(Request $r): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $d = $this->validasi($r);
        $f = new Faq(collect($d)->except('branch_id')->all());
        $f->branch_id = $r->user()->role === Role::Owner ? ($d['branch_id'] ?? null) : $r->user()->branch_id;
        $f->save();

        return redirect()->route('panel.faq.index')->with('status', 'Item disimpan.');
    }

    public function edit(Request $r, Faq $faq)
    {
        Gate::authorize('konten.kelola');

        return view('panel.konten.faq.form', ['f' => $faq, 'cabang' => $this->pilihanCabang($r)]);
    }

    public function update(Request $r, Faq $faq): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $d = $this->validasi($r);
        if ($r->user()->role === Role::Owner) {
            $faq->branch_id = $d['branch_id'] ?? null;
        }
        $faq->fill(collect($d)->except('branch_id')->all())->save();

        return redirect()->route('panel.faq.index')->with('status', 'Item diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $faq->delete();

        return redirect()->route('panel.faq.index')->with('status', 'Item dihapus.');
    }

    private function pilihanCabang(Request $r)
    {
        return $r->user()->role === Role::Owner ? Branch::where('kode', '!=', 'PUSAT')->orderBy('kode')->get() : null;
    }

    private function validasi(Request $r): array
    {
        $d = $r->validate([
            'kelompok' => ['required', 'in:faq,informasi'],
            'pertanyaan' => ['required', 'string', 'max:200'],
            'jawaban' => ['required', 'string', 'max:1500'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')],
        ], ['pertanyaan.required' => 'Pertanyaan wajib diisi.', 'jawaban.required' => 'Jawaban wajib diisi.']);
        $d['is_published'] = $r->boolean('is_published');
        $d['urutan'] = (int) ($d['urutan'] ?? 0);

        return $d;
    }
}
