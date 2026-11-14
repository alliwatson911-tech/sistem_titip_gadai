@extends('layouts.publik')

@section('judul', 'PERSIDEN HP — Titip Gadai Aman, Cepat, Terpercaya')
@section('deskripsi', 'PERSIDEN HP Titip Gadai Laptop, HP, Tab, Kamera, BPKB Motor & Mobil, Sertifikat, dan Surat Berharga di Makassar dan sekitarnya. Aman, Cepat, Terpercaya.')

@push('jsonld')
@php
    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'FinancialService',
        'name' => 'PERSIDEN HP Titip Gadai',
        'url' => url('/'),
        'description' => 'Layanan titip gadai barang elektronik dan surat berharga aman, cepat, terpercaya di Makassar.',
    ];
    if ($pusat?->alamat) { $ld['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $pusat->alamat, 'addressCountry' => 'ID']; }
    if ($pusat?->telepon) { $ld['telephone'] = $pusat->telepon; }
    if ($pusat?->email) { $ld['email'] = $pusat->email; }
    if ($pusat?->lat && $pusat?->lng) { $ld['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) $pusat->lat, 'longitude' => (float) $pusat->lng]; }
    $sama = collect($konten['sosmed'] ?? [])->filter(fn ($u) => is_string($u) && str_starts_with($u, 'https://'))->values()->all();
    if ($sama) { $ld['sameAs'] = $sama; }

    $faqLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faq->map(fn ($f) => ['@type' => 'Question', 'name' => $f->pertanyaan, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->jawaban]])->all(),
    ];
    $bendera = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
@endphp
<script type="application/ld+json">{!! json_encode($ld, $bendera) !!}</script>
@if ($faq->isNotEmpty())
<script type="application/ld+json">{!! json_encode($faqLd, $bendera) !!}</script>
@endif
@endpush

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi">
        @include('publik.partials.hero')
        @include('publik.partials.pengumuman')
        @include('publik.partials.standar')
        @include('publik.partials.faq')
        @include('publik.partials.kategori-showcase')
        @include('publik.partials.banner-member')
        @include('publik.partials.panduan')
        @include('publik.partials.simulasi')
        @include('publik.partials.tentang')
        @include('publik.partials.akun')
    </main>
    @include('publik.partials.kaki')
@endsection
