@php
    $daftarKategori = [
        [
            'key' => 'emas',
            'nama' => 'Gadai Emas',
            'label_panjang' => 'Gadai Emas & Perhiasan',
            'deskripsi' => 'Terima jaminan emas perhiasan dan logam mulia.',
            'plafon_badge' => 'Kadar s.d. 24 Karat',
            'fitur' => 'Uji Karat Akurat & Timbangan Digital',
        ],
        [
            'key' => 'elektronik',
            'nama' => 'Gadai Elektronik',
            'label_panjang' => 'Gadai Elektronik & Gadget',
            'deskripsi' => 'Terima jaminan barang elektronik.',
            'plafon_badge' => 'Plafon s.d. 80%',
            'fitur' => 'Laptop, HP, TV, Kamera & Konsol',
        ],
        [
            'key' => 'kendaraan',
            'nama' => 'Gadai BPKB Kendaraan',
            'label_panjang' => 'Gadai BPKB Mobil & Motor',
            'deskripsi' => 'Terima jaminan BPKB kendaraan motor dan mobil.',
            'plafon_badge' => 'Fisik Unit Tetap Dipakai',
            'fitur' => 'Mobil & Motor Pribadi / Niaga',
        ],
        [
            'key' => 'properti',
            'nama' => 'Gadai Sertifikat Properti',
            'label_panjang' => 'Gadai Sertifikat Rumah & Tanah',
            'deskripsi' => 'Terima jaminan sertifikat rumah dan tanah.',
            'plafon_badge' => 'Plafon s.d. Rp 1 Milyar',
            'fitur' => 'SHM / SHGB & Verifikasi Resmi BPN',
        ],
        [
            'key' => 'berharga',
            'nama' => 'Gadai Surat Berharga',
            'label_panjang' => 'Gadai Barang & Surat Berharga',
            'deskripsi' => 'Terima jaminan instrumen & surat berharga resmi.',
            'plafon_badge' => 'Agunan Khusus',
            'fitur' => 'Obligasi, Deposito & Aset Berharga',
        ],
    ];
@endphp

<section class="bagian bagian--kategori" id="produk" data-spy="produk" aria-labelledby="judul-kategori">
    <span id="layanan"></span>
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Katalog Jaminan</p>
            <div>
                <h2 id="judul-kategori">Pilih jaminan yang Anda punya</h2>
                <p class="pengantar" style="margin-top:0.75rem">Barang berharga Anda dinilai secara adil dan transparan, lalu disimpan dengan aman.</p>
            </div>
        </div>

        <!-- Grid Kartu Kategori Sesuai Referensi -->
        <div class="kategori-showcase-grid">
            @foreach ($daftarKategori as $kat)
                <a href="{{ route('publik.layanan', ['kategori' => $kat['key']]) }}#katalog" class="kartu-kategori" data-muncul>
                    <!-- Visual Ilustrasi Kategori -->
                    <div class="kartu-kategori-visual">
                        <span class="kartu-kategori-badge">{{ $kat['plafon_badge'] }}</span>
                        @include('publik.partials.kategori-ilustrasi', ['jenis' => $kat['key'], 'id' => 'beranda-'.$kat['key']])
                    </div>

                    <!-- Isi Kartu Kategori -->
                    <div class="kartu-kategori-body">
                        <h3>{{ $kat['nama'] }}</h3>
                        <p>{{ $kat['deskripsi'] }}</p>
                        <div class="kartu-kategori-meta">
                            <span class="kategori-subfitur">{{ $kat['fitur'] }}</span>
                        </div>
                        <div class="kartu-kategori-cta">
                            <span class="link-kategori-selengkapnya">
                                Selengkapnya
                                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Bar Aksi Tambahan untuk Eksplorasi Penuh -->
        <div class="kategori-showcase-bawah" data-muncul>
            <div class="showcase-bawah-info">
                <strong>Ingin menghitung estimasi pinjaman Anda sekarang?</strong>
                <span>Gunakan kalkulator simulasi titip gadai resmi dengan rincian biaya transparan di muka.</span>
            </div>
            <div class="showcase-bawah-tombol">
                <a href="{{ route('publik.layanan') }}" class="btn btn--kecil">Lihat Semua Rincian Produk &rarr;</a>
                <a href="#simulasi" class="btn btn--utama btn--kecil">Hitung Simulasi Gadai</a>
            </div>
        </div>
    </div>
</section>

