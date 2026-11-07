@extends('layouts.publik')
@section('judul', 'Verifikasi dua langkah — linygadai')
@section('deskripsi', 'Masukkan kode verifikasi untuk melanjutkan masuk.')
@section('robots', 'noindex,nofollow')
@section('badan')
    <header class="kepala"><div class="wrap"><a class="merek" href="{{ route('beranda') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#1B2A2F" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>linygadai</a></div></header>
    <main id="isi" class="bagian"><div class="wrap" style="max-width:34rem">
        <p class="nomor">Keamanan</p>
        <h1 style="font-size:2.2rem;margin:1rem 0">Verifikasi dua langkah</h1>
        <p>Buka aplikasi autentikator di ponsel Anda dan masukkan kode 6 digit. Bila ponsel hilang, masukkan salah satu kode pemulihan (format xxxx-xxxx); tiap kode hanya berlaku sekali.</p>
        <form class="panel-form" method="post" action="{{ route('dua-faktor.periksa') }}" novalidate>
            @csrf
            <label for="k-kode">Kode</label>
            <input id="k-kode" name="kode" inputmode="numeric" autocomplete="one-time-code" maxlength="32" required autofocus>
            @error('kode')<p class="galat" role="alert">{{ $message }}</p>@enderror
            <button class="btn btn--utama" type="submit">Verifikasi</button>
        </form>
        <p style="margin-top:1.2rem"><a href="{{ route('masuk') }}">Batal, kembali ke masuk</a></p>
    </div></main>
@endsection
