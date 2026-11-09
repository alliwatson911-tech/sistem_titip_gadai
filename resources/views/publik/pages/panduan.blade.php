@extends('layouts.publik')

@section('judul', 'Panduan Titip Gadai — linygadai')
@section('deskripsi', 'Panduan lengkap alur titip gadai elektronik: dari pendaftaran anggota, penyerahan barang di loket, sampai pelunasan dan tebus kembali.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Panduan Titip' => '']" />
            <div class="halaman-header">
                <p class="kicker">Alur & Syarat</p>
                <h1>Panduan Transaksi Titip Gadai</h1>
                <p class="pengantar">Ikuti tahapan mudah dan syarat kelengkapan untuk menitipkan barang elektronik Anda dengan aman dan tenang di cabang linygadai.</p>
            </div>
        </div>
        @include('publik.partials.panduan')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

