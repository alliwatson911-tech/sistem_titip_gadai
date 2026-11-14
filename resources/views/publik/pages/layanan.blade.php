@extends('layouts.publik')

@section('judul', 'Layanan Titip Gadai — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Daftar barang dan surat berharga yang bisa dititip di cabang PERSIDEN HP: Laptop, HP, Tab, Kamera, BPKB Mobil, BPKB Motor, Sertifikat Properti, dan Surat Berharga.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Layanan' => '']" />
            <div class="halaman-header">
                <p class="kicker">Daftar Barang &amp; Jaminan</p>
                <h1>Layanan Titip Gadai PERSIDEN HP</h1>
                <p class="pengantar">Pilihan barang berharga dan dokumen legal yang dapat Anda titipkan di 7 cabang PERSIDEN HP di Makassar dan Gowa dengan pemeriksaan transparan, pilihan durasi 1 atau 2 minggu, serta keamanan terjamin.</p>
            </div>
        </div>
        @include('publik.partials.produk')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection
