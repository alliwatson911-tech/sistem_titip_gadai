@extends('layouts.panel')
@section('judul', 'Ringkasan')
@section('isi')
    <h1>Halo, {{ $user->name }}</h1>
    <p class="sub">
        <span class="lencana lencana--netral">{{ $user->role->label() }}</span>
        @if ($user->branch) <span class="lencana lencana--netral">{{ $user->branch->nama }}</span>
        @elseif ($user->role === \App\Enums\Role::Owner) <span class="lencana lencana--netral">Semua cabang</span>
        @endif
    </p>

    @if (! empty($tugas))
        <h2 style="margin-top:1rem">Menunggu Anda</h2>
        <div class="tugas">
            @foreach ($tugas as [$label, $jumlah, $url])
                <a href="{{ $url }}"><b>{{ $jumlah }}</b>{{ $label }}</a>
            @endforeach
        </div>
    @endif

    @can('pengajuan.buat')
        <div class="bar-aksi"><a class="btn btn--utama" href="{{ route('panel.pengajuan.create') }}">Buat pengajuan titip gadai</a></div>
    @endcan

    @if ($user->role === \App\Enums\Role::Member && $user->email_verified_at === null)
        <p class="kabar kabar--galat">Email Anda belum diverifikasi, sehingga belum dapat mengajukan titip gadai. Buka email verifikasi yang kami kirim ke {{ $user->email }}.
            <form method="post" action="{{ route('panel.akun.verifikasi.kirim') }}" style="display:inline">@csrf <button class="btn btn--kecil" type="submit">Kirim ulang email verifikasi</button></form></p>
    @endif
    @if ($user->role === \App\Enums\Role::Member && $user->member && $user->member->status !== 'terverifikasi')
        <p class="kabar kabar--galat">Keanggotaan Anda belum terverifikasi (status: {{ $user->member->status }}).
            @if (! $user->member->ktp_path) Unggah foto KTP di <a href="{{ route('panel.profil') }}">Profil &amp; KTP</a> agar admin cabang dapat memverifikasi Anda.
            @else Admin cabang sedang memeriksa data Anda.
            @endif
        </p>
    @endif
@endsection
