@extends('layouts.panel')
@section('judul', 'Jejak audit')
@section('isi')
    <h1>Jejak audit</h1>
    <p class="sub">Catatan hanya-tambah: tidak dapat diubah atau dihapus, baik lewat aplikasi maupun database. {{ auth()->user()->role === \App\Enums\Role::Owner ? 'Semua cabang.' : 'Hanya catatan cabang Anda.' }}</p>

    <form class="filter" method="get" action="{{ route('panel.audit.index') }}">
        <div class="bidang" style="margin:0"><label for="f-aksi">Aksi (awalan)</label><input id="f-aksi" name="aksi" value="{{ request('aksi') }}" placeholder="mis. payment, auth, loan"></div>
        <div class="bidang" style="margin:0"><label for="f-cari">Pelaku</label><input id="f-cari" name="cari" value="{{ request('cari') }}" placeholder="nama atau email"></div>
        <div class="bidang" style="margin:0"><label for="f-dari">Dari</label><input id="f-dari" type="date" name="dari" value="{{ request('dari') }}"></div>
        <div class="bidang" style="margin:0"><label for="f-sampai">Sampai</label><input id="f-sampai" type="date" name="sampai" value="{{ request('sampai') }}"></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
    </form>

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Waktu</th><th>Pelaku</th><th>Aksi</th><th>Subjek</th><th>IP</th></tr></thead>
        <tbody>
        @forelse ($daftar as $a)
            <tr>
                <td><a href="{{ route('panel.audit.show', $a) }}">{{ $a->created_at->translatedFormat('j M Y H:i:s') }}</a></td>
                <td>{{ $a->user?->name ?? 'Sistem/tamu' }}</td>
                <td class="mono">{{ $a->aksi }}</td>
                <td class="mono">{{ $a->subject_type ? class_basename($a->subject_type).' #'.$a->subject_id : '—' }}</td>
                <td class="mono">{{ $a->ip }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="kosong">Tidak ada catatan.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $daftar->links() }}</div>
@endsection
