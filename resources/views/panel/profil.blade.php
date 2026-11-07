@extends('layouts.panel')
@section('judul', 'Profil & KTP')
@section('isi')
    <h1>Profil &amp; KTP</h1>
    <p class="sub">Nama dan NIK hanya dapat diubah admin cabang. Hubungi cabang bila ada yang perlu dikoreksi.</p>

    <dl class="ringkasan">
        <div><dt>Nama</dt><dd>{{ $m->nama_lengkap }}</dd></div>
        <div><dt>NIK</dt><dd class="mono">{{ $m->nikTersamar() }}</dd></div>
        <div><dt>Cabang</dt><dd>{{ $m->branch->nama }}</dd></div>
        <div><dt>Status keanggotaan</dt><dd><span class="lencana lencana--{{ ['terverifikasi' => 'sukses', 'calon' => 'proses', 'ditolak' => 'gagal', 'blokir' => 'gagal'][$m->status] ?? 'netral' }}">{{ $m->status }}</span></dd></div>
    </dl>

    <form class="form" method="post" action="{{ route('panel.profil.update') }}">
        @csrf @method('PUT')
        <h2>Kontak</h2>
        <x-bidang nama="telepon" label="Telepon / WhatsApp" tipe="tel" :nilai="$m->telepon" :wajib="true" />
        <x-bidang nama="tgl_lahir" label="Tanggal lahir" tipe="date" :nilai="$m->tgl_lahir?->toDateString()" />
        <x-bidang nama="alamat" label="Alamat" :nilai="$m->alamat" />
        <button class="btn btn--utama" type="submit">Simpan</button>
    </form>

    <form class="form" method="post" action="{{ route('panel.profil.ktp') }}" enctype="multipart/form-data">
        @csrf
        <h2>Foto KTP</h2>
        @if ($m->ktp_path)
            <p>KTP sudah diunggah. <a href="{{ route('panel.berkas.ktp', $m) }}" target="_blank" rel="noopener">Lihat</a></p>
        @else
            <p>Belum ada foto KTP. Admin cabang memerlukannya untuk memverifikasi Anda.</p>
        @endif
        <div class="bidang">
            <label for="f-ktp">{{ $m->ktp_path ? 'Ganti foto KTP' : 'Unggah foto KTP' }} (JPG/PNG/WebP, maks. 4 MB)</label>
            <input id="f-ktp" type="file" name="ktp" accept="image/jpeg,image/png,image/webp" required>
            @error('ktp')<p class="galat" role="alert">{{ $message }}</p>@enderror
            <p class="bantuan">Foto hanya dapat dilihat oleh Anda dan petugas cabang Anda. Pastikan semua tulisan terbaca.</p>
        </div>
        <button class="btn btn--utama" type="submit">Unggah</button>
    </form>
@endsection
