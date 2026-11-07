@extends('layouts.base')
@section('judul', 'Pengguna — linygadai')
@section('isi')
    <h1>Pengguna</h1>
    <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Cabang</th><th>Status</th></tr></thead>
        <tbody>
        @foreach ($pengguna as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->role->label() }}</td>
                <td>{{ $p->branch?->nama ?? '—' }}</td>
                <td><span class="lencana">{{ $p->status }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $pengguna->links() }}
@endsection
