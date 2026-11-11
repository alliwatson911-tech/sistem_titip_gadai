<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'linygadai')</title>
    <meta name="robots" content="@yield('robots', 'noindex')">
    {{-- Gaya sementara Tahap 1. Sistem desain penuh (font, komponen) dibangun di Tahap 2. --}}
    <style>
        :root { --tinta:#0F4C3A; --kertas:#F8F5F0; --kuningan:#D4AF37; --lumut:#1B4332; --terakota:#B5472E; --garis:rgba(15, 76, 58, .16); }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--kertas); color:var(--tinta); font:16px/1.55 system-ui, "Segoe UI", sans-serif; }
        a { color:var(--tinta); }
        header.atas { display:flex; gap:1rem; align-items:center; justify-content:space-between; padding:.75rem 1.25rem; background:var(--tinta); color:var(--kertas); }
        header.atas a, header.atas button { color:var(--kertas); }
        header.atas .merek { font:700 1.25rem Georgia, serif; text-decoration:none; letter-spacing:.02em; }
        main { max-width:60rem; margin:0 auto; padding:1.5rem 1.25rem 4rem; }
        h1 { font:700 1.75rem/1.2 Georgia, serif; margin:.25rem 0 1rem; }
        label { display:block; font-weight:600; margin:.9rem 0 .25rem; }
        input, select { width:100%; max-width:26rem; padding:.65rem .7rem; border:1px solid var(--tinta); border-radius:3px; background:#fff; font:inherit; min-height:44px; }
        input[type=checkbox] { width:auto; min-height:0; }
        button, .tombol { display:inline-block; margin-top:1.1rem; padding:.7rem 1.2rem; background:var(--kuningan); color:var(--tinta); border:0; border-radius:3px; font:600 1rem inherit; cursor:pointer; text-decoration:none; min-height:44px; }
        button.pelan { background:transparent; border:1px solid currentColor; margin:0; padding:.35rem .8rem; min-height:36px; }
        :focus-visible { outline:3px solid var(--kuningan); outline-offset:2px; }
        .galat { color:var(--terakota); font-size:.9rem; margin:.25rem 0 0; }
        .info { background:#e6ede7; border-left:4px solid var(--lumut); padding:.6rem .9rem; margin:0 0 1rem; }
        table { border-collapse:collapse; width:100%; font-size:.95rem; }
        th, td { text-align:left; padding:.5rem .6rem; border-bottom:1px solid var(--garis); vertical-align:top; }
        .mono { font-family: ui-monospace, Consolas, monospace; }
        .lencana { display:inline-block; padding:.05rem .5rem; border:1px solid var(--tinta); border-radius:2px; font-size:.8rem; }
        nav.panel a { margin-right:1rem; }
        @media (prefers-reduced-motion: no-preference) { a, button { transition: background-color .15s; } }
    </style>
</head>
<body>
<header class="atas">
    <a class="merek" href="{{ route('beranda') }}">linygadai</a>
    <nav>
        @auth
            <a href="{{ route('panel') }}">Panel</a>
            <form method="post" action="{{ route('keluar') }}" style="display:inline">@csrf <button class="pelan" type="submit">Keluar</button></form>
        @else
            <a href="{{ route('masuk') }}">Masuk</a> · <a href="{{ route('daftar') }}">Daftar</a>
        @endauth
    </nav>
</header>
<main>
    @if (session('status'))<p class="info" role="status">{{ session('status') }}</p>@endif
    @yield('isi')
</main>
</body>
</html>
