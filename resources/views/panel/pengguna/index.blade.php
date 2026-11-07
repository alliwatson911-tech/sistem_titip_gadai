@extends('layouts.panel')
@section('judul', 'Pengguna')
@section('isi')
    <h1>Pengguna</h1>
    <p class="sub">Akun staf. Akun anggota dibuat lewat pendaftaran di situs.</p>
    <div class="bar-aksi"><a class="btn btn--utama btn--kecil" href="{{ route('panel.pengguna.create') }}">Tambah staf</a></div>

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Cabang</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach ($pengguna as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->role->label() }}</td>
                <td>{{ $p->branch?->nama ?? '—' }}</td>
                <td><span class="lencana lencana--{{ $p->status === 'aktif' ? 'sukses' : 'netral' }}">{{ $p->status }}</span></td>
                <td>
                    <a href="{{ route('panel.pengguna.edit', $p) }}">Ubah</a>
                    <form method="post" action="{{ route('panel.pengguna.reset', $p) }}" style="display:inline" data-konfirmasi="Reset kata sandi {{ $p->email }}? Semua sesinya akan diakhiri.">@csrf
                        <button class="btn btn--kecil" type="submit">Reset sandi</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $pengguna->links() }}</div>
@endsection
