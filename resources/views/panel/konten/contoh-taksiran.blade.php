@extends('layouts.panel')
@section('judul', 'Contoh taksiran')
@section('isi')
    <h1>Contoh taksiran simulasi</h1>
    <p class="sub">Chip contoh barang di simulasi publik. Hanya Owner. Ini <b>kisaran indikatif</b>, bukan harga pasar resmi: isi dari pengalaman transaksi atau pengecekan manual yang terdokumentasi, dan perbarui rutin. Jenis barang tanpa contoh tidak menampilkan chip.</p>
    <p class="bantuan" style="max-width:46rem">Satu baris satu contoh: <code>Nama contoh | terendah | tertinggi</code> (angka boleh memakai titik). Maksimal 12 contoh per jenis barang.</p>

    <form class="form" method="post" action="{{ route('panel.emas.update') }}" style="max-width:46rem; margin-top:1.5rem">
        @csrf @method('PUT')
        <h2>Harga emas per gram (24K)</h2>
        <p class="bantuan">Dipakai simulasi emas &amp; perhiasan: berat &times; harga &times; karat/24. Isi dari sumber yang jelas dan perbarui rutin. Bila lebih dari 7 hari tidak diperbarui (atau belum diisi), hitung otomatis di situs dimatikan dan pengunjung mengisi taksiran sendiri.</p>
        <x-bidang nama="harga_emas_gram" label="Harga emas 24K per gram (Rp)" :nilai="$emas['harga'] ? number_format($emas['harga'], 0, ',', '.') : ''" :wajib="true" inputmode="numeric" autocomplete="off" placeholder="1.250.000" />
        <x-bidang nama="sumber" label="Sumber harga" :nilai="$emas['sumber']" :wajib="true" maxlength="120" placeholder="Antam, 20 Nov 2026" bantuan="Tampil di situs bersama tanggal pembaruan." />
        @if ($emas['tgl'])
            <p class="bantuan">Terakhir diperbarui {{ \Illuminate\Support\Carbon::parse($emas['tgl'])->format('d-m-Y') }}@if ($emas['basi']) &mdash; <b>sudah lebih dari 7 hari, hitung otomatis di situs mati</b>@endif.</p>
        @endif
        <button class="btn btn--utama" type="submit">Simpan harga emas</button>
    </form>

    <h2 style="margin-top:2rem">Contoh barang per jenis</h2>
    @foreach ($produk as $p)
        @php
            $bag = $errors->getBag('p'.$p->id);
            $isi = $bag->any() ? old('contoh') : collect($p->contohSimulasi())->map(fn ($c) => $c['label'].' | '.number_format($c['min'], 0, ',', '.').' | '.number_format($c['max'], 0, ',', '.'))->implode("\n");
        @endphp
        <form class="form" method="post" action="{{ route('panel.contoh.update', $p) }}" style="max-width:46rem; margin-top:1.5rem">
            @csrf @method('PUT')
            <div class="bidang">
                <label for="f-contoh-{{ $p->id }}">{{ $p->nama }}</label>
                <textarea id="f-contoh-{{ $p->id }}" name="contoh" rows="{{ min(14, max(5, substr_count((string) $isi, "
") + 2)) }}" maxlength="2000" placeholder="iPhone 13 128GB | 5.500.000 | 7.000.000">{{ $isi }}</textarea>
                @if ($p->contoh_taksiran)<p class="bantuan">Terakhir diubah {{ $p->updated_at->format('d-m-Y H:i') }}.</p>@endif
                @if ($bag->has('contoh'))<p class="galat" role="alert">{{ $bag->first('contoh') }}</p>@endif
            </div>
            <button class="btn btn--utama" type="submit">Simpan {{ $p->nama }}</button>
        </form>
    @endforeach
@endsection
