@extends('layouts.base')
@section('judul', 'Anggota — linygadai')
@section('isi')
    <h1>Anggota</h1>
    <table>
        <thead><tr><th>Nama</th><th>NIK</th><th>Cabang</th><th>Status</th></tr></thead>
        <tbody>
        @forelse ($anggota as $a)
            <tr>
                <td>{{ $a->nama_lengkap }}</td>
                <td class="mono">{{ $a->nikTersamar() }}</td>
                <td>{{ $a->branch->nama }}</td>
                <td><span class="lencana">{{ $a->status }}</span></td>
            </tr>
        @empty
            <tr><td colspan="4">Belum ada anggota.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $anggota->links() }}
@endsection
