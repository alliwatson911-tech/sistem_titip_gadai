@extends('layouts.publik')

@section('judul', 'Sambutan Pimpinan — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Sambutan resmi dari pimpinan PERSIDEN HP Titip Gadai terkait komitmen pelayanan penitipan barang terpercaya di Makassar.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Tentang Kami" :backUrl="route('publik.tentang')" :crumbs="['Tentang Kami' => route('publik.tentang'), 'Sambutan' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Tentang Kami</p>
                <h1>Sambutan Pimpinan</h1>
                <p class="pengantar">Komitmen dan arah pelayanan PERSIDEN HP Titip Gadai dalam memberikan solusi penitipan barang berharga yang aman, cepat, dan terpercaya bagi masyarakat.</p>
            </div>

            <div class="panel-form" style="padding: clamp(1.5rem, 4vw, 3rem); margin-bottom: 3.5rem; background: #fff;" data-muncul>
                <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 280px;">
                        <span style="display: inline-block; font-size: .8rem; font-weight: 600; padding: .35rem .75rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px; margin-bottom: 1rem;">
                            [ISI: Menunggu Teks Resmi dari Pimpinan]
                        </span>

                        <h2 style="font-size: 1.5rem; color: var(--tinta); margin-bottom: 1rem; line-height: 1.3;">
                            Membangun Layanan Titip Gadai yang Nyaman, Jelas, dan Menenangkan
                        </h2>

                        <div style="font-size: 1rem; line-height: 1.8; color: var(--tinta-2);">
                            <p style="margin-bottom: 1rem;">
                                Selamat datang di portal resmi <strong>PERSIDEN HP Titip Gadai</strong>. Kami hadir di tengah masyarakat Kota Makassar dan sekitarnya dengan visi memberikan solusi finansial jangka pendek melalui layanan penitipan barang bergerak dan surat berharga yang transparan, mudah diakses, dan menjamin keamanan barang titipan Anda.
                            </p>
                            <p style="margin-bottom: 1rem; padding: 1rem 1.25rem; background: var(--kertas-2); border-left: 4px solid var(--kuningan); font-style: italic; color: var(--muted);">
                                <em>"Teks narasi sambutan lengkap dari pimpinan PERSIDEN HP akan diperbarui setelah materi resmi diserahkan oleh klien."</em>
                            </p>
                            <p style="margin-bottom: 0;">
                                Seluruh operasional kami di 7 cabang berpegang pada prinsip kejelasan di muka: nilai yang diterima dihitung terbuka, jangka waktu fleksibel (1 minggu atau 2 minggu), dan barang titipan dijaga dengan penuh tanggung jawab sampai saat penebusan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

