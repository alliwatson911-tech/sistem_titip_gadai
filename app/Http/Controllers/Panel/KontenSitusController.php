<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Audit;
use App\Services\Situs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Konten global situs (Owner): tagline, sambutan, ringkasan, informasi perizinan, media sosial. */
class KontenSitusController extends Controller
{
    private const SOSMED = ['instagram', 'facebook', 'tiktok', 'youtube'];

    public function edit()
    {
        Gate::authorize('konten.situs');
        $k = Situs::konten();

        return view('panel.konten.situs', ['k' => $k, 'sosmed' => array_merge(array_fill_keys(self::SOSMED, ''), (array) ($k['sosmed'] ?? []))]);
    }

    public function update(Request $r): RedirectResponse
    {
        Gate::authorize('konten.situs');
        $aturanUrl = ['nullable', 'url:https', 'max:255'];
        $d = $r->validate([
            'tagline' => ['required', 'string', 'max:120'],
            'sambutan' => ['required', 'string', 'max:600'],
            'ringkasan' => ['required', 'string', 'max:800'],
            'izin_usaha' => ['nullable', 'string', 'max:500'],
            'sosmed' => ['nullable', 'array'],
            'sosmed.instagram' => $aturanUrl, 'sosmed.facebook' => $aturanUrl, 'sosmed.tiktok' => $aturanUrl, 'sosmed.youtube' => $aturanUrl,
        ], [
            'tagline.required' => 'Tagline wajib diisi.', 'sambutan.required' => 'Sambutan wajib diisi.', 'ringkasan.required' => 'Ringkasan wajib diisi.',
            'sosmed.*.url' => 'Tautan harus berupa alamat https:// yang valid.',
        ]);

        $sosmed = [];
        foreach (self::SOSMED as $s) {
            $sosmed[$s] = (string) ($d['sosmed'][$s] ?? '');
        }
        $baru = ['tagline' => $d['tagline'], 'sambutan' => $d['sambutan'], 'ringkasan' => $d['ringkasan'],
            'izin_usaha' => (string) ($d['izin_usaha'] ?? ''), 'sosmed' => json_encode($sosmed, JSON_UNESCAPED_UNICODE)];

        $lama = Situs::konten();
        DB::transaction(function () use ($baru) {
            foreach ($baru as $kunci => $nilai) {
                DB::table('site_contents')->updateOrInsert(['branch_id' => null, 'kunci' => $kunci], ['nilai' => $nilai, 'updated_at' => now(), 'created_at' => now()]);
            }
        });
        Audit::log('situs.diubah', null, array_intersect_key($lama, $baru) ? ['kunci' => array_keys($baru)] : null, ['kunci' => array_keys($baru), 'izin_usaha_terisi' => $baru['izin_usaha'] !== '']);

        return back()->with('status', 'Konten situs disimpan.');
    }
}
