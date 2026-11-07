<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Pengumuman untuk papan di beranda. Admin: cabangnya sendiri (global scope). Owner: semua, atau global (tanpa cabang). */
class PengumumanController extends Controller
{
    public function index()
    {
        Gate::authorize('konten.lihat');

        return view('panel.konten.pengumuman.index', ['daftar' => Announcement::with('branch')->latest('id')->paginate(20)]);
    }

    public function create(Request $r)
    {
        Gate::authorize('konten.kelola');

        return view('panel.konten.pengumuman.form', ['p' => new Announcement(['is_published' => true]), 'cabang' => $this->pilihanCabang($r)]);
    }

    public function store(Request $r): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $d = $this->validasi($r);
        $d['dibuat_oleh'] = $r->user()->id;
        $d['branch_id'] = $r->user()->role === Role::Owner ? ($d['branch_id'] ?? null) : $r->user()->branch_id;

        $p = new Announcement(collect($d)->except('branch_id')->all());
        $p->branch_id = $d['branch_id'];
        $p->save();

        return redirect()->route('panel.pengumuman.index')->with('status', 'Pengumuman disimpan.');
    }

    public function edit(Request $r, Announcement $pengumuman)
    {
        Gate::authorize('konten.kelola');

        return view('panel.konten.pengumuman.form', ['p' => $pengumuman, 'cabang' => $this->pilihanCabang($r)]);
    }

    public function update(Request $r, Announcement $pengumuman): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $d = $this->validasi($r);
        if ($r->user()->role === Role::Owner) {
            $pengumuman->branch_id = $d['branch_id'] ?? null;
        }
        $pengumuman->fill(collect($d)->except('branch_id')->all())->save();

        return redirect()->route('panel.pengumuman.index')->with('status', 'Pengumuman diperbarui.');
    }

    public function destroy(Announcement $pengumuman): RedirectResponse
    {
        Gate::authorize('konten.kelola');
        $pengumuman->delete(); // soft delete, tercatat di audit

        return redirect()->route('panel.pengumuman.index')->with('status', 'Pengumuman dihapus.');
    }

    private function pilihanCabang(Request $r)
    {
        return $r->user()->role === Role::Owner ? Branch::where('kode', '!=', 'PUSAT')->orderBy('kode')->get() : null;
    }

    private function validasi(Request $r): array
    {
        $d = $r->validate([
            'judul' => ['required', 'string', 'max:150'],
            'isi' => ['required', 'string', 'max:1000'],
            'mulai' => ['nullable', 'date'],
            'selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
            'is_published' => ['nullable', 'boolean'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')],
        ], [
            'judul.required' => 'Judul wajib diisi.', 'isi.required' => 'Isi pengumuman wajib diisi.',
            'selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);
        $d['is_published'] = $r->boolean('is_published');

        return $d;
    }
}
