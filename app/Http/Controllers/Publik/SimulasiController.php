<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\LoanCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/** Simulasi biaya. Rumus ada di LoanCalculator (server) supaya sama dengan tagihan sebenarnya. */
class SimulasiController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'produk' => ['required', 'integer'],
            'taksiran' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'ltv_bp' => ['required', 'integer', 'min:1', 'max:10000'],
            'periode' => ['required', 'integer', 'min:1', 'max:'.LoanCalculator::MAKS_PERIODE],
        ], [
            'taksiran.required' => 'Isi taksiran barang.',
            'taksiran.integer' => 'Taksiran harus berupa angka.',
            'taksiran.min' => 'Taksiran harus lebih dari nol.',
            'taksiran.max' => 'Taksiran terlalu besar.',
        ]);

        $produk = Product::where('is_active', true)->find($data['produk']);
        if (! $produk) {
            return response()->json(['pesan' => 'Jenis barang tidak ditemukan.'], 422);
        }

        try {
            $hasil = LoanCalculator::simulasi(LoanCalculator::dariProduk($produk), (int) $data['taksiran'], (int) $data['ltv_bp'], (int) $data['periode']);
        } catch (InvalidArgumentException $e) {
            return response()->json(['pesan' => $e->getMessage()], 422);
        }

        return response()->json($hasil + ['produk' => $produk->nama, 'jatuh_tempo' => now()->addDays($hasil['hari_total'])->toDateString()]);
    }
}
