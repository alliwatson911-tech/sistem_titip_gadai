@extends('layouts.publik')

@section('judul', 'Layanan Gadai Elektronik — linygadai')
@section('deskripsi', 'Katalog layanan titip gadai smartphone, laptop, kamera, televisi, smartwatch, dan konsol game di Makassar.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Layanan' => '']" />
            <div class="halaman-header">
                <p class="kicker">Katalog Layanan</p>
                <h1>Layanan Titip Gadai Elektronik</h1>
                <p class="pengantar">Pilihan layanan titip gadai barang elektronik dengan taksiran transparan, sewa modal kompetitif, dan penyimpanan aman bersegel di brankas Cabang Makassar.</p>
            </div>
        </div>
        @include('publik.partials.produk')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

