@extends('layouts.panel')
@section('judul', 'Laporan harian')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Laporan harian saya</h1>
    <p class="sub">{{ $tgl->translatedFormat('l, j F Y') }}. Hanya transaksi yang Anda catat. Gunakan untuk mencocokkan uang tunai di laci.</p>

    <form class="filter" method="get" action="{{ route('panel.laporan.harian') }}">
        <div class="bidang" style="margin:0"><label for="f-tanggal">Tanggal</label><input id="f-tanggal" type="date" name="tanggal" value="{{ $tgl->toDateString() }}" max="{{ today()->toDateString() }}"></div>
        <button class="btn btn--kecil" type="submit">Tampilkan</button>
    </form>
    @error('tanggal')<p class="galat">{{ $message }}</p>@enderror

    <dl class="ringkasan">
        <div><dt>Tunai masuk (pembayaran)</dt><dd>{{ Rupiah::format($h['masuk_tunai']) }}</dd></div>
        <div><dt>Tunai keluar (pencairan)</dt><dd>{{ Rupiah::format($h['keluar_tunai']) }}</dd></div>
        <div><dt>Kas tunai bersih</dt><dd style="color:{{ $h['kas_tunai_bersih'] < 0 ? 'var(--terakota)' : 'inherit' }}">{{ $h['kas_tunai_bersih'] < 0 ? '−' : '' }}{{ Rupiah::format(abs($h['kas_tunai_bersih'])) }}</dd></div>
        <div><dt>Transfer sah</dt><dd>{{ Rupiah::format($h['transfer_sah']) }}</dd></div>
        <div><dt>Transfer menunggu verifikasi</dt><dd>{{ Rupiah::format($h['transfer_menunggu']) }}</dd></div>
    </dl>

    <h2>Pembayaran</h2>
    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Kwitansi</th><th>Nota gadai</th><th>Jenis</th><th>Metode</th><th>Status</th><th class="angka">Total</th></tr></thead>
        <tbody>
        @forelse ($h['pembayaran'] as $p)
            <tr><td class="mono">{{ $p->no_kwitansi }}</td><td class="mono">{{ $p->loan?->no_sbg }}</td><td>{{ ucfirst($p->tipe) }}</td><td>{{ $p->metode }}</td><td>{{ str_replace('_', ' ', $p->status) }}</td><td class="angka">{{ Rupiah::format((int) $p->total) }}</td></tr>
        @empty
            <tr><td colspan="6" class="kosong">Tidak ada pembayaran.</td></tr>
        @endforelse
        </tbody>
    </table></div>

    <h2>Pencairan</h2>
    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Waktu</th><th>Metode</th><th>Referensi</th><th class="angka">Jumlah</th></tr></thead>
        <tbody>
        @forelse ($h['pencairan'] as $c)
            <tr><td>{{ $c->dicairkan_at->translatedFormat('H:i') }}</td><td>{{ $c->metode }}</td><td>{{ $c->referensi ?: '—' }}</td><td class="angka">{{ Rupiah::format((int) $c->jumlah) }}</td></tr>
        @empty
            <tr><td colspan="4" class="kosong">Tidak ada pencairan.</td></tr>
        @endforelse
        </tbody>
    </table></div>
@endsection
