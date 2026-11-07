@extends('layouts.panel')
@section('judul', 'Kontak cabang')
@section('isi')
    <h1>Kontak cabang</h1>
    <p class="sub">Tampil di bagian Tentang Kami. Butir yang kosong otomatis disembunyikan; peta muncul setelah lintang dan bujur diisi.</p>
    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Cabang</th><th>Alamat</th><th>WhatsApp</th><th>Peta</th><th></th></tr></thead>
        <tbody>
        @foreach ($cabang as $c)
            <tr>
                <td>{{ $c->nama }}</td>
                <td>{{ $c->alamat ?: '—' }}</td>
                <td>{{ $c->whatsapp ?: '—' }}</td>
                <td>{{ $c->lat !== null ? 'Ada' : 'Belum' }}</td>
                <td><a href="{{ route('panel.kontak.edit', $c) }}">Ubah</a></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
@endsection
