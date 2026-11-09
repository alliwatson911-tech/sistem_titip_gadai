@extends('layouts.publik')

@section('judul', 'Standar Layanan & Keunggulan — linygadai')
@section('deskripsi', 'Prinsip standar layanan linygadai: keamanan brankas bersegel, taksiran terbuka, tanpa biaya tersembunyi.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Standar Layanan' => '']" />
            <div class="halaman-header">
                <p class="kicker">Komitmen Kami</p>
                <h1>Standar Layanan linygadai</h1>
                <p class="pengantar">Enam prinsip dasar pelayanan yang kami pegang teguh untuk memastikan setiap transaksi titip gadai berlangsung aman, adil, dan terpercaya bagi seluruh anggota.</p>
            </div>
        </div>
        @include('publik.partials.standar')
        @include('publik.partials.banner-member')
    </main>
    @include('publik.partials.kaki')
@endsection

