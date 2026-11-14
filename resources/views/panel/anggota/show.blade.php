@php
    /** @var \App\Models\Member $m */
@endphp
@extends('layouts.panel')
@section('judul', $m->nama_lengkap)
@section('isi')
    <p class="sub"><a href="{{ route('panel.anggota') }}">&larr; Semua penitip</a></p>
    <h1>{{ $m->nama_lengkap }}</h1>

    <dl class="ringkasan">
        <div><dt>Status</dt><dd><span class="lencana lencana--{{ ['terverifikasi' => 'sukses', 'calon' => 'proses', 'ditolak' => 'gagal', 'blokir' => 'gagal'][$m->status] ?? 'netral' }}">{{ $m->status }}</span></dd></div>
        <div><dt>Cabang</dt><dd>{{ $m->branch->nama }}</dd></div>
        <div><dt>NIK</dt><dd class="mono">{{ $nikPenuh ?? $m->nikTersamar() }}</dd></div>
        <div><dt>Telepon</dt><dd>{{ $m->telepon ?: '—' }}</dd></div>
        <div><dt>Tanggal lahir</dt><dd>{{ $m->tgl_lahir?->translatedFormat('j F Y') ?? '—' }}</dd></div>
        <div><dt>Alamat</dt><dd>{{ $m->alamat ?: '—' }}</dd></div>
        <div><dt>Diverifikasi</dt><dd>{{ $m->verified_at ? $m->verified_at->translatedFormat('j M Y H:i') : '—' }}</dd></div>
    </dl>

    @can('anggota.kelola')
        @unless ($nikPenuh)
            <form method="post" action="{{ route('panel.anggota.nik', $m) }}" style="margin-bottom:1rem">@csrf
                <button class="btn btn--kecil" type="submit">Tampilkan NIK lengkap</button>
                <span class="catatan-audit">Permintaan ini dicatat di audit.</span></form>
        @endunless

        <h2>Foto KTP</h2>
        @if ($m->ktp_path)
            <p><a class="btn btn--kecil" href="{{ route('panel.berkas.ktp', $m) }}" target="_blank" rel="noopener">Buka foto KTP</a> <span class="catatan-audit">Pembukaan foto dicatat di audit.</span></p>
        @else
            <p>Anggota belum mengunggah foto KTP.</p>
        @endif

        <h2>Keputusan keanggotaan</h2>
        <form method="post" action="{{ route('panel.anggota.tindakan', $m) }}" class="bar-aksi">@csrf
            <button class="btn btn--utama" name="aksi" value="verifikasi" type="submit">Verifikasi</button>
            <button class="btn" name="aksi" value="tolak" type="submit">Tolak</button>
            <button class="btn btn--bahaya" name="aksi" value="blokir" type="submit" data-konfirmasi="Blokir anggota ini?">Blokir</button>
        </form>
        <p class="bantuan">Cocokkan nama, NIK, dan foto KTP sebelum memverifikasi.</p>
    @endcan
@endsection
