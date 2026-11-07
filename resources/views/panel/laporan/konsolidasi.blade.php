@extends('layouts.panel')
@section('judul', 'Laporan konsolidasi')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Laporan konsolidasi</h1>
    <p class="sub">Seluruh cabang · {{ $dari->translatedFormat('j M Y') }} – {{ $sampai->translatedFormat('j M Y') }}. Dasar kas; portofolio keadaan saat ini.</p>

    <form class="filter" method="get" action="{{ route('panel.laporan.konsolidasi') }}">
        <div class="bidang" style="margin:0"><label for="f-dari">Dari</label><input id="f-dari" type="date" name="dari" value="{{ $dari->toDateString() }}"></div>
        <div class="bidang" style="margin:0"><label for="f-sampai">Sampai</label><input id="f-sampai" type="date" name="sampai" value="{{ $sampai->toDateString() }}"></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
        <a class="btn btn--kecil" href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}">Unduh CSV</a>
    </form>

    @php $pertama = reset($kolom); @endphp
    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Metrik</th>@foreach (array_keys($kolom) as $k)<th class="angka">{{ $k }}</th>@endforeach</tr></thead>
        <tbody>
        @foreach ($pertama as $i => [$label, , $jenis])
            <tr><td>{{ $label }}</td>@foreach ($kolom as $k => $baris)<td class="angka" @if ($k === 'Total') style="font-weight:600" @endif>{{ $jenis === 'rp' ? Rupiah::format($baris[$i][1]) : number_format($baris[$i][1], 0, ',', '.') }}</td>@endforeach</tr>
        @endforeach
        </tbody>
    </table></div>
@endsection
