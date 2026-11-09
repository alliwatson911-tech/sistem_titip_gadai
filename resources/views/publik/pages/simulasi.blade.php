@extends('layouts.publik')

@section('judul', 'Kalkulator Simulasi Gadai — linygadai')
@section('deskripsi', 'Hitung estimasi pokok pinjaman, biaya sewa modal, biaya administrasi, dan total tebusan barang elektronik secara akurat sebelum datang ke cabang.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Simulasi' => '']" />
            <div class="halaman-header">
                <p class="kicker">Kalkulator Transparan</p>
                <h1>Hitung Simulasi Titip Gadai</h1>
                <p class="pengantar">Pilih jenis barang, masukkan nilai perkiraan taksiran, dan tentukan tenor titip. Kami rincikan seluruh komponen biaya di muka secara transparan tanpa pungutan terselubung.</p>
            </div>
        </div>
        @include('publik.partials.simulasi')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

