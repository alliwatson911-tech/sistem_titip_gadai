<?php

namespace Tests\Unit;

use App\Services\LoanCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LoanCalculatorTest extends TestCase
{
    /** Syarat contoh = nilai asumsi awal produk smartphone. */
    private array $syarat = [
        'ltv_max_bp' => 8000, 'pinjaman_min' => 100000, 'pinjaman_max' => 25000000, 'tenor_hari' => 30,
        'sewa_modal_per_hari_bp' => 15, 'admin_bp' => 100, 'admin_min' => 5000, 'admin_max' => 125000,
    ];

    public function test_satu_periode_hitungan_manual(): void
    {
        // pokok 3.000.000 x 80% = 2.400.000; sewa 2.400.000 x 0,15% x 30 hari = 108.000; admin 1% = 24.000
        $h = LoanCalculator::simulasi($this->syarat, 3_000_000, 8000, 1);

        $this->assertSame(2_400_000, $h['pokok']);
        $this->assertSame(108_000, $h['sewa_modal_total']);
        $this->assertSame(24_000, $h['administrasi_total']);
        $this->assertSame(2_532_000, $h['total_tebus']);
        $this->assertSame(30, $h['hari_total']);
    }

    public function test_dua_periode_menggandakan_sewa_dan_administrasi(): void
    {
        $h = LoanCalculator::simulasi($this->syarat, 3_000_000, 8000, 2);

        $this->assertSame(216_000, $h['sewa_modal_total']);
        $this->assertSame(48_000, $h['administrasi_total']);
        $this->assertSame(2_664_000, $h['total_tebus']);
        $this->assertCount(2, $h['rincian']);
        $this->assertSame('Perpanjangan 1', $h['rincian'][1]['keterangan']);
    }

    public function test_administrasi_dibatasi_minimum_dan_maksimum(): void
    {
        $this->assertSame(5_000, LoanCalculator::administrasi($this->syarat, 100_000));      // 1% = 1.000 -> min
        $this->assertSame(125_000, LoanCalculator::administrasi($this->syarat, 25_000_000)); // 1% = 250.000 -> maks
    }

    public function test_pembulatan_setengah_ke_atas(): void
    {
        // 150.001 x 15 x 30 / 10000 = 6.750,045 -> 6.750 ; 100.100 x 15 x 30 / 10000 = 4.504,5 -> 4.505
        $this->assertSame(6_750, LoanCalculator::sewaModal($this->syarat, 150_001, 30));
        $this->assertSame(4_505, LoanCalculator::sewaModal($this->syarat, 100_100, 30));
    }

    public function test_menolak_persentase_di_atas_batas_produk(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LoanCalculator::simulasi($this->syarat, 3_000_000, 8100, 1);
    }

    public function test_menolak_pinjaman_di_bawah_minimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LoanCalculator::simulasi($this->syarat, 100_000, 7000, 1); // pokok 70.000 < min
    }

    public function test_menolak_pinjaman_melebihi_maksimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LoanCalculator::simulasi($this->syarat, 40_000_000, 8000, 1); // pokok 32 juta > 25 juta
    }

    public function test_menolak_periode_tidak_valid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LoanCalculator::simulasi($this->syarat, 3_000_000, 8000, 5);
    }
}
