@extends('layouts.publik')

@section('judul', 'Simulasi Jasa Penitipan — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Hitung estimasi nilai yang diterima, jasa penitipan durasi 1 atau 2 minggu, dan perkiraan total pelunasan sebelum berkunjung ke loket cabang.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Simulasi Jasa' => '']" />
            <div class="halaman-header">
                <p class="kicker">Kalkulator Transparan</p>
                <h1>Hitung Simulasi Jasa Penitipan</h1>
                <p class="pengantar">Pilih jenis barang, tentukan nilai yang diterima, dan pilih durasi 1 minggu atau 2 minggu. Kami rincikan estimasi jasa penitipan dan simulasi perpanjangan di muka tanpa biaya terselubung.</p>
            </div>
        </div>
        @include('publik.partials.simulasi')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection
