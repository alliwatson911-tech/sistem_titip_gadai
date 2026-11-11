@extends('layouts.publik')

@use('App\Support\Rupiah')

@section('judul', $p->nama . ' — Syarat, Taksiran & Dokumen Resmi | linygadai')
@section('deskripsi', $p->deskripsi . ' Hitung simulasi taksiran dana dan ajukan online di Cabang Makassar.')

@section('badan')
    @include('publik.partials.kepala')

    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb
                backText="Kembali ke Layanan"
                :backUrl="route('publik.layanan')"
                :crumbs="[
                    'Layanan' => route('publik.layanan'),
                    $p->nama => ''
                ]"
            />

            <!-- Header Halaman Detail Jaminan -->
            <div class="halaman-header" style="margin-bottom: 2rem;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: .65rem; margin-bottom: .85rem;">
                    <span class="badge-plafon" style="font-size: .8rem; padding: .4rem .85rem;">
                        {{ $p->badge_label ?? 'Plafon s.d. ' . rtrim(rtrim(number_format($p->ltv_max_bp / 100, 1, ',', ''), '0'), ',') . '%' }}
                    </span>
                    <span class="nomor" style="font-size: .8rem;">{{ $p->labelKelompok() }}</span>
                </div>
                <h1 style="font-size: clamp(2.2rem, 5vw, 3.4rem); margin-bottom: 1rem;">{{ $p->nama }}</h1>
                <p class="pengantar" style="max-width: 72ch; font-size: 1.15rem; line-height: 1.65;">
                    {{ $p->deskripsi }}
                </p>

                <!-- Parameter Ringkas -->
                <div class="kartu-chips" style="margin-top: 1.25rem; gap: .65rem;">
                    <span class="chip" style="font-size: .85rem; padding: .45rem .85rem;"><strong>Tenor:</strong> {{ $p->tenor_hari }} hari (dapat diperpanjang)</span>
                    <span class="chip" style="font-size: .85rem; padding: .45rem .85rem;"><strong>Sewa Modal:</strong> {{ rtrim(rtrim(number_format($p->sewa_modal_per_hari_bp / 100, 2, ',', ''), '0'), ',') }}%/hari</span>
                    <span class="chip" style="font-size: .85rem; padding: .45rem .85rem;"><strong>Plafon Nilai:</strong> {{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</span>
                    @if(str_contains(strtolower($p->kategori_barang), 'bpkb'))
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: var(--lumut-muda); color: var(--lumut); border-color: rgba(62, 92, 74, .3);"><strong>Status Unit:</strong> Kendaraan Tetap Dipakai Nasabah</span>
                    @elseif(str_contains(strtolower($p->kategori_barang), 'sertifikat'))
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: var(--lumut-muda); color: var(--lumut); border-color: rgba(62, 92, 74, .3);"><strong>Status Fisik:</strong> Properti Tetap Dihuni / Dikelola</span>
                    @else
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: var(--kuningan-muda); color: var(--kuningan-tua);"><strong>Penyimpanan:</strong> Disegel Resmi di Brankas Cabang</span>
                    @endif
                </div>
            </div>

            <!-- Grid 2 Kolom Informasi Lengkap Jaminan -->
            <div class="dua-kolom" style="margin-bottom: 3.5rem;">
                <!-- Kolom Kiri: Dokumen Wajib & Syarat Fisik/Hukum -->
                <div>
                    <div class="syarat-card" style="margin-bottom: 2rem;">
                        <h3 style="font-size: 1.35rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .65rem; margin-bottom: 1.25rem;">
                            Dokumen Persyaratan Wajib
                        </h3>
                        <p style="color: var(--tinta-2); margin-bottom: 1rem; font-size: .95rem;">
                            Bawa dokumen asli dan kelengkapannya saat verifikasi di kantor Cabang Makassar:
                        </p>
                        <ul class="syarat" style="margin-bottom: 1.5rem;">
                            @foreach ($p->dokumen_wajib ?? ['KTP asli pemohon', 'Bukti kepemilikan yang sah'] as $dok)
                                <li>{{ $dok }}</li>
                            @endforeach
                        </ul>

                        <h3 style="font-size: 1.35rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .65rem; margin-bottom: 1.25rem; margin-top: 1.5rem;">
                            Ketentuan &amp; Kelaikan Jaminan
                        </h3>
                        <ul class="syarat">
                            @foreach ($p->syarat_ketentuan ?? ['Kondisi fisik barang normal dan dapat dibuktikan kepemilikannya.'] as $sk)
                                <li>{{ $sk }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Kolom Kanan: Prosedur Taksiran & Tempat Penyimpanan -->
                <div>
                    <div class="syarat-card" style="margin-bottom: 2rem;">
                        <h3 style="font-size: 1.35rem; border-bottom: 2px solid var(--tinta); padding-bottom: .65rem; margin-bottom: 1.25rem;">
                            Proses &amp; Metode Penaksiran
                        </h3>
                        <p style="font-size: .98rem; line-height: 1.65; color: var(--tinta); margin-bottom: 1.25rem;">
                            {{ $p->proses_taksiran ?? 'Pemeriksaan menyeluruh dilakukan langsung oleh analis tersertifikasi untuk memberikan plafon pinjaman tertinggi yang wajar.' }}
                        </p>

                        <h3 style="font-size: 1.35rem; border-bottom: 2px solid var(--tinta); padding-bottom: .65rem; margin-bottom: 1.25rem; margin-top: 1.5rem;">
                            Standar Keamanan Penyimpanan
                        </h3>
                        <p style="font-size: .98rem; line-height: 1.65; color: var(--tinta-2); margin-bottom: 1.25rem;">
                            {{ $p->ketentuan_penyimpanan ?? 'Semua barang atau dokumen disimpan aman dalam brankas khusus bersegel resmi dengan pengawasan 24 jam.' }}
                        </p>

                        <!-- Aksi Cepat -->
                        <div style="background: var(--kertas); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--garis); margin-top: 1.5rem;">
                            <h4 style="font-size: 1.1rem; margin: 0 0 .5rem;">Ingin Mengajukan Titip Gadai Ini?</h4>
                            <p style="font-size: .9rem; margin-bottom: 1rem; color: var(--tinta-2);">
                                Ajukan permohonan secara online agar antrean dan persiapan berkas Anda di cabang langsung diproses tanpa menunggu lama.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: .75rem;">
                                <a class="btn btn--utama" href="{{ auth()->check() ? route('panel.pengajuan.create', ['produk' => $p->id]) : route('masuk') }}">
                                    Ajukan {{ $p->nama }}
                                </a>
                                <a class="btn" href="#kalkulator-detail">
                                    Simulasi Hitungan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Simulasi Khusus Produk Terpilih -->
            <div id="kalkulator-detail" style="scroll-margin-top: 90px; margin-bottom: 3.5rem;">
                <div class="kepala-bagian">
                    <p class="nomor">Simulasi {{ $p->nama }}</p>
                    <div>
                        <h2>Hitung Perkiraan Pinjaman &amp; Biaya</h2>
                        <p class="pengantar" style="margin-top: .75rem;">
                            Sesuaikan perkiraan nilai taksiran dan kebutuhan pinjaman Anda. Nilai pasti ditetapkan analis setelah pemeriksaan barang/dokumen di cabang.
                        </p>
                    </div>
                </div>

                @include('publik.partials.simulasi')
            </div>

            <!-- Navigasi ke Jenis Jaminan Lainnya -->
            <div style="border-top: 2px solid var(--garis); padding-top: 2.5rem; margin-bottom: 3rem;">
                <h3 style="font-size: 1.45rem; margin-bottom: 1.25rem;">Lihat Jenis Jaminan Lainnya</h3>
                <div style="display: flex; flex-wrap: wrap; gap: .65rem;">
                    @foreach ($produk as $item)
                        @if ($item->id !== $p->id)
                            <a href="{{ route('publik.layanan.detail', $item->slug) }}" class="btn btn--kecil" style="background: var(--putih);">
                                {{ $item->nama }} &rarr;
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        @include('publik.partials.banner-member')
    </main>

    @include('publik.partials.kaki')
@endsection

