<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PersidenHpBranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nonaktifkan cabang dummy lama A & B, perbarui PUSAT
        Branch::where('kode', 'PUSAT')->update([
            'nama' => 'Kantor Pusat PERSIDEN HP',
            'nama_tampilan' => 'Kantor Pusat PERSIDEN HP',
            'alamat' => 'Makassar, Sulawesi Selatan',
            'jam_buka' => '08.00 - 22.00 WITA',
            'is_active' => true,
        ]);

        Branch::whereIn('kode', ['A', 'B'])->update([
            'is_active' => false,
        ]);

        // 2. Daftar 7 Cabang PERSIDEN HP di Makassar & Gowa
        $cabangList = [
            [
                'kode' => 'ANTANG',
                'nama' => 'Persiden HP Antang Raya',
                'nama_tampilan' => 'Persiden HP Antang Raya',
                'alamat' => 'Jl. Antang Raya No.116, Antang, Kec. Manggala, Kota Makassar, Sulawesi Selatan 90234',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+Hp+Antang+Raya+Jl.+Antang+Raya+No.116+Makassar',
                'telepon' => null,
                'whatsapp' => null,
                'instagram' => null,
                'medsos_lain' => null,
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => '[ISI: Menunggu Konfirmasi Klien]',
                'is_active' => true,
            ],
            [
                'kode' => 'BTP',
                'nama' => 'Persiden HP BTP',
                'nama_tampilan' => 'Persiden HP BTP (Bumi Tamalanrea Permai)',
                'alamat' => 'Ruko BTP, blok M No.3, Jl. Bumi Tamalanrea Permai No.17, Tamalanrea, Kec. Tamalanrea, Kota Makassar, Sulawesi Selatan 90245',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+HP+Ruko+BTP+blok+M+No.3+Jl.+Bumi+Tamalanrea+Permai+Makassar',
                'telepon' => '0853 4245 3005',
                'whatsapp' => '0853 4245 3005',
                'instagram' => 'persiden_betepe',
                'medsos_lain' => ['facebook' => 'PersidenHp Btp'],
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => true,
                'catatan_verifikasi' => 'Terverifikasi via akun resmi Instagram @persiden_betepe',
                'is_active' => true,
            ],
            [
                'kode' => 'TODDOPULI',
                'nama' => 'Persiden HP Toddopuli',
                'nama_tampilan' => 'Persiden HP Toddopuli',
                'alamat' => 'Jalan Toddopuli depan, Jl. Pasar Hobby samping halte Damri No.50, Paropo, Kec. Panakkukang, Kota Makassar, Sulawesi Selatan 90222',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+Hp+toddopuli+Jl.+Pasar+Hobby+Makassar',
                'telepon' => '0853 4247 7009',
                'whatsapp' => '0853 4247 7009',
                'instagram' => null,
                'medsos_lain' => null,
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => 'Nomor 0853 4247 7009 dari direktori lama, wajib diverifikasi klien sebelum tampil publik.',
                'is_active' => true,
            ],
            [
                'kode' => 'GOWA',
                'nama' => 'President HP Gowa',
                'nama_tampilan' => 'President HP Gowa (Sultan Hasanuddin)',
                'alamat' => 'Jl. Sultan Hasanuddin No.164 B, Pandang Pandang, Kec. Somba Opu, Kabupaten Gowa, Sulawesi Selatan 90221',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=President+HP+Jl.+Sultan+Hasanuddin+No.164+B+Gowa',
                'telepon' => null,
                'whatsapp' => null,
                'instagram' => null,
                'medsos_lain' => null,
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => '[ISI: Menunggu Konfirmasi Klien]',
                'is_active' => true,
            ],
            [
                'kode' => 'DAYA',
                'nama' => 'Persiden HP Daya',
                'nama_tampilan' => 'Persiden HP Daya (Paccerakkang)',
                'alamat' => 'Jl. Paccerakkang No.82, Paccerakkang, Kec. Biringkanaya, Kota Makassar, Sulawesi Selatan 90241',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+HP+Daya+Jl.+Paccerakkang+No.82+Makassar',
                'telepon' => null,
                'whatsapp' => null,
                'instagram' => null,
                'medsos_lain' => null,
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => '[ISI: Menunggu Konfirmasi Klien]',
                'is_active' => true,
            ],
            [
                'kode' => 'RAPPOCINI',
                'nama' => 'Persiden HP Rappocini',
                'nama_tampilan' => 'Persiden HP Rappocini',
                'alamat' => 'Jl. Rappocini Raya No.187b, Bua Kana, Kec. Rappocini, Kota Makassar, Sulawesi Selatan 90222',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+hp+rappocini+Jl.+Rappocini+Raya+No.187b+Makassar',
                'telepon' => '0823 4809 9977',
                'whatsapp' => '0823 4809 9977',
                'instagram' => null,
                'medsos_lain' => ['facebook' => 'Persiden hp rappocini'],
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => 'Nomor 0823 4809 9977 dari Facebook, wajib diverifikasi klien sebelum tampil publik.',
                'is_active' => true,
            ],
            [
                'kode' => 'UJUNGBORI',
                'nama' => 'Persiden HP Ujung Bori',
                'nama_tampilan' => 'Persiden HP Ujung Bori',
                'alamat' => 'Jl. Ujung Bori No.22, Antang, Kec. Manggala, Kota Makassar, Sulawesi Selatan 90234',
                'gmaps_link' => 'https://www.google.com/maps/search/?api=1&query=Persiden+Hp+Ujung+Bori+No.22+Makassar',
                'telepon' => null,
                'whatsapp' => null,
                'instagram' => null,
                'medsos_lain' => null,
                'jam_buka' => '08.00 - 22.00 WITA',
                'is_verified' => false,
                'catatan_verifikasi' => '[ISI: Menunggu Konfirmasi Klien]',
                'is_active' => true,
            ],
        ];

        foreach ($cabangList as $item) {
            Branch::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }

        // 3. Sembunyikan produk lama yang tidak ada di daftar klien
        // (Emas & perhiasan, televisi, konsol game, smartwatch)
        Product::whereIn('slug', [
            'gadai-emas-perhiasan',
            'gadai-televisi',
            'gadai-konsol-game',
            'gadai-smartwatch',
        ])->update(['is_client_featured' => false]);

        // 4. Pastikan 8 Kategori Pilihan Klien Aktif dan Ditampilkan
        // Laptop, HP, Tab, Kamera, BPKB Mobil, BPKB Motor, Properti/Tanah, Surat Berharga
        Product::whereIn('slug', [
            'gadai-laptop',
            'gadai-smartphone',
            'gadai-kamera',
            'gadai-bpkb-mobil',
            'gadai-bpkb-motor',
            'gadai-sertifikat-rumah',
            'gadai-sertifikat-tanah',
            'gadai-barang-dan-surat-berharga-lainnya',
        ])->update([
            'is_client_featured' => true,
            'tenor_hari' => 14,
            'durasi_opsi_minggu' => 1,
        ]);

        // Tambahkan Tab/Tablet bila belum ada
        Product::updateOrCreate(
            ['slug' => 'gadai-tab-tablet'],
            [
                'nama' => 'Titip Gadai Tab / Tablet',
                'kategori_barang' => 'tablet',
                'kelompok_agunan' => 'elektronik',
                'deskripsi' => 'iPad, Samsung Galaxy Tab, dan tablet Android berbagai merek. Taksiran dinilai dari tipe layar, kapasitas memori, fungsionalitas stylus/keyboard, dan kelengkapan unit.',
                'pinjaman_min' => 300_000,
                'pinjaman_max' => 20_000_000,
                'ltv_max_bp' => 8000,
                'tenor_hari' => 14,
                'durasi_opsi_minggu' => 1,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 7,
                'admin_bp' => 100,
                'admin_min' => 10_000,
                'admin_max' => 100_000,
                'urutan' => 3,
                'is_active' => true,
                'is_client_featured' => true,
            ]
        );
    }
}
