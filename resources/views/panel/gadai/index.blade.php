@extends('layouts.panel')
@section('judul', 'Gadai berjalan')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Gadai berjalan</h1>
    <p class="sub">Urut jatuh tempo terdekat. @if (auth()->user()->role === \App\Enums\Role::Member) Gadai Anda. @else {{ auth()->user()->branch?->nama ?? 'Semua cabang' }}. @endif</p>

    <div class="bar-aksi"><a class="chip" href="{{ route('panel.gadai.index', ['segera' => 1]) }}">Jatuh tempo ≤ 7 hari atau terlambat</a></div>
    <form class="filter" method="get" action="{{ route('panel.gadai.index') }}">
        <div class="bidang" style="margin:0"><label for="f-status">Status</label>
            <select id="f-status" name="status"><option value="">Semua</option>
                @foreach ($semuaStatus as $s)<option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>@endforeach
            </select></div>
        <div class="bidang" style="margin:0"><label for="f-cari">No. nota</label><input id="f-cari" name="cari" value="{{ request('cari') }}" placeholder="SBG-..."></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
    </form>

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>No. nota</th>@if (auth()->user()->role->isStaff())<th>Anggota</th>@endif<th>Barang</th><th class="angka">Saldo pokok</th><th>Jatuh tempo</th><th>Status</th></tr></thead>
        <tbody>
        @forelse ($daftar as $g)
            @php $sisa = (int) today()->diffInDays($g->jatuh_tempo, false); $bisaBayar = in_array($g->status, \App\Enums\LoanStatus::dapatDibayar(), true); @endphp
            <tr>
                <td><a class="mono" href="{{ route('panel.gadai.show', $g) }}">{{ $g->no_sbg }}</a></td>
                @if (auth()->user()->role->isStaff())<td>{{ $g->member->nama_lengkap }}</td>@endif
                <td>{{ $g->product->nama }}</td>
                <td class="angka">{{ Rupiah::format((int) $g->pokok_saldo) }}</td>
                <td>{{ $g->jatuh_tempo->translatedFormat('j M Y') }}
                    @if ($bisaBayar)<br><small style="color:{{ $sisa < 0 ? 'var(--terakota)' : 'inherit' }}">{{ $sisa < 0 ? 'terlambat '.abs($sisa).' hari' : ($sisa === 0 ? 'hari ini' : $sisa.' hari lagi') }}</small>@endif</td>
                <td><span class="lencana lencana--{{ $g->status->nada() }}">{{ $g->status->label() }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="kosong">Belum ada gadai.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $daftar->links() }}</div>
@endsection
