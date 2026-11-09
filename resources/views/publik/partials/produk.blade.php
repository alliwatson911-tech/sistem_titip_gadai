@use('App\Support\Rupiah')
@php
    $kategoriList = $produk->pluck('kategori_barang')->unique()->values();
@endphp
<section class="bagian" id="produk" data-spy="produk" aria-labelledby="judul-produk">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Katalog Titip</p>
            <div>
                <h2 id="judul-produk">Pilihan Produk Elektronik yang Diterima</h2>
                <p class="pengantar" style="margin-top:1rem">Pilih kategori barang elektronik Anda. Plafon taksiran, tenor titip, dan tarif sewa modal tercantum transparan dan dapat langsung dihitung di kalkulator simulasi.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Daftar produk sedang disiapkan.</p>
        @else
            <!-- Filter Kategori Tabs -->
            <div class="filter-tabs" role="tablist" aria-label="Filter kategori barang elektronik">
                <button type="button" class="tab-pill aktif" data-filter="semua" role="tab" aria-selected="true">Semua Barang</button>
                @foreach ($kategoriList as $kat)
                    <button type="button" class="tab-pill" data-filter="{{ strtolower($kat) }}" role="tab" aria-selected="false">{{ ucfirst($kat) }}</button>
                @endforeach
            </div>

            <!-- Grid Kartu Produk Seragam -->
            <div class="produk-grid" id="produk-katalog">
                @foreach ($produk as $p)
                    <article class="kartu-modern" data-kategori="{{ strtolower($p->kategori_barang) }}" data-muncul>
                        <div class="kartu-modern-top">
                            <span class="badge-plafon">
                                Plafon s.d. {{ rtrim(rtrim(number_format($p->ltv_max_bp / 100, 1, ',', ''), '0'), ',') }}%
                            </span>
                            <div class="kartu-icon-wrap" aria-hidden="true">
                                @include('publik.partials.ikon', ['k' => $p->kategori_barang])
                            </div>
                        </div>

                        <div class="kartu-modern-body">
                            <span class="nomor" style="font-size: .74rem; margin-bottom: .4rem;">{{ $p->kategori_barang }}</span>
                            <h3>{{ $p->nama }}</h3>
                            <p>{{ $p->deskripsi }}</p>

                            <div class="kartu-chips">
                                <span class="chip">Tenor {{ $p->tenor_hari }} hari</span>
                                <span class="chip">Sewa {{ rtrim(rtrim(number_format($p->sewa_modal_per_hari_bp / 100, 2, ',', ''), '0'), ',') }}%/hari</span>
                                <span class="chip">Brankas Segel</span>
                            </div>

                            <div class="kartu-modern-footer">
                                <div class="kartu-harga-blok">
                                    <span>Plafon Taksiran</span>
                                    <b>{{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</b>
                                </div>
                                <div class="kartu-aksi-ganda">
                                    <a class="btn btn--utama btn--kecil" href="#simulasi" data-pilih="{{ $p->slug }}">Hitung Simulasi</a>
                                    <a class="btn btn--kecil" href="{{ auth()->check() ? route('panel') : '#akun' }}">Ajukan</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
