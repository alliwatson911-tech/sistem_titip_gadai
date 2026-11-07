@extends('layouts.publik')
@section('judul', 'Atur ulang kata sandi — linygadai')
@section('deskripsi', 'Buat kata sandi baru untuk akun linygadai.')
@section('robots', 'noindex,nofollow')
@section('badan')
    <header class="kepala"><div class="wrap">
        <a class="merek" href="{{ route('beranda') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#1B2A2F" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>linygadai</a>
    </div></header>
    <main id="isi" class="bagian"><div class="wrap" style="max-width:34rem">
        <p class="nomor">Akun</p>
        <h1 style="font-size:2.2rem;margin:1rem 0">Buat kata sandi baru</h1>
        <form class="panel-form" method="post" action="{{ route('sandi.simpan') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label for="a-email">Email</label>
            <input id="a-email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="username" required>
            @error('email')<p class="galat" role="alert">{{ $message }}</p>@enderror
            <label for="a-sandi">Kata sandi baru (min. 10 karakter, huruf dan angka)</label>
            <input id="a-sandi" name="password" type="password" autocomplete="new-password" required autofocus>
            @error('password')<p class="galat" role="alert">{{ $message }}</p>@enderror
            <label for="a-sandi2">Ulangi kata sandi baru</label>
            <input id="a-sandi2" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="btn btn--utama" type="submit">Simpan kata sandi</button>
        </form>
        <p style="margin-top:1.2rem"><a href="{{ route('sandi.lupa') }}">Minta tautan baru</a></p>
    </div></main>
@endsection
