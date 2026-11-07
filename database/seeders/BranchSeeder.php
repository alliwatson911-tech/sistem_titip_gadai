<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

/**
 * Dua cabang operasional dan satu kantor pusat.
 * Data kontak TIDAK dikarang: dibaca dari .env bila Anda mengisinya, selain itu dikosongkan
 * (situs publik menyembunyikan butir yang kosong). Variabel .env per kode cabang (PUSAT, A, B):
 *   PUSAT_ALAMAT, PUSAT_TELEPON, PUSAT_WHATSAPP, PUSAT_EMAIL, PUSAT_LAT, PUSAT_LNG, PUSAT_JAM
 * Menjalankan ulang seeder hanya menimpa kolom yang terisi di .env.
 */
class BranchSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['kode' => 'PUSAT', 'nama' => 'Kantor Pusat'],
            ['kode' => 'A', 'nama' => 'Cabang A'],
            ['kode' => 'B', 'nama' => 'Cabang B'],
        ] as $cabang) {
            $k = $cabang['kode'];
            $dariEnv = array_filter([
                'alamat' => env("{$k}_ALAMAT"),
                'telepon' => env("{$k}_TELEPON"),
                'whatsapp' => env("{$k}_WHATSAPP"),
                'email' => env("{$k}_EMAIL"),
                'lat' => env("{$k}_LAT"),
                'lng' => env("{$k}_LNG"),
                'jam_buka' => env("{$k}_JAM"),
            ], fn ($v) => $v !== null && $v !== '');

            Branch::updateOrCreate(['kode' => $k], $cabang + ['is_active' => true] + $dariEnv);
        }
    }
}
