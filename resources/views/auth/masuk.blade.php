@extends('layouts.base')
@section('judul', 'Masuk — linygadai')
@section('isi')
    <h1>Masuk akun</h1>
    <form method="post" action="{{ route('masuk') }}" novalidate>
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        @error('email')<p class="galat" role="alert">{{ $message }}</p>@enderror

        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        @error('password')<p class="galat" role="alert">{{ $message }}</p>@enderror

        <label style="font-weight:400"><input type="checkbox" name="ingat" value="1"> Ingat saya di perangkat ini</label>
        <button type="submit">Masuk</button>
    </form>
    <p>Belum punya akun? <a href="{{ route('daftar') }}">Daftar sebagai calon anggota</a>.</p>
@endsection
