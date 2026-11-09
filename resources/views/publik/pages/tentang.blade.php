@extends('layouts.publik')

@section('judul', 'Tentang Kami & Lokasi Cabang — linygadai')
@section('deskripsi', 'Informasi profil perusahaan, kantor pusat, lokasi cabang operasional, jam layanan loket, serta kontak resmi linygadai di Makassar.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Tentang Kami' => '']" />
            <div class="halaman-header">
                <p class="kicker">Profil & Lokasi</p>
                <h1>Tentang Kami & Kantor Cabang</h1>
                <p class="pengantar">Hubungi tim linygadai atau kunjungi lokasi cabang terdekat kami di Makassar untuk konsultasi taksiran dan layanan titip gadai terpercaya.</p>
            </div>
        </div>
        @include('publik.partials.tentang')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

