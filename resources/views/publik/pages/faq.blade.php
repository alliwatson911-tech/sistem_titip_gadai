@extends('layouts.publik')

@section('judul', 'FAQ & Tanya Jawab — linygadai')
@section('deskripsi', 'Pertanyaan yang paling sering diajukan seputar titip gadai elektronik, keamanan brankas, pelunasan, perpanjangan, dan pengambilan barang di linygadai.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['FAQ & Informasi' => '']" />
            <div class="halaman-header">
                <p class="kicker">Pusat Bantuan</p>
                <h1>Pertanyaan yang Sering Diajukan</h1>
                <p class="pengantar">Temukan jawaban cepat seputar syarat gadai, perhitungan bunga sewa, perpanjangan tenor, lelang, dan keamanan barang elektronik Anda di linygadai.</p>
            </div>
        </div>
        @include('publik.partials.faq')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

