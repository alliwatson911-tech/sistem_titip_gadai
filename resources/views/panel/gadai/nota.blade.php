<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota gadai {{ $g->no_sbg }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="{{ asset('css/situs.css') }}?v={{ filemtime(public_path('css/situs.css')) }}">
    <style>
        body { background: #fff; padding: 1.5rem; }
        .nota-cetak { max-width: 34rem; margin: 0 auto; border: 2px solid var(--tinta); padding: 1.6rem; font-family: var(--f-mono); font-size: .9rem; }
        .nota-cetak h1 { font-size: 1.6rem; margin-bottom: .2rem; }
        .baris { display: flex; justify-content: space-between; gap: 1rem; padding: .35rem 0; border-bottom: 1px dashed var(--garis); }
        .baris b { text-align: right; font-weight: 500; }
        .tt { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 3rem; text-align: center; }
        .tt div { border-top: 1px solid var(--tinta); padding-top: .4rem; }
        .tombol-cetak { text-align: center; margin-bottom: 1rem; }
        @media print { .tombol-cetak { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
@use('App\Support\Rupiah')
<div class="tombol-cetak"><button class="btn btn--utama" type="button" data-cetak>Cetak</button></div>
<div class="nota-cetak">
    <h1>linygadai</h1>
    <div style="margin-bottom:1rem">NOTA GADAI · {{ $g->member->branch->nama }}</div>
    <div class="baris"><span>No. nota</span><b>{{ $g->no_sbg }}</b></div>
    <div class="baris"><span>Tanggal akad</span><b>{{ $g->tgl_akad->translatedFormat('j F Y') }}</b></div>
    <div class="baris"><span>Penggadai</span><b>{{ $g->member->nama_lengkap }}</b></div>
    <div class="baris"><span>NIK</span><b>{{ $g->member->nikTersamar() }}</b></div>
    @foreach ($barang as $b)
        <div class="baris"><span>Barang</span><b>{{ $b->merek }} {{ $b->model }}</b></div>
        <div class="baris"><span>No. segel</span><b>{{ $b->no_segel }}</b></div>
    @endforeach
    <div class="baris"><span>Pinjaman (pokok)</span><b>{{ Rupiah::format((int) $g->pokok_awal) }}</b></div>
    <div class="baris"><span>Tenor</span><b>{{ $g->snapshot_syarat['tenor_hari'] }} hari</b></div>
    <div class="baris"><span>Jatuh tempo</span><b>{{ $g->tgl_akad->copy()->addDays($g->snapshot_syarat['tenor_hari'])->translatedFormat('j F Y') }}</b></div>
    <div class="baris"><span>Sewa modal</span><b>{{ rtrim(rtrim(number_format($g->snapshot_syarat['sewa_modal_per_hari_bp'] / 100, 2, ',', ''), '0'), ',') }}% per hari</b></div>
    <div class="baris"><span>Administrasi</span><b>{{ rtrim(rtrim(number_format($g->snapshot_syarat['admin_bp'] / 100, 2, ',', ''), '0'), ',') }}% (min {{ Rupiah::format($g->snapshot_syarat['admin_min']) }}, maks {{ Rupiah::format($g->snapshot_syarat['admin_max']) }})</b></div>
    <p style="margin-top:1rem;font-size:.8rem">Sewa modal dan administrasi dibayar saat perpanjang, cicil, atau tebus. Barang yang tidak diperpanjang atau ditebus setelah masa tenggang ({{ $g->snapshot_syarat['masa_tenggang_hari'] }} hari) dapat dilelang setelah pemberitahuan; kelebihan hasil lelang menjadi hak penggadai. Bawa nota asli ini dan KTP asli saat menebus.</p>
    <div class="tt"><div>Penggadai</div><div>Petugas</div></div>
</div>
<script src="{{ asset('js/panel.js') }}" defer></script>
</body>
</html>
