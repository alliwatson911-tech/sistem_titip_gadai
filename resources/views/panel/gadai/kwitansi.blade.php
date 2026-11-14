@php
    /** @var \App\Models\GadaiPayment $p */
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi {{ $p->no_kwitansi }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="{{ asset('css/situs.css') }}?v={{ filemtime(public_path('css/situs.css')) }}">
    <style>
        body { background: #fff; padding: 1.5rem; }
        .nota-cetak { max-width: 30rem; margin: 0 auto; border: 2px solid var(--tinta); padding: 1.6rem; font-family: var(--f-mono); font-size: .9rem; }
        .nota-cetak h1 { font-size: 1.5rem; margin-bottom: .2rem; }
        .baris { display: flex; justify-content: space-between; gap: 1rem; padding: .35rem 0; border-bottom: 1px dashed var(--garis); }
        .baris b { text-align: right; font-weight: 500; }
        .total { display: flex; justify-content: space-between; margin-top: 1rem; padding-top: .8rem; border-top: 2px solid var(--tinta); font-size: 1.1rem; }
        .batal { color: var(--terakota); border: 2px solid var(--terakota); display: inline-block; padding: .2rem .6rem; margin-bottom: 1rem; }
        .tombol-cetak { text-align: center; margin-bottom: 1rem; }
        @media print { .tombol-cetak { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
@use('App\Support\Rupiah')
<div class="tombol-cetak"><button class="btn btn--utama" type="button" data-cetak>Cetak</button></div>
<div class="nota-cetak">
    <h1>PERSIDEN HP</h1>
    <div style="margin-bottom:1rem; font-weight: 600; color: var(--kuningan-tua);">KWITANSI TRANSAKSI TITIP GADAI</div>
    @if ($p->status === 'batal')<div class="batal">DIBATALKAN / DIBALIKKAN</div>@endif
    <div class="baris"><span>No. kwitansi</span><b>{{ $p->no_kwitansi }}</b></div>
    <div class="baris"><span>No. nota titip</span><b>{{ $p->loan->no_sbg }}</b></div>
    <div class="baris"><span>Penitip</span><b>{{ $p->loan->member->nama_lengkap }}</b></div>
    <div class="baris"><span>Jenis</span><b>{{ ucfirst($p->tipe) }}</b></div>
    <div class="baris"><span>Tanggal</span><b>{{ $p->dibayar_at->translatedFormat('j F Y H:i') }}</b></div>
    <div class="baris"><span>Metode</span><b>{{ $p->metode }}</b></div>
    @if ($p->pokok_dibayar > 0)<div class="baris"><span>Pokok dibayar</span><b>{{ Rupiah::format((int) $p->pokok_dibayar) }}</b></div>@endif
    <div class="baris"><span>Jasa penitipan</span><b>{{ Rupiah::format((int) $p->sewa_modal) }}</b></div>
    <div class="baris"><span>Administrasi</span><b>{{ Rupiah::format((int) $p->administrasi) }}</b></div>
    @if ($p->denda > 0)<div class="baris"><span>Denda</span><b>{{ Rupiah::format((int) $p->denda) }}</b></div>@endif
    <div class="total"><span>TOTAL</span><b>{{ Rupiah::format((int) $p->total) }}</b></div>
    <div style="margin-top:1rem;font-size:.8rem">Petugas Loket: {{ $p->teller?->name }}</div>
</div>
<script src="{{ asset('js/panel.js') }}" defer></script>
</body>
</html>
