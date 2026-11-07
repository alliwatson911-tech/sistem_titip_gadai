<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemberController extends Controller
{
    /** Daftar anggota. Cakupan cabang ditegakkan global scope BelongsToBranch. */
    public function index(Request $r)
    {
        $q = Member::with('branch')->latest('id');
        if (in_array($r->query('status'), ['calon', 'terverifikasi', 'ditolak', 'blokir'], true)) {
            $q->where('status', $r->query('status'));
        }
        if (($k = trim((string) $r->query('cari'))) !== '') {
            $q->where('nama_lengkap', 'like', '%'.addcslashes($k, '%_\\').'%');
        }

        return view('panel.anggota.index', ['anggota' => $q->paginate(20)->withQueryString()]);
    }

    public function show(Member $anggota)
    {
        return view('panel.anggota.show', ['m' => $anggota->load('branch'), 'nikPenuh' => null]);
    }

    /** Verifikasi / tolak / blokir. Verifikasi mensyaratkan KTP sudah diunggah. */
    public function tindakan(Request $r, Member $anggota): RedirectResponse
    {
        Gate::authorize('anggota.kelola');
        $d = $r->validate(['aksi' => ['required', 'in:verifikasi,tolak,blokir']]);

        if ($d['aksi'] === 'verifikasi' && ! $anggota->ktp_path) {
            return back()->withErrors(['umum' => 'Anggota belum mengunggah foto KTP.']);
        }

        $status = ['verifikasi' => 'terverifikasi', 'tolak' => 'ditolak', 'blokir' => 'blokir'][$d['aksi']];
        $anggota->forceFill([
            'status' => $status,
            'verified_by' => $status === 'terverifikasi' ? $r->user()->id : null,
            'verified_at' => $status === 'terverifikasi' ? now() : null,
        ])->save();

        return back()->with('status', 'Status anggota diubah menjadi '.$status.'.');
    }

    /** Menampilkan NIK lengkap hanya atas permintaan eksplisit, dan dicatat di audit. */
    public function lihatNik(Member $anggota)
    {
        Gate::authorize('anggota.kelola');
        Audit::log('anggota.lihat_nik', $anggota);

        return view('panel.anggota.show', ['m' => $anggota->load('branch'), 'nikPenuh' => $anggota->nik_enc]);
    }
}
