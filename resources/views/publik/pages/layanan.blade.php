@extends('layouts.publik')

@section('judul', 'Layanan Titip Gadai Resmi — linygadai')
@section('deskripsi', 'Katalog layanan titip gadai laptop, smartphone, BPKB mobil/motor, sertifikat rumah/tanah, dan surat berharga di Makassar.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Layanan' => '']" />
            <div class="halaman-header">
                <p class="kicker">Katalog Jaminan Resmi</p>
                <h1>Layanan Titip Gadai Elektronik &amp; Agunan</h1>
                <p class="pengantar">Pilihan layanan titip gadai elektronik, BPKB kendaraan bermotor, sertifikat properti, dan surat berharga dengan taksiran profesional, sewa modal adil, dan jaminan keamanan legal di Cabang Makassar.</p>
            </div>
        </div>
        @include('publik.partials.produk')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

