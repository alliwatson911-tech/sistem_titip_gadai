<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Audit;
use App\Services\Situs;
use App\Support\Rupiah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/** Contoh barang + kisaran taksiran di simulasi publik (Owner). Angka ini ASUMSI indikatif, bukan harga pasar resmi. */
class ContohTaksiranController extends Controller
{
    private const MAKS_BARIS = 12;

    private const MAKS_NILAI = 10_000_000_000;

    public function index()
    {
        Gate::authorize('konten.situs');

        return view('panel.konten.contoh-taksiran', [
            'produk' => Product::where('is_active', true)->clientFeatured()->orderBy('id')->get(),
            'emas' => Situs::hargaEmas(),
        ]);
    }

    /** Harga emas 24K per gram untuk simulasi emas & perhiasan. Sumber wajib jelas; tanggal pembaruan tercatat otomatis. */
    public function emas(Request $r): RedirectResponse
    {
        Gate::authorize('konten.situs');
        $r->merge(['harga_emas_gram' => Rupiah::digit($r->input('harga_emas_gram'))]);
        $d = $r->validate([
            'harga_emas_gram' => ['required', 'integer', 'min:1', 'max:100000000'],
            'sumber' => ['required', 'string', 'max:120'],
        ], [
            'harga_emas_gram.required' => 'Isi harga emas per gram.',
            'harga_emas_gram.integer' => 'Harga emas harus berupa angka rupiah.',
            'harga_emas_gram.min' => 'Harga emas harus lebih dari nol.',
            'harga_emas_gram.max' => 'Harga emas terlalu besar.',
            'sumber.required' => 'Sebutkan sumber harga (mis. Antam, tanggal).',
        ]);

        $lama = Situs::hargaEmas();
        DB::transaction(function () use ($d) {
            foreach (['harga_emas_gram' => (string) $d['harga_emas_gram'], 'harga_emas_sumber' => $d['sumber']] as $kunci => $nilai) {
                DB::table('site_contents')->updateOrInsert(['branch_id' => null, 'kunci' => $kunci], ['nilai' => $nilai, 'updated_at' => now(), 'created_at' => now()]);
            }
        });
        Audit::log('situs.harga_emas', null, ['harga' => $lama['harga'], 'sumber' => $lama['sumber']], ['harga' => $d['harga_emas_gram'], 'sumber' => $d['sumber']]);

        return back()->with('status', 'Harga emas disimpan.');
    }

    public function update(Request $r, Product $produk): RedirectResponse
    {
        Gate::authorize('konten.situs');
        $bag = 'p'.$produk->id;
        $r->validateWithBag($bag, ['contoh' => ['nullable', 'string', 'max:2000']]);

        try {
            $contoh = $this->uraikan((string) $r->input('contoh', ''));
        } catch (ValidationException $e) {
            throw $e->errorBag($bag);
        }
        $produk->update(['contoh_taksiran' => $contoh ?: null]);

        return back()->with('status', 'Contoh taksiran '.$produk->nama.' disimpan.');
    }

    /**
     * Satu baris = "Nama contoh | terendah | tertinggi" (angka boleh memakai titik).
     *
     * @return list<array{label: string, min: int, max: int}>
     */
    private function uraikan(string $teks): array
    {
        $hasil = [];
        foreach (preg_split('/\R/', $teks) as $no => $baris) {
            if (trim($baris) === '') {
                continue;
            }
            $bagian = array_map('trim', explode('|', $baris));
            $label = $bagian[0];
            $angka = fn (string $v): ?int => preg_match('/^\d{1,3}(\.\d{3})+$|^\d+$/', $v) ? (int) str_replace('.', '', $v) : null;
            $min = $angka($bagian[1] ?? '');
            $maks = $angka($bagian[2] ?? '');
            $salah = match (true) {
                count($bagian) !== 3 || $label === '' => 'harus berbentuk "Nama contoh | terendah | tertinggi"',
                mb_strlen($label) > 60 => 'nama contoh maksimal 60 karakter',
                $min === null || $maks === null => 'nilai harus berupa angka bulat rupiah tanpa koma (contoh 3.500.000)',
                $min < 1 || $maks < $min => 'nilai terendah harus lebih dari nol dan tidak melebihi nilai tertinggi',
                $maks > self::MAKS_NILAI => 'nilai terlalu besar',
                default => null,
            };
            if ($salah) {
                throw ValidationException::withMessages(['contoh' => 'Baris '.($no + 1).': '.$salah.'.']);
            }
            $hasil[] = ['label' => $label, 'min' => $min, 'max' => $maks];
        }
        if (count($hasil) > self::MAKS_BARIS) {
            throw ValidationException::withMessages(['contoh' => 'Maksimal '.self::MAKS_BARIS.' contoh per jenis barang.']);
        }

        return $hasil;
    }
}
