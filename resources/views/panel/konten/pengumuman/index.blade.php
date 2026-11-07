@extends('layouts.panel')
@section('judul', 'Pengumuman')
@section('isi')
    <h1>Pengumuman</h1>
    <p class="sub">Tampil di papan pengumuman halaman utama.</p>
    @can('konten.kelola')<div class="bar-aksi"><a class="btn btn--utama btn--kecil" href="{{ route('panel.pengumuman.create') }}">Pengumuman baru</a></div>@endcan

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Judul</th><th>Cabang</th><th>Tayang</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($daftar as $p)
            <tr>
                <td>{{ $p->judul }}</td>
                <td>{{ $p->branch?->nama ?? 'Semua' }}</td>
                <td>{{ $p->mulai?->translatedFormat('j M Y') ?? 'Langsung' }} – {{ $p->selesai?->translatedFormat('j M Y') ?? 'tanpa batas' }}</td>
                <td><span class="lencana lencana--{{ $p->is_published ? 'sukses' : 'netral' }}">{{ $p->is_published ? 'terbit' : 'draf' }}</span></td>
                <td>
                    @can('konten.kelola')
                        <a href="{{ route('panel.pengumuman.edit', $p) }}">Ubah</a>
                        <form method="post" action="{{ route('panel.pengumuman.destroy', $p) }}" style="display:inline" data-konfirmasi="Hapus pengumuman ini?">@csrf @method('DELETE')
                            <button class="btn btn--kecil btn--bahaya" type="submit">Hapus</button></form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="kosong">Belum ada pengumuman.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $daftar->links() }}</div>
@endsection
