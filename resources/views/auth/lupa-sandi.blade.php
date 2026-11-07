@extends('layouts.publik')
@section('judul', 'Lupa kata sandi — linygadai')
@section('deskripsi', 'Atur ulang kata sandi akun linygadai lewat email.')
@section('robots', 'noindex,nofollow')
@section('badan')
    <header class="kepala"><div class="wrap">
        <a class="merek" href="{{ route('beranda') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#1B2A2F" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>linygadai</a>
    </div></header>
    <main id="isi" class="bagian"><div class="wrap" style="max-width:34rem">
        <p class="nomor">Akun</p>
        <h1 style="font-size:2.2rem;margin:1rem 0">Lupa kata sandi</h1>
        <p>Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.</p>
        @if (session('status'))<p class="sudah-masuk" role="status" style="margin:1rem 0">{{ session('status') }}</p>@endif
        <form class="panel-form" method="post" action="{{ route('sandi.kirim') }}" novalidate>
            @csrf
            <label for="s-email">Email</label>
            <input id="s-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')<p class="galat" role="alert">{{ $message }}</p>@enderror
            <button class="btn btn--utama" type="submit">Kirim tautan</button>
        </form>
        <p style="margin-top:1.2rem"><a href="{{ route('masuk') }}">Kembali ke halaman masuk</a></p>
    </div></main>
@endsection
