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
@php
    $u = auth()->user();
    $nav = fn (string $pola) => request()->is($pola) ? 'page' : 'false';
    $kodeAkun = strtoupper(substr($u->role->value, 0, 3)).'-'.str_pad((string) $u->id, 3, '0', STR_PAD_LEFT);
    $lokasi = $u->branch?->nama ?? ($u->role === \App\Enums\Role::Owner ? 'Semua cabang' : 'linygadai');
@endphp
<a class="lompat" href="#isi">Lewati ke isi halaman</a>
<div class="p-kerangka">
    <aside class="p-samping">
        <a class="p-merek" href="{{ route('panel') }}">
            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#F4EFE6" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span><b>linygadai</b><small>Sistem operasional</small></span>
        </a>
        <div class="p-peran" aria-label="Peran dan lokasi">
            <span>Panel {{ $u->role->label() }}</span>
            <em>{{ $u->branch?->kode ?? ($u->role === \App\Enums\Role::Owner ? 'SEMUA' : '—') }}</em>
        </div>
        <nav class="p-nav" aria-label="Menu panel">
            <span class="grup">Menu utama</span>
            <a href="{{ route('panel') }}" aria-current="{{ request()->routeIs('panel') ? 'page' : 'false' }}">@include('partials.ikon', ['n' => 'beranda'])Ringkasan</a>
            @can('pengajuan.lihat')<a href="{{ route('panel.pengajuan.index') }}" aria-current="{{ $nav('panel/pengajuan*') }}">@include('partials.ikon', ['n' => 'pengajuan'])Pengajuan</a>@endcan
            @can('gadai.lihat')<a href="{{ route('panel.gadai.index') }}" aria-current="{{ $nav('panel/gadai*') }}">@include('partials.ikon', ['n' => 'gadai'])Gadai berjalan</a>@endcan
            @canany(['lelang.kelola', 'lelang.catat'])<a href="{{ route('panel.lelang.index') }}" aria-current="{{ $nav('panel/lelang*') }}">@include('partials.ikon', ['n' => 'lelang'])Lelang</a>@endcanany
            @can('anggota.lihat')<a href="{{ route('panel.anggota') }}" aria-current="{{ $nav('panel/anggota*') }}">@include('partials.ikon', ['n' => 'anggota'])Anggota</a>@endcan
            @can('profil.ubah')<a href="{{ route('panel.profil') }}" aria-current="{{ $nav('panel/profil*') }}">@include('partials.ikon', ['n' => 'profil'])Profil &amp; KTP</a>@endcan
            @canany(['konten.lihat', 'cabang.kontak', 'konten.situs'])
                <span class="grup">Konten situs</span>
                @can('konten.lihat')<a href="{{ route('panel.pengumuman.index') }}" aria-current="{{ $nav('panel/pengumuman*') }}">@include('partials.ikon', ['n' => 'pengumuman'])Pengumuman</a>
                <a href="{{ route('panel.faq.index') }}" aria-current="{{ $nav('panel/faq*') }}">@include('partials.ikon', ['n' => 'faq'])FAQ &amp; informasi</a>@endcan
                @can('cabang.kontak')<a href="{{ route('panel.kontak.index') }}" aria-current="{{ $nav('panel/kontak*') }}">@include('partials.ikon', ['n' => 'lokasi'])Kontak cabang</a>@endcan
                @can('konten.situs')<a href="{{ route('panel.situs') }}" aria-current="{{ $nav('panel/situs*') }}">@include('partials.ikon', ['n' => 'situs'])Teks &amp; izin situs</a>@endcan
            @endcanany
            @canany(['laporan.cabang', 'laporan.harian', 'laporan.konsolidasi'])
                <span class="grup">Laporan</span>
                @can('laporan.harian')<a href="{{ route('panel.laporan.harian') }}" aria-current="{{ $nav('panel/laporan/harian*') }}">@include('partials.ikon', ['n' => 'laporan'])Harian saya</a>@endcan
                @can('laporan.cabang')<a href="{{ route('panel.laporan.cabang') }}" aria-current="{{ request()->routeIs('panel.laporan.cabang') ? 'page' : 'false' }}">@include('partials.ikon', ['n' => 'laporan'])Laporan cabang</a>@endcan
                @can('laporan.konsolidasi')<a href="{{ route('panel.laporan.konsolidasi') }}" aria-current="{{ $nav('panel/laporan/konsolidasi*') }}">@include('partials.ikon', ['n' => 'koin'])Konsolidasi</a>@endcan
            @endcanany
            @canany(['pengguna.kelola', 'audit.lihat'])
                <span class="grup">Administrasi</span>
                @can('pengguna.kelola')<a href="{{ route('panel.pengguna.index') }}" aria-current="{{ $nav('panel/pengguna*') }}">@include('partials.ikon', ['n' => 'pengguna'])Pengguna</a>@endcan
                @can('audit.lihat')<a href="{{ route('panel.audit.index') }}" aria-current="{{ $nav('panel/audit*') }}">@include('partials.ikon', ['n' => 'audit'])Jejak audit</a>@endcan
            @endcanany
            <span class="grup">Akun saya</span>
            <a href="{{ route('panel.akun.sandi') }}" aria-current="{{ $nav('panel/akun/sandi*') }}">@include('partials.ikon', ['n' => 'kunci'])Ganti kata sandi</a>
            @if ($u->role->isStaff())<a href="{{ route('panel.akun.2fa') }}" aria-current="{{ $nav('panel/akun/2fa*') }}">@include('partials.ikon', ['n' => 'audit'])Keamanan 2FA</a>@endif
            <span class="grup">Lainnya</span>
            <a href="{{ route('beranda') }}">@include('partials.ikon', ['n' => 'luar'])Lihat situs</a>
        </nav>
        <div class="p-akun">
            <div><b>{{ $u->name }}</b><small>{{ $kodeAkun }}</small></div>
            <form method="post" action="{{ route('keluar') }}">@csrf <button type="submit">Keluar</button></form>
        </div>
    </aside>

    <div class="p-utama">
        <header class="p-atas">
            <div class="p-atas-kiri">
                <span class="p-pil"><i aria-hidden="true"></i>Sistem operasional</span>
                <time class="p-tanggal" datetime="{{ today()->toDateString() }}">{{ today()->translatedFormat('d M Y') }}</time>
            </div>
            <div class="p-atas-kanan">
                <span class="p-pil p-pil--peran">{{ $u->role->label() }}</span>
                <div class="p-identitas"><b>{{ $u->name }}</b><small>{{ $lokasi }} · {{ $kodeAkun }}</small></div>
                <form class="p-keluar-atas" method="post" action="{{ route('keluar') }}">@csrf <button type="submit">Keluar</button></form>
            </div>
        </header>
        <main class="p-isi" id="isi">
            @if (session('status'))<p class="kabar" role="status">{{ session('status') }}</p>@endif
            @if (session('sandi_baru'))
                <p class="kabar kabar--rahasia" role="status">Kata sandi sementara untuk {{ session('sandi_baru')['email'] }}: <b>{{ session('sandi_baru')['sandi'] }}</b><br><small>Salin sekarang. Kata sandi ini tidak ditampilkan lagi. Minta pemiliknya menggantinya.</small></p>
            @endif
            @if ($errors->has('umum'))<p class="kabar kabar--galat" role="alert">{{ $errors->first('umum') }}</p>@endif
            @yield('isi')
        </main>
    </div>
</div>
<script src="{{ asset('js/panel.js') }}?v={{ filemtime(public_path('js/panel.js')) }}" defer></script>
</body>
</html>
