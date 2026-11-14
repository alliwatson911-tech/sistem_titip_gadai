@extends('layouts.publik')

@section('judul', 'Masuk Akun — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Masuk ke akun penitip atau staf PERSIDEN HP Titip Gadai untuk memantau status titipan barang, tanggal jatuh tempo, dan rincian transaksi.')
@section('robots', 'noindex,nofollow')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem); max-width: 32rem;">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Masuk' => '']" />

            <div class="halaman-header" style="text-align: center; border-bottom: none; margin-bottom: 1.5rem;">
                <p class="kicker">Portal Penitip &amp; Staf</p>
                <h1>Masuk ke Akun Anda</h1>
                <p class="pengantar" style="margin: 0 auto; font-size: .95rem;">Masukkan email dan kata sandi akun PERSIDEN HP yang telah didaftarkan oleh petugas loket cabang.</p>
            </div>

            @if (session('status'))
                <p class="sudah-masuk" role="status" style="margin-bottom: 1.5rem; text-align: center;">{{ session('status') }}</p>
            @endif

            <form class="panel-form" method="post" action="{{ route('masuk') }}" novalidate style="padding: 2.2rem 2rem; box-shadow: var(--shadow-md);">
                @csrf
                <input type="hidden" name="form" value="masuk">

                <div class="bidang">
                    <label for="email" style="margin-top: 0;">Alamat Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@email.com" required autofocus class="@error('email') is-invalid input-error-shake @enderror">
                    @error('email')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="bidang">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <label for="password" style="margin-top: .8rem;">Kata Sandi</label>
                        <a href="{{ route('sandi.lupa') }}" style="font-size: .84rem; color: var(--kuningan-tua); text-decoration: none;">Lupa kata sandi?</a>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required class="@error('password') is-invalid input-error-shake @enderror">
                    @error('password')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <label class="centang" style="margin-top: 1.1rem;">
                    <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))>
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button class="btn btn--utama" type="submit" style="width: 100%; margin-top: 1.5rem; min-height: 48px; font-size: 1rem;">
                    Masuk ke Akun
                </button>

                <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px dashed var(--garis); text-align: center; font-size: .86rem; color: var(--muted); line-height: 1.5;">
                    <p style="margin: 0;">
                        Akun penitip dibuat langsung oleh petugas loket di cabang PERSIDEN HP saat menyerahkan barang titipan. Kata sandi sementara dikirimkan via email atau WhatsApp.
                    </p>
                </div>
            </form>
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection
