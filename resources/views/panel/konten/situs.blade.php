@extends('layouts.panel')
@section('judul', 'Teks & izin situs')
@section('isi')
    <h1>Teks &amp; izin situs</h1>
    <p class="sub">Konten global di halaman utama. Hanya Owner.</p>
    <form class="form" method="post" action="{{ route('panel.situs.update') }}" style="max-width:46rem">
        @csrf @method('PUT')
        <x-bidang nama="tagline" label="Tagline" :nilai="$k['tagline'] ?? ''" :wajib="true" maxlength="120" bantuan="Kata \"tenang\" otomatis diberi garis penekanan bila ada." />
        <div class="bidang"><label for="f-sambutan">Sambutan</label><textarea id="f-sambutan" name="sambutan" maxlength="600" required>{{ old('sambutan', $k['sambutan'] ?? '') }}</textarea>@error('sambutan')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <div class="bidang"><label for="f-ringkasan">Ringkasan organisasi</label><textarea id="f-ringkasan" name="ringkasan" maxlength="800" required>{{ old('ringkasan', $k['ringkasan'] ?? '') }}</textarea>@error('ringkasan')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <div class="bidang"><label for="f-izin">Informasi perizinan usaha</label>
            <textarea id="f-izin" name="izin_usaha" maxlength="500">{{ old('izin_usaha', $k['izin_usaha'] ?? '') }}</textarea>
            <p class="bantuan">Isi hanya dengan keterangan perizinan yang sudah Anda verifikasi (nomor dan lembaga penerbit). Bila kosong, situs menampilkan catatan netral dan tidak mengklaim apa pun.</p>
            @error('izin_usaha')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <h2>Media sosial</h2>
        @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'youtube' => 'YouTube'] as $kunci => $label)
            <div class="bidang"><label for="f-sosmed-{{ $kunci }}">{{ $label }}</label>
                <input id="f-sosmed-{{ $kunci }}" name="sosmed[{{ $kunci }}]" type="url" value="{{ old("sosmed.$kunci", $sosmed[$kunci] ?? '') }}" placeholder="https://">
                @error("sosmed.$kunci")<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        @endforeach
        <button class="btn btn--utama" type="submit">Simpan</button>
    </form>
@endsection
