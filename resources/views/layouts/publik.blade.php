<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>@yield('judul')</title>
    <meta name="description" content="@yield('deskripsi')">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#002665">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="PERSIDEN HP Titip Gadai">
    <meta property="og:title" content="@yield('judul', 'PERSIDEN HP — Titip Gadai Aman, Cepat, Terpercaya')">
    <meta property="og:description" content="@yield('deskripsi', config('istilah.merek.deskripsi_meta'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="PERSIDEN HP: Titip Gadai Aman, Cepat, Terpercaya Makassar">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preload" href="{{ asset('fonts/fraunces-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/instrument-sans-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/situs.css') }}?v={{ filemtime(public_path('css/situs.css')) }}">
    @stack('jsonld')
</head>
<body @if ($errors->any()) data-ke="akun" @endif>
    <a class="lompat" href="#isi">Lewati ke isi halaman</a>
    @yield('badan')
    @include('publik.partials.floating-cta')
    <script src="{{ asset('js/situs.js') }}?v={{ filemtime(public_path('js/situs.js')) }}" defer></script>
</body>
</html>
