@php
    $daftarKategori = [
        [
            'key' => 'elektronik',
            'nama' => 'Elektronik & Gadget',
            'label_panjang' => 'Laptop, HP, Tab & Kamera',
            'deskripsi' => 'Terima titip gadai Laptop, Smartphone, Tablet, dan Kamera.',
            'plafon_badge' => 'Durasi 1 &ndash; 2 Minggu',
            'fitur' => 'Pemeriksaan Terbuka di Loket',
        ],
        [
            'key' => 'kendaraan',
            'nama' => 'BPKB Kendaraan',
            'label_panjang' => 'BPKB Mobil & BPKB Motor',
            'deskripsi' => 'Terima jaminan BPKB kendaraan motor dan mobil.',
            'plafon_badge' => 'Fisik Unit Tetap Dipakai',
            'fitur' => 'Mobil & Motor Pribadi / Niaga',
        ],
        [
            'key' => 'properti',
            'nama' => 'Sertifikat Properti',
            'label_panjang' => 'Sertifikat Rumah & Tanah',
            'deskripsi' => 'Terima jaminan sertifikat rumah dan tanah sah.',
            'plafon_badge' => 'Properti Tetap Dikelola',
            'fitur' => 'SHM / SHGB & Dokumen Sah',
        ],
        [
            'key' => 'berharga',
            'nama' => 'Surat Berharga',
            'label_panjang' => 'Barang & Surat Berharga Lainnya',
            'deskripsi' => 'Terima jaminan instrumen legal & aset berharga.',
            'plafon_badge' => 'Penyimpanan Aman',
            'fitur' => 'Surat Berharga & Barang Bernilai',
        ],
    ];
@endphp

<section class="bagian bagian--kategori" id="produk" data-spy="produk" aria-labelledby="judul-kategori">
    <span id="layanan"></span>
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Katalog Titip Gadai</p>
            <div>
                <h2 id="judul-kategori">Pilih Barang yang Anda Titipkan</h2>
                <p class="pengantar" style="margin-top:0.75rem">Barang berharga dan dokumen legal Anda diperiksa terbuka di loket cabang dengan nilai yang diterima transparan di muka.</p>
            </div>
        </div>

        <!-- Grid Kartu Kategori Sesuai Referensi -->
        <div class="kategori-showcase-grid">
            @foreach ($daftarKategori as $kat)
                <a href="{{ route('publik.layanan', ['kategori' => $kat['key']]) }}#katalog" class="kartu-kategori" data-muncul>
                    <!-- Visual Ilustrasi Kategori -->
                    <div class="kartu-kategori-visual">
                        <span class="kartu-kategori-badge">{!! $kat['plafon_badge'] !!}</span>
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
                <strong>Ingin menghitung estimasi jasa penitipan Anda sekarang?</strong>
                <span>Gunakan kalkulator simulasi jasa penitipan resmi dengan durasi 1 atau 2 minggu.</span>
            </div>
            <div class="showcase-bawah-tombol">
                <a href="{{ route('publik.layanan') }}" class="btn btn--kecil">Lihat Semua Barang &rarr;</a>
                <a href="#simulasi" class="btn btn--utama btn--kecil">Hitung Simulasi Jasa</a>
            </div>
        </div>
    </div>
</section>
