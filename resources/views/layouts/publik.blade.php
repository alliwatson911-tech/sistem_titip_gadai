<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul')</title>
    <meta name="description" content="@yield('deskripsi')">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="theme-color" content="#1B2A2F">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="linygadai">
    <meta property="og:title" content="@yield('judul')">
    <meta property="og:description" content="@yield('deskripsi')">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="linygadai: titip gadai elektronik, biaya jelas">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preload" href="{{ asset('fonts/fraunces-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/instrument-sans-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/situs.css') }}?v={{ filemtime(public_path('css/situs.css')) }}">
    @stack('jsonld')
</head>
<body @if ($errors->any()) data-ke="akun" @endif>
    <a class="lompat" href="#isi">Lewati ke isi halaman</a>
    @include('partials.banner-uji')
    @yield('badan')
    @include('publik.partials.floating-cta')
    <script src="{{ asset('js/situs.js') }}?v={{ filemtime(public_path('js/situs.js')) }}" defer></script>
</body>
</html>
