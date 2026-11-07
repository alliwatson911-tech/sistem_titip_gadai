@extends('layouts.panel')
@section('judul', 'Keamanan 2FA')
@section('isi')
    <h1>Autentikasi dua faktor</h1>
    <p class="sub">Menambah kode 6 digit dari aplikasi autentikator (Google Authenticator, Authy, dll.) saat masuk. {{ $wajib ? 'Peran Anda wajib mengaktifkannya.' : 'Sangat disarankan untuk akun staf.' }}</p>

    @if (session('kode_pemulihan'))
        <div class="kabar kabar--rahasia" role="status">
            <b>Simpan kode pemulihan ini sekarang</b> (tampil sekali). Tiap kode hanya berlaku sekali, dipakai bila ponsel hilang.
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(9rem,1fr));gap:.4rem;margin-top:.8rem">
                @foreach (session('kode_pemulihan') as $k)<code style="font-size:1.05rem;user-select:all">{{ $k }}</code>@endforeach
            </div>
        </div>
    @endif

    @if ($u->duaFaktorAktif())
        <p><span class="lencana lencana--sukses">Aktif</span> Sisa kode pemulihan: <b>{{ $sisaKode }}</b></p>
        <form class="form" method="post" action="{{ route('panel.akun.2fa.kode') }}">@csrf
            <h2>Buat kode pemulihan baru</h2>
            <x-bidang nama="sandi" label="Kata sandi Anda" tipe="password" autocomplete="current-password" :wajib="true" />
            <button class="btn" type="submit">Buat kode baru</button>
        </form>
        @unless ($wajib)
            <form class="form" method="post" action="{{ route('panel.akun.2fa.nonaktif') }}">@csrf @method('DELETE')
                <h2>Nonaktifkan 2FA</h2>
                <x-bidang nama="sandi" label="Kata sandi Anda" tipe="password" autocomplete="current-password" :wajib="true" />
                <x-bidang nama="kode" label="Kode 6 digit saat ini" inputmode="numeric" autocomplete="one-time-code" maxlength="10" :wajib="true" />
                <button class="btn btn--bahaya" type="submit">Nonaktifkan</button>
            </form>
        @endunless
    @elseif ($menunggu)
        <div class="form">
            <h2>Langkah 1: daftarkan di aplikasi autentikator</h2>
            <p>Pilih "masukkan kunci setup" di aplikasi, lalu isi:</p>
            <p>Akun: <b>{{ $u->email }}</b></p>
            <p>Kunci: <code style="font-size:1.1rem;user-select:all;word-break:break-all">{{ $rahasia }}</code></p>
            <p class="bantuan">Jenis: berbasis waktu (TOTP), 6 digit, 30 detik. Tautan setup (untuk aplikasi yang mendukung): <span class="mono" style="word-break:break-all">{{ $uri }}</span></p>
        </div>
        <form class="form" method="post" action="{{ route('panel.akun.2fa.konfirmasi') }}">@csrf
            <h2>Langkah 2: konfirmasi</h2>
            <x-bidang nama="kode" label="Kode 6 digit dari aplikasi" inputmode="numeric" autocomplete="one-time-code" maxlength="10" :wajib="true" />
            <button class="btn btn--utama" type="submit">Aktifkan 2FA</button>
        </form>
    @else
        <p><span class="lencana lencana--netral">Belum aktif</span></p>
        <form method="post" action="{{ route('panel.akun.2fa.mulai') }}">@csrf <button class="btn btn--utama" type="submit">Mulai mengaktifkan</button></form>
    @endif
@endsection
