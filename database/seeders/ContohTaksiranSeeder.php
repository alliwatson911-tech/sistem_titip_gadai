<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Contoh barang + kisaran taksiran untuk chip di simulasi publik.
 * SEMUA ANGKA ADALAH ASUMSI INDIKATIF (kisaran konservatif barang bekas), BUKAN harga pasar resmi.
 * Tim PERSIDEN HP wajib meninjau dan menggantinya dengan pengalaman transaksi nyata sebelum go-live.
 */
class ContohTaksiranSeeder extends Seeder
{
    /** @var array<string, list<array<string, int|string>>> */
    private const CONTOH = [
        'smartphone' => [
            ['label' => 'iPhone 11 64GB', 'min' => 3_000_000, 'max' => 4_000_000],
            ['label' => 'iPhone 13 128GB', 'min' => 5_500_000, 'max' => 7_000_000],
            ['label' => 'Samsung Galaxy A54 128GB', 'min' => 2_800_000, 'max' => 3_800_000],
            ['label' => 'Xiaomi Redmi Note 12', 'min' => 1_500_000, 'max' => 2_200_000],
            ['label' => 'Oppo Reno8 / Vivo V25', 'min' => 2_000_000, 'max' => 3_000_000],
        ],
        'tablet' => [
            ['label' => 'iPad Gen 9 64GB', 'min' => 3_000_000, 'max' => 4_200_000],
            ['label' => 'Samsung Galaxy Tab A8', 'min' => 1_500_000, 'max' => 2_200_000],
        ],
        'laptop' => [
            ['label' => 'Asus Vivobook Core i3 / 8GB / 256GB', 'min' => 3_500_000, 'max' => 5_000_000],
            ['label' => 'Lenovo IdeaPad Core i5 / 8GB / 512GB', 'min' => 5_500_000, 'max' => 7_500_000],
            ['label' => 'Acer Aspire 5 Core i5', 'min' => 5_000_000, 'max' => 7_000_000],
            ['label' => 'HP 14 Ryzen 5', 'min' => 4_500_000, 'max' => 6_500_000],
            ['label' => 'MacBook Air M1 8GB / 256GB', 'min' => 8_500_000, 'max' => 11_000_000],
        ],
        'kamera' => [
            ['label' => 'Canon EOS 1500D + lensa kit', 'min' => 3_500_000, 'max' => 5_000_000],
            ['label' => 'Sony A6000 body', 'min' => 5_000_000, 'max' => 7_000_000],
        ],
        'bpkb-motor' => [
            ['label' => 'Honda Beat tahun 2019', 'min' => 8_000_000, 'max' => 11_000_000],
            ['label' => 'Honda Vario 125 tahun 2021', 'min' => 13_000_000, 'max' => 17_000_000],
            ['label' => 'Yamaha NMAX tahun 2020', 'min' => 18_000_000, 'max' => 24_000_000],
        ],
        'bpkb-mobil' => [
            ['label' => 'Daihatsu Xenia tahun 2015', 'min' => 80_000_000, 'max' => 100_000_000],
            ['label' => 'Toyota Avanza tahun 2016', 'min' => 100_000_000, 'max' => 125_000_000],
            ['label' => 'Honda Brio tahun 2018', 'min' => 110_000_000, 'max' => 135_000_000],
        ],
    ];

    public function run(): void
    {
        foreach (self::CONTOH as $kategori => $contoh) {
            Product::where('kategori_barang', $kategori)->whereNull('contoh_taksiran')->get()->each->update(['contoh_taksiran' => $contoh]);
        }
    }
}
