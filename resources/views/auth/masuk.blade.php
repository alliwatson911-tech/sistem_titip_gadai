@extends('layouts.publik')

@section('judul', 'Masuk Akun — linygadai')
@section('deskripsi', 'Masuk ke akun anggota linygadai untuk memantau status titip gadai, tanggal jatuh tempo, dan riwayat transaksi resmi.')
@section('robots', 'noindex,nofollow')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem); max-width: 32rem;">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Masuk' => '']" />

            <div class="halaman-header" style="text-align: center; border-bottom: none; margin-bottom: 1.5rem;">
                <p class="kicker">Area Anggota &amp; Staf</p>
                <h1>Masuk ke Akun Anda</h1>
                <p class="pengantar" style="margin: 0 auto;">Masukkan email dan kata sandi akun linygadai Anda yang telah terdaftar.</p>
            </div>

            @if (session('status'))
                <p class="sudah-masuk" role="status" style="margin-bottom: 1.5rem; text-align: center;">{{ session('status') }}</p>
            @endif

            <form class="panel-form" method="post" action="{{ route('masuk') }}" novalidate style="padding: 2.2rem 2rem; box-shadow: var(--shadow-md);">
                @csrf
                <input type="hidden" name="form" value="masuk">

                <div class="bidang">
                    <label for="email" style="margin-top: 0;">Alamat Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@email.com" required autofocus>
                    @error('email')
                        <p class="galat" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bidang">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <label for="password" style="margin-top: .8rem;">Kata Sandi</label>
                        <a href="{{ route('sandi.lupa') }}" style="font-size: .84rem; color: var(--kuningan-tua); text-decoration: none;">Lupa kata sandi?</a>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required>
                    @error('password')
                        <p class="galat" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <label class="centang" style="margin-top: 1.1rem;">
                    <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))>
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button class="btn btn--utama" type="submit" style="width: 100%; margin-top: 1.5rem; min-height: 48px; font-size: 1rem;">
                    Masuk ke Akun
                </button>

                <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px dashed var(--garis); text-align: center; font-size: .92rem;">
                    <p style="margin: 0; color: var(--muted);">
                        Belum memiliki akun?
                        <a href="{{ route('daftar') }}" style="color: var(--kuningan-tua); font-weight: 700; text-decoration: none; margin-left: .25rem;">
                            Daftar sebagai calon anggota &rarr;
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection
