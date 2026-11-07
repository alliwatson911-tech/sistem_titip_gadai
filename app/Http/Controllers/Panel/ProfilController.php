<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/** Profil anggota: kontak, alamat, dan unggah KTP. Nama dan NIK hanya dapat diubah admin. */
class ProfilController extends Controller
{
    public function edit(Request $r)
    {
        Gate::authorize('profil.ubah');

        return view('panel.profil', ['m' => $r->user()->member()->with('branch')->firstOrFail()]);
    }

    public function update(Request $r): RedirectResponse
    {
        Gate::authorize('profil.ubah');
        $d = $r->validate([
            'telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'tgl_lahir' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
        ], ['telepon.regex' => 'Nomor telepon tidak valid. Contoh: 08123456789.', 'telepon.required' => 'Nomor telepon wajib diisi.']);

        $r->user()->member()->firstOrFail()->update($d);

        return back()->with('status', 'Profil diperbarui.');
    }

    public function unggahKtp(Request $r): RedirectResponse
    {
        Gate::authorize('profil.ubah');
        $r->validate(['ktp' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']], [
            'ktp.required' => 'Pilih foto KTP.', 'ktp.image' => 'Berkas harus berupa gambar JPG, PNG, atau WebP.', 'ktp.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $m = $r->user()->member()->firstOrFail();
        $lama = $m->ktp_path;
        $m->forceFill(['ktp_path' => $r->file('ktp')->store("ktp/{$m->id}")]);
        if ($m->status === 'ditolak') {
            $m->status = 'calon'; // unggah ulang = minta ditinjau lagi
        }
        $m->save();

        if ($lama) {
            Storage::delete($lama);
        }

        return back()->with('status', 'Foto KTP diunggah. Admin cabang akan memverifikasi data Anda.');
    }
}
