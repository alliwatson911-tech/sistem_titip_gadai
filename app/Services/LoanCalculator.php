<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

/**
 * Rumus biaya gadai. SUMBER TUNGGAL: dipakai simulasi publik sekarang, dan modul pembayaran di Tahap 4.
 * Semua uang bilangan bulat rupiah; persentase dalam basis poin (bp). Pembulatan: setengah ke atas.
 *
 * ASUMSI (docs/00-rancangan-tahap0.md §1.2, dapat diubah Owner lewat produk/kebijakan):
 *  - sewa modal = pokok x tarif harian x hari, dibayar per periode tenor;
 *  - administrasi = persen dari pokok, dengan batas bawah/atas, dibayar saat akad dan tiap perpanjangan.
 */
final class LoanCalculator
{
    public const MAKS_PERIODE = 4; // akad + 3 perpanjangan (kebijakan maks_perpanjangan = 3)

    /** @return array<string,int> syarat produk dalam bentuk datar (juga bentuk snapshot_syarat pada akad). */
    public static function dariProduk(Product $p): array
    {
        return [
            'ltv_max_bp' => (int) $p->ltv_max_bp,
            'pinjaman_min' => (int) $p->pinjaman_min,
            'pinjaman_max' => (int) $p->pinjaman_max,
            'tenor_hari' => (int) $p->tenor_hari,
            'sewa_modal_per_hari_bp' => (int) $p->sewa_modal_per_hari_bp,
            'admin_bp' => (int) $p->admin_bp,
            'admin_min' => (int) $p->admin_min,
            'admin_max' => (int) $p->admin_max,
        ];
    }

    /** Bagi dengan pembulatan setengah ke atas, hanya untuk bilangan non-negatif. */
    private static function bagiBulat(int $pembilang, int $penyebut): int
    {
        return intdiv($pembilang * 2 + $penyebut, $penyebut * 2);
    }

    public static function pokok(int $taksiran, int $ltvBp): int
    {
        return self::bagiBulat($taksiran * $ltvBp, 10000);
    }

    public static function sewaModal(array $syarat, int $pokok, int $hari): int
    {
        return self::bagiBulat($pokok * $syarat['sewa_modal_per_hari_bp'] * $hari, 10000);
    }

    public static function administrasi(array $syarat, int $pokok): int
    {
        $biaya = self::bagiBulat($pokok * $syarat['admin_bp'], 10000);

        return max($syarat['admin_min'], min($syarat['admin_max'], $biaya));
    }

    /**
     * @param  int  $taksiran  nilai taksiran barang (rupiah)
     * @param  int  $ltvBp     persentase pinjaman dari taksiran, basis poin (8000 = 80%)
     * @param  int  $periode   jumlah periode tenor yang disimulasikan (1 = tanpa perpanjangan)
     * @throws InvalidArgumentException bila masukan di luar batas produk
     */
    public static function simulasi(array $syarat, int $taksiran, int $ltvBp, int $periode = 1): array
    {
        if ($taksiran < 1) {
            throw new InvalidArgumentException('Taksiran harus lebih dari nol.');
        }
        if ($ltvBp < 1 || $ltvBp > $syarat['ltv_max_bp']) {
            throw new InvalidArgumentException('Persentase pinjaman maksimal '.rtrim(rtrim(number_format($syarat['ltv_max_bp'] / 100, 2, ',', ''), '0'), ',').'% untuk jenis barang ini.');
        }
        if ($periode < 1 || $periode > self::MAKS_PERIODE) {
            throw new InvalidArgumentException('Jumlah periode harus 1 sampai '.self::MAKS_PERIODE.'.');
        }

        $pokok = self::pokok($taksiran, $ltvBp);
        if ($pokok < $syarat['pinjaman_min']) {
            throw new InvalidArgumentException('Pinjaman di bawah batas minimum Rp '.number_format($syarat['pinjaman_min'], 0, ',', '.').'.');
        }
        if ($pokok > $syarat['pinjaman_max']) {
            throw new InvalidArgumentException('Pinjaman melebihi batas maksimum Rp '.number_format($syarat['pinjaman_max'], 0, ',', '.').'.');
        }

        $sewaPeriode = self::sewaModal($syarat, $pokok, $syarat['tenor_hari']);
        $adminPeriode = self::administrasi($syarat, $pokok);

        $rincian = [];
        for ($ke = 1; $ke <= $periode; $ke++) {
            $rincian[] = [
                'ke' => $ke,
                'keterangan' => $ke === 1 ? 'Akad awal' : 'Perpanjangan '.($ke - 1),
                'hari' => $syarat['tenor_hari'],
                'sewa_modal' => $sewaPeriode,
                'administrasi' => $adminPeriode,
            ];
        }

        $sewaTotal = $sewaPeriode * $periode;
        $adminTotal = $adminPeriode * $periode;

        return [
            'taksiran' => $taksiran,
            'ltv_bp' => $ltvBp,
            'pokok' => $pokok,
            'periode' => $periode,
            'hari_total' => $syarat['tenor_hari'] * $periode,
            'sewa_modal_per_periode' => $sewaPeriode,
            'administrasi_per_periode' => $adminPeriode,
            'sewa_modal_total' => $sewaTotal,
            'administrasi_total' => $adminTotal,
            'total_tebus' => $pokok + $sewaTotal + $adminTotal,
            'rincian' => $rincian,
        ];
    }
}
