@extends('layouts.panel')
@section('judul', 'Pengajuan baru')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Pengajuan titip gadai</h1>
    <p class="sub">Pengajuan ini membuat antrean Anda lebih cepat. Barang tetap harus dibawa ke cabang untuk diperiksa dan ditaksir.</p>

    @if ($member && $member->status !== 'terverifikasi')
        <p class="kabar kabar--galat">Keanggotaan Anda belum terverifikasi. Unggah foto KTP di <a href="{{ route('panel.profil') }}">Profil &amp; KTP</a>, lalu tunggu admin cabang memverifikasi.</p>
    @endif

    <form class="form" method="post" action="{{ route('panel.pengajuan.store') }}">
        @csrf
        @if ($anggota !== null)
            <div class="bidang">
                <label for="f-member_id">Anggota (terverifikasi)</label>
                <select id="f-member_id" name="member_id" required>
                    <option value="">Pilih anggota</option>
                    @foreach ($anggota as $m)<option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->nama_lengkap }}</option>@endforeach
                </select>
                @error('member_id')<p class="galat" role="alert">{{ $message }}</p>@enderror
            </div>
        @endif
        <div class="bidang">
            <label for="f-product_id">Jenis barang</label>
            <select id="f-product_id" name="product_id" required>
                <option value="">Pilih jenis barang</option>
                @foreach ($produk as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id', $pilihan) == $p->id || $pilihan === $p->slug)>{{ $p->nama }} ({{ Rupiah::ringkas($p->pinjaman_min) }} – {{ Rupiah::ringkas($p->pinjaman_max) }})</option>
                @endforeach
            </select>
            @error('product_id')<p class="galat" role="alert">{{ $message }}</p>@enderror
        </div>
        <x-bidang nama="pinjaman_diminta" label="Jumlah pinjaman yang diinginkan (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" bantuan="Angka pasti ditetapkan analis setelah barang ditaksir. Anda boleh meminta lebih rendah dari plafon." />
        <div class="bidang">
            <label for="f-catatan_member">Catatan (opsional)</label>
            <textarea id="f-catatan_member" name="catatan_member" maxlength="500">{{ old('catatan_member') }}</textarea>
            @error('catatan_member')<p class="galat" role="alert">{{ $message }}</p>@enderror
        </div>
        <button class="btn btn--utama" type="submit">Kirim pengajuan</button>
    </form>
@endsection
