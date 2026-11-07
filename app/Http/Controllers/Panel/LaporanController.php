<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\Audit;
use App\Services\LaporanService;
use App\Support\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $svc) {}

    /** Laporan cabang. Manager: cabangnya. Owner: pilih satu cabang atau semua. */
    public function cabang(Request $r)
    {
        Gate::authorize('laporan.cabang');
        [$dari, $sampai] = $this->periode($r);
        $u = $r->user();
        $owner = $u->role === Role::Owner;
        $cabangId = $owner ? ($r->integer('cabang') ?: null) : $u->branch_id;
        abort_if(! $owner && ! $cabangId, 403);

        $data = $this->svc->ringkas($dari, $sampai, $cabangId);
        $nama = $cabangId ? Branch::find($cabangId)?->nama : 'Semua cabang';

        if ($r->query('format') === 'csv') {
            Audit::log('laporan.ekspor', null, null, ['laporan' => 'cabang', 'cabang' => $nama, 'dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()]);
            $baris = [['Laporan cabang', $nama], ['Periode', $dari->toDateString().' s.d. '.$sampai->toDateString()], ['Metrik', 'Nilai']];
            foreach ($this->svc->baris($data) as [$label, $nilai]) {
                $baris[] = [$label, $nilai];
            }

            return $this->unduh($baris, 'laporan-cabang-'.$dari->toDateString().'-'.$sampai->toDateString().'.csv');
        }

        return view('panel.laporan.cabang', [
            'baris' => $this->svc->baris($data), 'data' => $data, 'nama' => $nama, 'dari' => $dari, 'sampai' => $sampai,
            'cabangId' => $cabangId, 'pilihanCabang' => $owner ? Branch::where('kode', '!=', 'PUSAT')->orderBy('kode')->get() : null,
        ]);
    }

    /** Laporan konsolidasi seluruh cabang (Owner). */
    public function konsolidasi(Request $r)
    {
        Gate::authorize('laporan.konsolidasi');
        [$dari, $sampai] = $this->periode($r);

        $cabang = Branch::where('is_active', true)->where('kode', '!=', 'PUSAT')->orderBy('kode')->get();
        $kolom = [];
        foreach ($cabang as $c) {
            $kolom[$c->nama] = $this->svc->baris($this->svc->ringkas($dari, $sampai, $c->id));
        }
        $kolom['Total'] = $this->svc->baris($this->svc->ringkas($dari, $sampai, null));

        if ($r->query('format') === 'csv') {
            Audit::log('laporan.ekspor', null, null, ['laporan' => 'konsolidasi', 'dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()]);
            $baris = [['Laporan konsolidasi'], ['Periode', $dari->toDateString().' s.d. '.$sampai->toDateString()], array_merge(['Metrik'], array_keys($kolom))];
            $pertama = reset($kolom);
            foreach ($pertama as $i => [$label]) {
                $baris[] = array_merge([$label], array_map(fn ($k) => $k[$i][1], $kolom));
            }

            return $this->unduh($baris, 'laporan-konsolidasi-'.$dari->toDateString().'-'.$sampai->toDateString().'.csv');
        }

        return view('panel.laporan.konsolidasi', ['kolom' => $kolom, 'dari' => $dari, 'sampai' => $sampai]);
    }

    /** Laporan harian teller: transaksi sendiri dan kas tunai bersih. */
    public function harian(Request $r)
    {
        Gate::authorize('laporan.harian');
        $d = $r->validate(['tanggal' => ['nullable', 'date', 'before_or_equal:today']], ['tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan.', 'tanggal.date' => 'Tanggal tidak valid.']);
        $tgl = Carbon::parse($d['tanggal'] ?? today()->toDateString())->startOfDay();

        return view('panel.laporan.harian', ['h' => $this->svc->harian($r->user(), $tgl), 'tgl' => $tgl]);
    }

    /** @return array{0:Carbon,1:Carbon} */
    private function periode(Request $r): array
    {
        $d = $r->validate(['dari' => ['nullable', 'date'], 'sampai' => ['nullable', 'date']], ['dari.date' => 'Tanggal awal tidak valid.', 'sampai.date' => 'Tanggal akhir tidak valid.']);
        $dari = Carbon::parse($d['dari'] ?? today()->startOfMonth()->toDateString())->startOfDay();
        $sampai = Carbon::parse($d['sampai'] ?? today()->toDateString())->startOfDay();
        abort_if($dari->gt($sampai) || $dari->diffInDays($sampai) > 366, 422, 'Periode tidak valid (awal setelah akhir, atau lebih dari 366 hari).');

        return [$dari, $sampai];
    }

    private function unduh(array $baris, string $nama): StreamedResponse
    {
        return response()->streamDownload(fn () => Csv::tulis($baris, fopen('php://output', 'w')), $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
