@use('App\Support\Rupiah')
@php
    $kategoriAktif = request('kategori', $kategoriAktif ?? 'semua');
    $kelompokList = [
        'semua' => 'Semua Jaminan',
        'emas' => 'Gadai Emas',
        'elektronik' => 'Elektronik',
        'kendaraan' => 'BPKB Kendaraan',
        'properti' => 'Sertifikat Properti',
        'berharga' => 'Surat Berharga',
    ];
@endphp
<section class="bagian" id="produk" data-spy="produk" aria-labelledby="judul-produk">
    <span id="layanan"></span>
    <span id="katalog"></span>
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Katalog Jaminan</p>
            <div>
                <h2 id="judul-produk">Pilih Jaminan yang Anda Punya</h2>
                <p class="pengantar" style="margin-top:1rem">Barang berharga dan dokumen legal Anda dinilai secara adil dan transparan oleh analis profesional, lalu disimpan dengan standar keamanan tinggi di Cabang Makassar.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Daftar layanan sedang disiapkan.</p>
        @else
            <!-- Filter Kelompok Agunan Tabs -->
            <div class="filter-tabs" role="tablist" aria-label="Filter kategori jaminan yang diterima">
                @foreach ($kelompokList as $key => $label)
                    <button type="button" class="tab-pill {{ $key === $kategoriAktif ? 'aktif' : '' }}" data-filter="{{ $key }}" role="tab" aria-selected="{{ $key === $kategoriAktif ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>

            <!-- Grid Kartu Jaminan Seragam & Orisinal -->
            <div class="produk-grid" id="produk-katalog">
                @foreach ($produk as $p)
                    <article class="kartu-modern" data-kategori="{{ strtolower($p->kelompok_agunan ?? 'elektronik') }}" style="{{ $kategoriAktif === 'semua' || strtolower($p->kelompok_agunan ?? 'elektronik') === $kategoriAktif ? '' : 'display:none;' }}" data-muncul>
                        <!-- Area Visual Ikon Orisinal (Gaya Linygadai) -->
                        <div class="kartu-modern-visual" aria-hidden="true">
                            @include('publik.partials.ikon', ['k' => $p->kategori_barang])
                        </div>

                        <div class="kartu-modern-top">
                            <span class="badge-plafon">
                                {{ $p->badge_label ?? 'Plafon s.d. ' . rtrim(rtrim(number_format($p->ltv_max_bp / 100, 1, ',', ''), '0'), ',') . '%' }}
                            </span>
                            <span class="nomor" style="font-size: .72rem; margin: 0; color: var(--kuningan-tua);">
                                {{ $p->labelKelompok() }}
                            </span>
                        </div>

                        <div class="kartu-modern-body">
                            <h3>{{ $p->nama }}</h3>
                            <p>{{ $p->deskripsi }}</p>

                            <div class="kartu-chips">
                                <span class="chip">Tenor {{ $p->tenor_hari }} hari</span>
                                <span class="chip">Sewa {{ rtrim(rtrim(number_format($p->sewa_modal_per_hari_bp / 100, 2, ',', ''), '0'), ',') }}%/hari</span>
                                @if(strtolower($p->kelompok_agunan) === 'emas' || strtolower($p->kategori_barang) === 'emas')
                                    <span class="chip" style="color: var(--kuningan-tua); background: var(--kuningan-muda); border-color: rgba(184, 134, 43, .35);">Uji Karat Akurat</span>
                                    <span class="chip">Safe Deposit Box</span>
                                @elseif(str_contains(strtolower($p->kategori_barang), 'bpkb') || strtolower($p->kelompok_agunan) === 'kendaraan')
                                    <span class="chip" style="color: var(--lumut); background: var(--lumut-muda); border-color: rgba(15, 76, 58, .25);">Unit Tetap Dipakai</span>
                                @elseif(str_contains(strtolower($p->kategori_barang), 'sertifikat') || strtolower($p->kelompok_agunan) === 'properti')
                                    <span class="chip" style="color: var(--lumut); background: var(--lumut-muda); border-color: rgba(15, 76, 58, .25);">Properti Dihuni</span>
                                @else
                                    <span class="chip">Brankas Segel</span>
                                @endif
                            </div>

                            <div class="kartu-modern-footer">
                                <div class="kartu-harga-blok">
                                    <span>Plafon Taksiran</span>
                                    <b>{{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</b>
                                </div>

                                <div class="kartu-aksi-ganda">
                                    <a class="btn btn--utama btn--kecil" href="{{ request()->routeIs('beranda') ? '#simulasi' : route('publik.simulasi', ['produk' => $p->slug]) }}" data-pilih="{{ $p->slug }}">Hitung Simulasi</a>
                                    <a class="btn btn--kecil" href="{{ auth()->check() ? route('panel.pengajuan.create', ['produk' => $p->id]) : route('masuk') }}">Ajukan</a>
                                </div>

                                <div style="text-align: center; margin-top: .75rem; padding-top: .65rem; border-top: 1px dashed var(--garis);">
                                    <a href="{{ route('publik.layanan.detail', $p->slug) }}" class="link-selengkapnya">
                                        Rincian &amp; Syarat Dokumen &rarr;
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
