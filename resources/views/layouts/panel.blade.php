<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Panel') — linygadai</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preload" href="{{ asset('fonts/instrument-sans-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/situs.css') }}?v={{ filemtime(public_path('css/situs.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}?v={{ filemtime(public_path('css/panel.css')) }}">
</head>
<body class="panel">
@php $u = auth()->user(); $nav = fn (string $pola) => request()->is($pola) ? 'page' : 'false'; @endphp
<a class="lompat" href="#isi">Lewati ke isi halaman</a>
@include('partials.banner-uji')
<header class="p-kepala">
    <div class="dalam">
        <a class="merek" href="{{ route('panel') }}">
            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#F4EFE6" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            linygadai
        </a>
        <div class="p-user">
            <span>{{ $u->name }}</span>
            <span class="peran">{{ $u->role->label() }}@if ($u->branch) · {{ $u->branch->kode }}@endif</span>
            <form method="post" action="{{ route('keluar') }}">@csrf <button type="submit">Keluar</button></form>
        </div>
    </div>
</header>
<div class="p-badan">
    <nav class="p-nav" aria-label="Menu panel">
        <a href="{{ route('panel') }}" aria-current="{{ request()->routeIs('panel') ? 'page' : 'false' }}">Ringkasan</a>
        @can('pengajuan.lihat')<a href="{{ route('panel.pengajuan.index') }}" aria-current="{{ $nav('panel/pengajuan*') }}">Pengajuan</a>@endcan
        @can('gadai.lihat')<a href="{{ route('panel.gadai.index') }}" aria-current="{{ $nav('panel/gadai*') }}">Gadai berjalan</a>@endcan
        @canany(['lelang.kelola', 'lelang.catat'])<a href="{{ route('panel.lelang.index') }}" aria-current="{{ $nav('panel/lelang*') }}">Lelang</a>@endcanany
        @can('anggota.lihat')<a href="{{ route('panel.anggota') }}" aria-current="{{ $nav('panel/anggota*') }}">Anggota</a>@endcan
        @can('profil.ubah')<a href="{{ route('panel.profil') }}" aria-current="{{ $nav('panel/profil*') }}">Profil &amp; KTP</a>@endcan
        @canany(['konten.lihat', 'cabang.kontak', 'konten.situs'])
            <span class="grup">Konten situs</span>
            @can('konten.lihat')<a href="{{ route('panel.pengumuman.index') }}" aria-current="{{ $nav('panel/pengumuman*') }}">Pengumuman</a>
            <a href="{{ route('panel.faq.index') }}" aria-current="{{ $nav('panel/faq*') }}">FAQ &amp; informasi</a>@endcan
            @can('cabang.kontak')<a href="{{ route('panel.kontak.index') }}" aria-current="{{ $nav('panel/kontak*') }}">Kontak cabang</a>@endcan
            @can('konten.situs')<a href="{{ route('panel.situs') }}" aria-current="{{ $nav('panel/situs*') }}">Teks &amp; izin situs</a>@endcan
        @endcanany
        @canany(['laporan.cabang', 'laporan.harian', 'laporan.konsolidasi'])
            <span class="grup">Laporan</span>
            @can('laporan.harian')<a href="{{ route('panel.laporan.harian') }}" aria-current="{{ $nav('panel/laporan/harian*') }}">Harian saya</a>@endcan
            @can('laporan.cabang')<a href="{{ route('panel.laporan.cabang') }}" aria-current="{{ request()->routeIs('panel.laporan.cabang') ? 'page' : 'false' }}">Laporan cabang</a>@endcan
            @can('laporan.konsolidasi')<a href="{{ route('panel.laporan.konsolidasi') }}" aria-current="{{ $nav('panel/laporan/konsolidasi*') }}">Konsolidasi</a>@endcan
        @endcanany
        @canany(['pengguna.kelola', 'audit.lihat'])
            <span class="grup">Administrasi</span>
            @can('pengguna.kelola')<a href="{{ route('panel.pengguna.index') }}" aria-current="{{ $nav('panel/pengguna*') }}">Pengguna</a>@endcan
            @can('audit.lihat')<a href="{{ route('panel.audit.index') }}" aria-current="{{ $nav('panel/audit*') }}">Jejak audit</a>@endcan
        @endcanany
        <span class="grup">Akun saya</span>
        <a href="{{ route('panel.akun.sandi') }}" aria-current="{{ $nav('panel/akun/sandi*') }}">Ganti kata sandi</a>
        @if ($u->role->isStaff())<a href="{{ route('panel.akun.2fa') }}" aria-current="{{ $nav('panel/akun/2fa*') }}">Keamanan 2FA</a>@endif
        <span class="grup">Lainnya</span>
        <a href="{{ route('beranda') }}">Lihat situs</a>
    </nav>
    <main class="p-isi" id="isi">
        @if (session('status'))<p class="kabar" role="status">{{ session('status') }}</p>@endif
        @if (session('sandi_baru'))
            <p class="kabar kabar--rahasia" role="status">Kata sandi sementara untuk {{ session('sandi_baru')['email'] }}: <b>{{ session('sandi_baru')['sandi'] }}</b><br><small>Salin sekarang. Kata sandi ini tidak ditampilkan lagi. Minta pemiliknya menggantinya.</small></p>
        @endif
        @if ($errors->has('umum'))<p class="kabar kabar--galat" role="alert">{{ $errors->first('umum') }}</p>@endif
        @yield('isi')
    </main>
</div>
<script src="{{ asset('js/panel.js') }}?v={{ filemtime(public_path('js/panel.js')) }}" defer></script>
</body>
</html>
