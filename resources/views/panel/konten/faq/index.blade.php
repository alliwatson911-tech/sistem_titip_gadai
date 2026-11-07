@extends('layouts.panel')
@section('judul', 'FAQ & informasi')
@section('isi')
    <h1>FAQ &amp; informasi</h1>
    <p class="sub">Tampil sebagai accordion di halaman utama. Token {tenor}, {tenggang}, {lelang_hari}, {maks_perpanjangan} diganti otomatis dari kebijakan.</p>
    @can('konten.kelola')<div class="bar-aksi"><a class="btn btn--utama btn--kecil" href="{{ route('panel.faq.create') }}">Item baru</a></div>@endcan

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Kelompok</th><th>Pertanyaan</th><th>Cabang</th><th>Urutan</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($daftar as $f)
            <tr>
                <td>{{ $f->kelompok === 'faq' ? 'Tanya jawab' : 'Informasi' }}</td>
                <td>{{ $f->pertanyaan }}</td>
                <td>{{ $f->branch?->nama ?? 'Semua' }}</td>
                <td class="angka">{{ $f->urutan }}</td>
                <td><span class="lencana lencana--{{ $f->is_published ? 'sukses' : 'netral' }}">{{ $f->is_published ? 'terbit' : 'draf' }}</span></td>
                <td>
                    @can('konten.kelola')
                        <a href="{{ route('panel.faq.edit', $f) }}">Ubah</a>
                        <form method="post" action="{{ route('panel.faq.destroy', $f) }}" style="display:inline" data-konfirmasi="Hapus item ini?">@csrf @method('DELETE')
                            <button class="btn btn--kecil btn--bahaya" type="submit">Hapus</button></form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="kosong">Belum ada item.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $daftar->links() }}</div>
@endsection
