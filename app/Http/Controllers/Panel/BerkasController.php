<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Collateral;
use App\Models\CollateralPhoto;
use App\Models\Member;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Berkas pribadi (KTP, foto barang) hanya lewat sini: disk privat, otorisasi per permintaan, tanpa URL publik. */
class BerkasController extends Controller
{
    public function ktp(Request $r, Member $anggota)
    {
        // Member model ber-scope: anggota hanya menemukan miliknya, staf hanya cabangnya, lainnya 404.
        abort_unless($anggota->ktp_path && Storage::exists($anggota->ktp_path), 404);

        if ($r->user()->role->isStaff()) {
            Audit::log('berkas.ktp_dilihat', $anggota);
        }

        return Storage::response($anggota->ktp_path);
    }

    /** Surat kuasa penebus: hanya staf cabang yang sama; pembukaan dicatat. */
    public function kuasa(Request $r, \App\Models\Loan $gadai)
    {
        abort_unless($r->user()->role->isStaff(), 403);
        $path = $gadai->redemption?->surat_kuasa_path;
        abort_unless($path && Storage::exists($path), 404);
        Audit::log('berkas.kuasa_dilihat', $gadai);

        return Storage::response($path);
    }

    public function foto(Request $r, CollateralPhoto $foto)
    {
        abort_unless($r->user()->role->isStaff(), 403);
        // Collateral ber-scope cabang: foto cabang lain -> 404.
        $jaminan = Collateral::findOrFail($foto->collateral_id);
        abort_unless(Storage::exists($foto->path), 404);

        return Storage::response($foto->path);
    }
}
