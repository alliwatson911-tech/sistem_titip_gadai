@extends('layouts.panel')
@section('judul', 'Anggota')
@section('isi')
    <h1>Anggota</h1>
    <p class="sub">Anggota di {{ auth()->user()->branch?->nama ?? 'semua cabang' }}.</p>

    <form class="filter" method="get" action="{{ route('panel.anggota') }}">
        <div class="bidang" style="margin:0"><label for="f-status">Status</label>
            <select id="f-status" name="status"><option value="">Semua</option>
                @foreach (['calon', 'terverifikasi', 'ditolak', 'blokir'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select></div>
        <div class="bidang" style="margin:0"><label for="f-cari">Nama</label><input id="f-cari" name="cari" value="{{ request('cari') }}"></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
    </form>

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Nama</th><th>NIK</th><th>Cabang</th><th>Status</th><th>KTP</th></tr></thead>
        <tbody>
        @forelse ($anggota as $a)
            <tr>
                <td><a href="{{ route('panel.anggota.show', $a) }}">{{ $a->nama_lengkap }}</a></td>
                <td class="mono">{{ $a->nikTersamar() }}</td>
                <td>{{ $a->branch->nama }}</td>
                <td><span class="lencana lencana--{{ ['terverifikasi' => 'sukses', 'calon' => 'proses', 'ditolak' => 'gagal', 'blokir' => 'gagal'][$a->status] ?? 'netral' }}">{{ $a->status }}</span></td>
                <td>{{ $a->ktp_path ? 'Ada' : 'Belum' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="kosong">Belum ada anggota.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $anggota->links() }}</div>
@endsection
