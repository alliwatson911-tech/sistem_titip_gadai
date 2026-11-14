<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Kebijakan bisnis awal. SEMUA NILAI ADALAH ASUMSI placeholder
 * (docs/00-rancangan-tahap0.md §1.2); Owner menggantinya lewat panel.
 */
class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $nilai = [
            'limit_persetujuan_manager' => 5_000_000,   // pinjaman di atas ini wajib disetujui Manager (rupiah)
            'pinjaman_min' => 100_000,
            'pinjaman_max' => 25_000_000,
            'masa_tenggang_hari' => 3,
            'denda_harian' => 0,           // rupiah per hari; 0 = tanpa denda uang
            'maks_perpanjangan' => 3,
            'cicilan_min' => 50_000,
            'hari_pemberitahuan_lelang' => 7,
            'batas_bawa_barang_hari' => 7,           // pengajuan kedaluwarsa bila barang tak dibawa dalam hari ini
            'maks_pengajuan_terbuka' => 3,           // pengajuan berjalan per anggota
        ];

        foreach ($nilai as $kunci => $isi) {
            DB::table('policy_settings')->updateOrInsert(
                ['key' => $kunci, 'berlaku_mulai' => '2026-01-01'],
                ['value' => json_encode($isi), 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
