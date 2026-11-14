<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kamus Terminologi & Konfigurasi Merek Resmi PERSIDEN HP
    |--------------------------------------------------------------------------
    | Sumber acuan: Dokumen Rencana Perubahan Web PERSIDEN HP (14 Nov 2026).
    | Tersimpan terpusat agar mudah diubah bila klien menetapkan istilah lain.
    */
    'merek' => [
        'nama' => 'PERSIDEN HP',
        'sub' => 'Titip Gadai',
        'lengkap' => 'PERSIDEN HP Titip Gadai',
        'tagline' => 'Aman · Cepat · Terpercaya',
        'wilayah' => 'Makassar dan Sekitarnya',
        'jam_standar' => '08.00 - 22.00 WITA',
        'deskripsi_meta' => 'PERSIDEN HP Titip Gadai Elektronik & Barang Berharga Aman, Cepat, Terpercaya di Makassar dan sekitarnya. Jam layanan buka setiap hari 08.00 - 22.00 WITA.',
        'kontak_pusat' => [
            'telepon' => '081342227229',
            'whatsapp' => '081342227229',
            'email' => 'pusatpersidenhp@gmail.com',
            'status_verifikasi' => 'Data awal dari halaman Facebook pusat PERSIDEN HP Makassar.',
        ],
    ],

    'istilah' => [
        'nasabah' => 'Penitip',
        'nasabah_jamak' => 'Para Penitip',
        'pinjaman' => 'Nilai yang Diterima',
        'bunga' => 'Jasa Penitipan',
        'sewa_modal' => 'Jasa Penitipan',
        'transaksi' => 'Penitipan',
        'agunan' => 'Barang Titipan / Jaminan',
        'tenor' => 'Durasi Titip',
        'status_transaksi' => [
            'aktif' => 'Aktif',
            'diperpanjang' => 'Diperpanjang',
            'jatuh_tempo' => 'Jatuh Tempo',
            'lunas' => 'Lunas',
            'dilelang' => 'Dilelang',
        ],
    ],

    'durasi' => [
        'pilihan' => [
            1 => '1 Minggu (7 Hari)',
            2 => '2 Minggu (14 Hari)',
        ],
        'default' => 1,
    ],

    'barang_diterima' => [
        'laptop' => [
            'nama' => 'Laptop / Notebook',
            'deskripsi' => 'Laptop berbagai merek & tipe dengan charger dan kelengkapan.',
            'ikon' => 'laptop',
        ],
        'hp' => [
            'nama' => 'Smartphone / HP',
            'deskripsi' => 'Smartphone Android dan iPhone resmi dalam kondisi prima.',
            'ikon' => 'smartphone',
        ],
        'tab' => [
            'nama' => 'Tablet / iPad',
            'deskripsi' => 'Tablet kerja, iPad, atau Tab Android beserta aksesori.',
            'ikon' => 'tablet',
        ],
        'kamera' => [
            'nama' => 'Kamera & Lensa',
            'deskripsi' => 'Kamera DSLR, mirrorless, dan lensa profesional.',
            'ikon' => 'kamera',
        ],
        'bpkb_motor' => [
            'nama' => 'BPKB Motor',
            'deskripsi' => 'Titip aman BPKB motor atas nama sendiri atau keluarga.',
            'ikon' => 'bpkb-motor',
        ],
        'bpkb_mobil' => [
            'nama' => 'BPKB Mobil',
            'deskripsi' => 'Titip BPKB mobil pribadi atau niaga resmi.',
            'ikon' => 'bpkb-mobil',
        ],
        'sertifikat' => [
            'nama' => 'Sertifikat Tanah / Rumah / Properti',
            'deskripsi' => 'Sertifikat hak milik (SHM) atau hak guna bangunan (HGB) resmi.',
            'ikon' => 'sertifikat',
        ],
        'surat_berharga' => [
            'nama' => 'Surat Berharga',
            'deskripsi' => 'Dokumen dan surat berharga bernilai ekonomi resmi.',
            'ikon' => 'dokumen',
        ],
        'lainnya' => [
            'nama' => 'Barang Berharga Lainnya',
            'deskripsi' => 'Barang berharga lain yang disepakati bersama di loket cabang.',
            'ikon' => 'kotak',
        ],
    ],
];

