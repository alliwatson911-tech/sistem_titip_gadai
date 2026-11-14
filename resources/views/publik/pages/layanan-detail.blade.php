@extends('layouts.publik')

@use('App\Support\Rupiah')

@section('judul', $p->nama . ' — Syarat, Taksiran & Ketentuan | PERSIDEN HP Titip Gadai')
@section('deskripsi', $p->deskripsi . ' Hitung simulasi taksiran jasa dan kunjungi 7 loket cabang kami di Makassar dan Gowa.')

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
                    <span class="badge-plafon" style="font-size: .8rem; padding: .4rem .85rem; background: var(--navy-tua, #002665); color: #fff;">
                        Durasi 1 &ndash; 2 Minggu
                    </span>
                    <span class="nomor" style="font-size: .8rem; color: var(--kuningan-tua);">{{ $p->labelKelompok() }}</span>
                </div>
                <h1 style="font-size: clamp(2.2rem, 5vw, 3.4rem); margin-bottom: 1rem; color: var(--tinta);">{{ $p->nama }}</h1>
                <p class="pengantar" style="max-width: 72ch; font-size: 1.15rem; line-height: 1.65;">
                    {{ $p->deskripsi }}
                </p>

                <!-- Parameter Ringkas -->
                <div class="kartu-chips" style="margin-top: 1.25rem; gap: .65rem;">
                    <span class="chip" style="font-size: .85rem; padding: .45rem .85rem;"><strong>Durasi:</strong> 1 minggu atau 2 minggu</span>
                    <span class="chip" style="font-size: .85rem; padding: .45rem .85rem;"><strong>Nilai yang Diterima:</strong> {{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</span>
                    @if(str_contains(strtolower($p->kategori_barang), 'bpkb'))
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: rgba(30,90,60,.12); color: #1e5a3c;"><strong>Status Kendaraan:</strong> Unit Tetap Anda Gunakan</span>
                    @elseif(str_contains(strtolower($p->kategori_barang), 'sertifikat'))
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: rgba(30,90,60,.12); color: #1e5a3c;"><strong>Status Properti:</strong> Properti Tetap Dihuni / Dikelola</span>
                    @else
                        <span class="chip" style="font-size: .85rem; padding: .45rem .85rem; background: var(--kuningan-muda); color: var(--kuningan-tua);"><strong>Penyimpanan:</strong> Disimpan Aman di Cabang Tanpa Dipakai</span>
                    @endif
                </div>
            </div>

            <!-- Grid 2 Kolom Informasi Lengkap Jaminan -->
            <div class="dua-kolom" style="margin-bottom: 3.5rem;">
                <!-- Kolom Kiri: Dokumen Wajib & Syarat Fisik/Hukum -->
                <div>
                    <div class="syarat-card" style="margin-bottom: 2rem;">
                        <h2 style="font-size: 1.35rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .65rem; margin-bottom: 1.25rem; color: var(--tinta);">
                            Dokumen Persyaratan Wajib
                        </h2>
                        <p style="color: var(--tinta-2); margin-bottom: 1rem; font-size: .95rem;">
                            Bawa dokumen asli dan kelengkapannya saat berkunjung ke loket cabang:
                        </p>
                        <ul class="syarat" style="margin-bottom: 1.5rem;">
                            @foreach ($p->dokumen_wajib ?? ['KTP fisik asli yang masih berlaku', 'Bukti kelengkapan kepemilikan yang sah'] as $dok)
                                <li>{{ $dok }}</li>
                            @endforeach
                        </ul>

                        <h2 style="font-size: 1.35rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .65rem; margin-bottom: 1.25rem; margin-top: 1.5rem; color: var(--tinta);">
                            Ketentuan Fisik &amp; Kelaikan
                        </h2>
                        <ul class="syarat">
                            @foreach ($p->syarat_ketentuan ?? ['Kondisi fisik barang berfungsi normal dan dapat dibuktikan kepemilikannya.'] as $sk)
                                <li>{{ $sk }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Kolom Kanan: Prosedur Taksiran & Tempat Penyimpanan -->
                <div>
                    <div class="syarat-card" style="margin-bottom: 2rem;">
                        <h2 style="font-size: 1.35rem; border-bottom: 2px solid var(--tinta); padding-bottom: .65rem; margin-bottom: 1.25rem; color: var(--tinta);">
                            Proses Pemeriksaan di Loket
                        </h2>
                        <p style="font-size: .98rem; line-height: 1.65; color: var(--tinta); margin-bottom: 1.25rem;">
                            {{ $p->proses_taksiran ?? 'Pemeriksaan kondisi fisik, fungsi, dan kelengkapan dilakukan secara terbuka di depan Anda oleh petugas loket cabang.' }}
                        </p>

                        <h2 style="font-size: 1.35rem; border-bottom: 2px solid var(--tinta); padding-bottom: .65rem; margin-bottom: 1.25rem; margin-top: 1.5rem; color: var(--tinta);">
                            Penyimpanan Aman
                        </h2>
                        <p style="font-size: .98rem; line-height: 1.65; color: var(--tinta-2); margin-bottom: 1.25rem;">
                            {{ $p->ketentuan_penyimpanan ?? 'Barang fisik disimpan dalam loker penyimpanan khusus cabang tanpa pernah dioperasikan selama masa penitipan.' }}
                        </p>

                        <!-- Aksi Cepat ke Loket Cabang -->
                        <div style="background: var(--kertas-2); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--garis); margin-top: 1.5rem;">
                            <h3 style="font-size: 1.1rem; margin: 0 0 .5rem; color: var(--tinta);">Siap Menitipkan Barang Ini?</h3>
                            <p style="font-size: .9rem; margin-bottom: 1rem; color: var(--muted); line-height: 1.6;">
                                Kunjungi salah satu dari 7 cabang PERSIDEN HP di Makassar dan Gowa (08.00 – 22.00 WITA) dengan membawa barang dan KTP fisik asli.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: .75rem;">
                                <a class="tombol tombol--emas" href="{{ route('publik.cabang') }}" style="text-decoration: none; font-size: .9rem;">
                                    Lihat 7 Loket Cabang
                                </a>
                                <a class="tombol tombol--garis" href="#kalkulator-detail" style="text-decoration: none; font-size: .9rem;">
                                    Simulasi Jasa
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
                        <h2>Hitung Perkiraan Jasa Penitipan</h2>
                        <p class="pengantar" style="margin-top: .75rem;">
                            Perkiraan jasa penitipan untuk durasi 1 minggu atau 2 minggu. Angka pasti disepakati di loket cabang setelah pemeriksaan fisik barang.
                        </p>
                    </div>
                </div>

                @include('publik.partials.simulasi')
            </div>

            <!-- Navigasi ke Jenis Jaminan Lainnya -->
            <div style="border-top: 2px solid var(--garis); padding-top: 2.5rem; margin-bottom: 3rem;">
                <h3 style="font-size: 1.45rem; margin-bottom: 1.25rem; color: var(--tinta);">Lihat Pilihan Barang Lainnya</h3>
                <div style="display: flex; flex-wrap: wrap; gap: .65rem;">
                    @foreach ($produk as $item)
                        @if ($item->id !== $p->id)
                            <a href="{{ route('publik.layanan.detail', $item->slug) }}" class="btn btn--kecil" style="background: #fff; text-decoration: none;">
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
