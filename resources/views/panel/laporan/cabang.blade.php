@extends('layouts.panel')
@section('judul', 'Laporan cabang')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Laporan cabang</h1>
    <p class="sub">{{ $nama }} · {{ $dari->translatedFormat('j M Y') }} – {{ $sampai->translatedFormat('j M Y') }}. Dasar kas: pembayaran dihitung pada tanggal dicatat sah. Portofolio adalah keadaan saat ini.</p>

    <form class="filter" method="get" action="{{ route('panel.laporan.cabang') }}">
        <div class="bidang" style="margin:0"><label for="f-dari">Dari</label><input id="f-dari" type="date" name="dari" value="{{ $dari->toDateString() }}"></div>
        <div class="bidang" style="margin:0"><label for="f-sampai">Sampai</label><input id="f-sampai" type="date" name="sampai" value="{{ $sampai->toDateString() }}"></div>
        @if ($pilihanCabang)
            <div class="bidang" style="margin:0"><label for="f-cabang">Cabang</label>
                <select id="f-cabang" name="cabang"><option value="">Semua cabang</option>@foreach ($pilihanCabang as $c)<option value="{{ $c->id }}" @selected($cabangId === $c->id)>{{ $c->nama }}</option>@endforeach</select></div>
        @endif
        <button class="btn btn--kecil" type="submit">Terapkan</button>
        <a class="btn btn--kecil" href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}">Unduh CSV</a>
    </form>

    <div class="tabel-bungkus"><table class="tabel" style="min-width:24rem">
        <thead><tr><th>Metrik</th><th class="angka">Nilai</th></tr></thead>
        <tbody>
        @foreach ($baris as [$label, $nilai, $jenis])
            <tr><td>{{ $label }}</td><td class="angka">{{ $jenis === 'rp' ? Rupiah::format($nilai) : number_format($nilai, 0, ',', '.') }}</td></tr>
        @endforeach
        </tbody>
    </table></div>

    <h2>Gadai menurut status (kini)</h2>
    <div class="tabel-bungkus"><table class="tabel" style="min-width:20rem"><tbody>
        @foreach ($data['status_gadai'] as $s => $n)<tr><td>{{ \App\Enums\LoanStatus::from($s)->label() }}</td><td class="angka">{{ $n }}</td></tr>@endforeach
    </tbody></table></div>

    <h2>Pengajuan dibuat pada periode, menurut status</h2>
    <div class="tabel-bungkus"><table class="tabel" style="min-width:20rem"><tbody>
        @forelse ($data['pengajuan'] as $s => $n)<tr><td>{{ \App\Enums\ApplicationStatus::tryFrom($s)?->label() ?? $s }}</td><td class="angka">{{ $n }}</td></tr>@empty<tr><td class="kosong">Tidak ada pengajuan pada periode ini.</td></tr>@endforelse
    </tbody></table></div>
@endsection
