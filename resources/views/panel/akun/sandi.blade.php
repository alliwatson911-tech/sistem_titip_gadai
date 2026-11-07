@extends('layouts.panel')
@section('judul', 'Ganti kata sandi')
@section('isi')
    <h1>Ganti kata sandi</h1>
    <p class="sub">Setelah diganti, perangkat lain yang sedang masuk dengan akun ini otomatis dikeluarkan.</p>
    <form class="form" method="post" action="{{ route('panel.akun.sandi.ubah') }}">
        @csrf @method('PUT')
        <x-bidang nama="sandi_lama" label="Kata sandi saat ini" tipe="password" autocomplete="current-password" :wajib="true" />
        <x-bidang nama="password" label="Kata sandi baru" tipe="password" autocomplete="new-password" :wajib="true" bantuan="Minimal 10 karakter, memuat huruf dan angka, dan berbeda dari yang lama." />
        <x-bidang nama="password_confirmation" label="Ulangi kata sandi baru" tipe="password" autocomplete="new-password" :wajib="true" />
        <button class="btn btn--utama" type="submit">Simpan kata sandi</button>
    </form>
@endsection
