@extends('layouts.publik')

@section('judul', 'Tentang Kami — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Profil perusahaan, sambutan pimpinan, visi & misi, legalitas, serta jaringan cabang PERSIDEN HP Titip Gadai di Makassar.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Tentang Kami' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Profil Perusahaan</p>
                <h1>Tentang PERSIDEN HP Titip Gadai</h1>
                <p class="pengantar">Mengenal lebih dekat PERSIDEN HP: komitmen layanan titip gadai terpercaya, kepatuhan legalitas, dan jaringan loket cabang di seluruh Makassar &amp; Gowa.</p>
            </div>

            <!-- 4 Navigasi Sub Menu Tentang Kami -->
            <div class="lembar-grid" style="margin-bottom: 3.5rem;">
                <a href="{{ route('publik.sambutan') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">01. Pimpinan</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Sambutan Pimpinan &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Komitmen manajemen dalam menghadirkan solusi penitipan barang yang aman, bersahabat, dan menenangkan bagi setiap penitip.
                    </p>
                </a>

                <a href="{{ route('publik.visi-misi') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">02. Fondasi</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Visi &amp; Misi &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Landasan nilai utama perusahaan: Aman, Cepat, dan Terpercaya dalam setiap langkah operasional loket cabang.
                    </p>
                </a>

                <a href="{{ route('publik.legalitas') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">03. Kepatuhan</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Dokumen Legalitas &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Transparansi informasi badan usaha, perizinan berusaha (NIB), dan kepatuhan hukum perdata dalam akad penitipan.
                    </p>
                </a>

                <a href="{{ route('publik.cabang') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">04. Lokasi</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Daftar 7 Cabang &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Alamat lengkap, peta Google Maps, dan jam operasional 08.00 – 22.00 WITA untuk seluruh cabang di Makassar dan Gowa.
                    </p>
                </a>
            </div>

            <!-- Ringkasan Komitmen Layanan -->
            <div class="syarat-highlight-box" style="margin-bottom: 3.5rem; border-left-color: var(--kuningan);" data-muncul>
                <h2 style="font-size: 1.3rem; color: var(--tinta); margin: 0 0 .75rem;">Nilai yang Kami Junjung</h2>
                <p style="font-size: .98rem; line-height: 1.7; color: var(--tinta-2); margin: 0 0 1rem;">
                    PERSIDEN HP Titip Gadai hadir bukan sekadar sebagai tempat gadai, melainkan mitra penitipan barang berharga dan surat berharga yang mengedepankan ketenangan pemilik barang. Setiap barang yang dititipkan disimpan rapi tanpa dipergunakan, dengan perhitungan jasa penitipan yang terbuka di muka.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="{{ route('publik.cara') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .9rem;">Pelajari Cara Penitipan</a>
                    <a href="{{ route('publik.kontak') }}" class="tombol tombol--emas" style="text-decoration: none; font-size: .9rem;">Hubungi Cabang Terdekat</a>
                </div>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection
