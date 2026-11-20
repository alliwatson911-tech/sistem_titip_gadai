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
        $aturan = [
            'produk' => ['required', 'integer'],
            'taksiran' => ['required', 'integer', 'min:1', 'max:10000000000'],
            'ltv_bp' => ['required', 'integer', 'min:1', 'max:10000'],
            'hari' => ['nullable', 'integer', 'min:1', 'max:120'],
        ];

        if ($request->filled('hari')) {
            $aturan['periode'] = ['nullable', 'integer', 'min:1', 'max:'.LoanCalculator::MAKS_PERIODE];
        } else {
            $aturan['periode'] = ['required', 'integer', 'min:1', 'max:'.LoanCalculator::MAKS_PERIODE];
        }

        $data = $request->validate($aturan, [
            'taksiran.required' => 'Isi taksiran barang.',
            'taksiran.integer' => 'Taksiran harus berupa angka.',
            'taksiran.min' => 'Taksiran harus lebih dari nol.',
            'taksiran.max' => 'Taksiran terlalu besar.',
            'produk.required' => 'Pilih jenis barang.',
            'produk.integer' => 'Jenis barang tidak valid.',
            'ltv_bp.required' => 'Rasio nilai yang diterima wajib diisi.',
            'ltv_bp.integer' => 'Rasio nilai yang diterima tidak valid.',
            'ltv_bp.min' => 'Rasio nilai yang diterima terlalu kecil.',
            'ltv_bp.max' => 'Rasio nilai yang diterima terlalu besar.',
            'hari.integer' => 'Durasi hari tidak valid.',
            'hari.min' => 'Durasi minimal 1 hari.',
            'hari.max' => 'Durasi maksimal 120 hari.',
            'periode.required' => 'Pilih durasi penitipan.',
            'periode.integer' => 'Durasi penitipan tidak valid.',
            'periode.min' => 'Durasi penitipan tidak valid.',
            'periode.max' => 'Durasi penitipan melebihi batas perpanjangan.',
        ]);

        $produk = Product::where('is_active', true)->find($data['produk']);
        if (! $produk) {
            return response()->json(['pesan' => 'Jenis barang tidak ditemukan.'], 422);
        }

        $hari = isset($data['hari']) && $data['hari'] !== null ? (int) $data['hari'] : null;
        $periode = isset($data['periode']) && $data['periode'] !== null ? (int) $data['periode'] : 1;

        try {
            $hasil = LoanCalculator::simulasi(LoanCalculator::dariProduk($produk), (int) $data['taksiran'], (int) $data['ltv_bp'], $periode, $hari);
        } catch (InvalidArgumentException $e) {
            return response()->json(['pesan' => $e->getMessage()], 422);
        }

        return response()->json($hasil + ['produk' => $produk->nama, 'jatuh_tempo' => now()->addDays($hasil['hari_total'])->toDateString()]);
    }
}
