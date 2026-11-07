<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/** Produk contoh. Angka LTV, sewa modal, dan administrasi adalah ASUMSI placeholder. */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            ['Gadai Smartphone', 'smartphone', 8000, 'Ponsel pintar berbagai merek dalam kondisi menyala dan terbebas dari kunci akun.'],
            ['Gadai Laptop', 'laptop', 8000, 'Laptop dan notebook lengkap dengan adaptor pengisi daya.'],
            ['Gadai Kamera', 'kamera', 7500, 'Kamera mirrorless dan DSLR beserta lensa dan baterai.'],
            ['Gadai Konsol Game', 'konsol', 7500, 'Konsol game dengan kontroler dan kabel bawaan.'],
            ['Gadai Televisi', 'televisi', 7000, 'Televisi layar datar dengan remote asli.'],
            ['Gadai Smartwatch', 'smartwatch', 7000, 'Jam tangan pintar dengan pengisi daya asli.'],
        ];

        foreach ($produk as $i => [$nama, $kategori, $ltv, $deskripsi]) {
            Product::updateOrCreate(['slug' => str($nama)->slug()->toString()], [
                'branch_id' => null,
                'nama' => $nama,
                'kategori_barang' => $kategori,
                'deskripsi' => $deskripsi,
                'pinjaman_min' => 100_000,
                'pinjaman_max' => 25_000_000,
                'ltv_max_bp' => $ltv,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 125_000,
                'urutan' => $i + 1,
                'is_active' => true,
            ]);
        }
    }
}
