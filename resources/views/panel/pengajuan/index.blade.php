@extends('layouts.panel')
@section('judul', 'Pengajuan')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Pengajuan</h1>
    <p class="sub">{{ auth()->user()->role === \App\Enums\Role::Member ? 'Pengajuan titip gadai Anda.' : 'Pengajuan di '.(auth()->user()->branch?->nama ?? 'semua cabang').'.' }}</p>

    <div class="bar-aksi">
        @if ($antrean)
            <a class="chip" href="{{ route('panel.pengajuan.index', ['status' => $antrean['status']->value]) }}">Antrean saya: {{ $antrean['status']->label() }} <b>{{ $antrean['jumlah'] }}</b></a>
        @endif
        @can('pengajuan.buat')<a class="btn btn--kecil" href="{{ route('panel.pengajuan.create') }}">Pengajuan baru</a>@endcan
    </div>

    <form class="filter" method="get" action="{{ route('panel.pengajuan.index') }}">
        <div class="bidang" style="margin:0"><label for="f-status">Status</label>
            <select id="f-status" name="status"><option value="">Semua</option>
                @foreach ($semuaStatus as $s)<option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>@endforeach
            </select></div>
        <div class="bidang" style="margin:0"><label for="f-cari">Kode</label><input id="f-cari" name="cari" value="{{ request('cari') }}" placeholder="PG-..."></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
    </form>

    <div class="tabel-bungkus">
        <table class="tabel">
            <thead><tr><th>Kode</th>@if (auth()->user()->role->isStaff())<th>Anggota</th>@endif<th>Barang</th><th class="angka">Pinjaman</th><th>Status</th><th>Diajukan</th></tr></thead>
            <tbody>
            @forelse ($daftar as $a)
                <tr>
                    <td><a class="mono" href="{{ route('panel.pengajuan.show', $a) }}">{{ $a->kode }}</a></td>
                    @if (auth()->user()->role->isStaff())<td>{{ $a->member->nama_lengkap }}</td>@endif
                    <td>{{ $a->product->nama }}</td>
                    <td class="angka">{{ Rupiah::format($a->pinjaman_disetujui ?? $a->pinjaman_diminta) }}</td>
                    <td><span class="lencana lencana--{{ $a->status->nada() }}">{{ $a->status->label() }}</span></td>
                    <td>{{ $a->diajukan_at?->translatedFormat('j M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="kosong">Belum ada pengajuan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $daftar->links() }}</div>
@endsection
