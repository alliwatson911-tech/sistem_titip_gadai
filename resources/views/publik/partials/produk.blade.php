@use('App\Support\Rupiah')
@php
    $kategoriAktif = request('kategori', $kategoriAktif ?? 'semua');
    $kelompokList = [
        'semua' => 'Semua Barang',
        'elektronik' => 'Elektronik (Laptop, HP, Tab, Kamera)',
        'kendaraan' => 'Kendaraan (BPKB)',
        'properti' => 'Sertifikat Properti & Tanah',
        'berharga' => 'Surat Berharga & Lainnya',
    ];
@endphp
<section class="bagian" id="produk" data-spy="produk" aria-labelledby="judul-produk">
    <span id="layanan"></span>
    <span id="katalog"></span>
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Katalog Titip Gadai</p>
            <div>
                <h2 id="judul-produk">Pilih Barang yang Ingin Dititipkan</h2>
                <p class="pengantar" style="margin-top:1rem">Barang berharga dan dokumen Anda diperiksa secara langsung di loket cabang PERSIDEN HP. Pilihan durasi fleksibel 1 minggu atau 2 minggu dengan nilai yang diterima transparan di muka.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Daftar layanan sedang disiapkan.</p>
        @else
            <!-- Filter Kelompok Agunan Tabs -->
            <div class="filter-tabs" role="tablist" aria-label="Filter kategori barang yang diterima">
                @foreach ($kelompokList as $key => $label)
                    <button type="button" class="tab-pill {{ $key === $kategoriAktif ? 'aktif' : '' }}" data-filter="{{ $key }}" role="tab" aria-selected="{{ $key === $kategoriAktif ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>

            <!-- Grid Kartu Jaminan Seragam & Orisinal -->
            <div class="produk-grid" id="produk-katalog">
                @foreach ($produk as $p)
                    <article class="kartu-modern" data-kategori="{{ strtolower($p->kelompok_agunan ?? 'elektronik') }}" style="{{ $kategoriAktif === 'semua' || strtolower($p->kelompok_agunan ?? 'elektronik') === $kategoriAktif ? '' : 'display:none;' }}" data-muncul>
                        <!-- Area Visual Ikon Orisinal -->
                        <div class="kartu-modern-visual" aria-hidden="true">
                            @include('publik.partials.ikon', ['k' => $p->kategori_barang])
                        </div>

                        <div class="kartu-modern-top">
                            <span class="badge-plafon" style="background: var(--navy-tua, #002665); color: #fff;">
                                Durasi 1 &ndash; 2 Minggu
                            </span>
                            <span class="nomor" style="font-size: .72rem; margin: 0; color: var(--kuningan-tua);">
                                {{ $p->labelKelompok() }}
                            </span>
                        </div>

                        <div class="kartu-modern-body">
                            <h3>{{ $p->nama }}</h3>
                            <p>{{ $p->deskripsi }}</p>

                            <div class="kartu-chips">
                                @if(str_contains(strtolower($p->kategori_barang), 'bpkb') || strtolower($p->kelompok_agunan) === 'kendaraan')
                                    <span class="chip" style="color: #1e5a3c; background: rgba(30,90,60,.12);">BPKB Disimpan Aman</span>
                                @elseif(str_contains(strtolower($p->kategori_barang), 'sertifikat') || strtolower($p->kelompok_agunan) === 'properti')
                                    <span class="chip" style="color: #1e5a3c; background: rgba(30,90,60,.12);">Sertifikat Asli</span>
                                @else
                                    <span class="chip">Disimpan Tanpa Dipakai</span>
                                @endif
                            </div>

                            <div class="kartu-modern-footer">
                                <div class="kartu-harga-blok">
                                    <span>Nilai yang Diterima</span>
                                    <b>{{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</b>
                                </div>

                                <div class="kartu-aksi-ganda">
                                    <a class="btn btn--utama btn--kecil" href="{{ request()->routeIs('beranda') ? '#simulasi' : route('publik.simulasi', ['produk' => $p->slug]) }}" data-pilih="{{ $p->slug }}">Hitung Jasa</a>
                                    <a class="btn btn--kecil" href="{{ route('publik.cabang') }}">Loket Cabang</a>
                                </div>

                                <div style="text-align: center; margin-top: .75rem; padding-top: .65rem; border-top: 1px solid var(--garis);">
                                    <a href="{{ route('publik.layanan.detail', $p->slug) }}" class="link-selengkapnya">
                                        Rincian &amp; Syarat Kelengkapan &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
