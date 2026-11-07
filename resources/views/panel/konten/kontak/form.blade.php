@extends('layouts.panel')
@section('judul', 'Ubah kontak '.$c->nama)
@section('isi')
    <h1>Kontak {{ $c->nama }}</h1>
    <form class="form" method="post" action="{{ route('panel.kontak.update', $c) }}">
        @csrf @method('PUT')
        <x-bidang nama="alamat" label="Alamat lengkap" :nilai="$c->alamat" maxlength="255" />
        <div class="baris-2">
            <x-bidang nama="telepon" label="Telepon" tipe="tel" :nilai="$c->telepon" />
            <x-bidang nama="whatsapp" label="WhatsApp admin" tipe="tel" :nilai="$c->whatsapp" bantuan="Contoh: 08123456789" />
        </div>
        <x-bidang nama="email" label="Email" tipe="email" :nilai="$c->email" />
        <x-bidang nama="jam_buka" label="Jam layanan" :nilai="$c->jam_buka" bantuan="Contoh: Senin–Sabtu 08.00–16.00" />
        <div class="baris-2">
            <x-bidang nama="lat" label="Lintang (latitude)" :nilai="$c->lat !== null ? (float) $c->lat : null" bantuan="Salin dari Google Maps. Contoh: -5.1476" inputmode="decimal" />
            <x-bidang nama="lng" label="Bujur (longitude)" :nilai="$c->lng !== null ? (float) $c->lng : null" bantuan="Contoh: 119.4327" inputmode="decimal" />
        </div>
        <button class="btn btn--utama" type="submit">Simpan</button> <a class="btn" href="{{ route('panel.kontak.index') }}">Batal</a>
    </form>
@endsection
