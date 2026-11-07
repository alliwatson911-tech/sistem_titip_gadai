<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Kontak dan lokasi cabang untuk bagian Tentang Kami. Admin hanya cabangnya; kantor pusat hanya Owner. */
class KontakCabangController extends Controller
{
    public function index(Request $r)
    {
        Gate::authorize('cabang.kontak');
        $q = Branch::orderByRaw("FIELD(kode,'PUSAT','A','B')");
        if ($r->user()->role !== Role::Owner) {
            $q->where('id', $r->user()->branch_id);
        }

        return view('panel.konten.kontak.index', ['cabang' => $q->get()]);
    }

    public function edit(Request $r, Branch $cabang)
    {
        $this->izinkan($r, $cabang);

        return view('panel.konten.kontak.form', ['c' => $cabang]);
    }

    public function update(Request $r, Branch $cabang): RedirectResponse
    {
        $this->izinkan($r, $cabang);
        $d = $r->validate([
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s().-]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s().-]+$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:lng'],
            'lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:lat'],
            'jam_buka' => ['nullable', 'string', 'max:150'],
        ], [
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda + ( ) - .', 'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan tanda + ( ) - .',
            'lat.between' => 'Lintang harus antara -90 dan 90.', 'lng.between' => 'Bujur harus antara -180 dan 180.',
            'lat.required_with' => 'Isi lintang dan bujur bersama-sama.', 'lng.required_with' => 'Isi lintang dan bujur bersama-sama.',
        ]);

        $cabang->update(array_map(fn ($v) => $v === '' ? null : $v, $d));

        return redirect()->route('panel.kontak.index')->with('status', 'Kontak '.$cabang->nama.' diperbarui.');
    }

    private function izinkan(Request $r, Branch $cabang): void
    {
        Gate::authorize('cabang.kontak');
        abort_unless($r->user()->role === Role::Owner || $r->user()->branch_id === $cabang->id, 404);
    }
}
