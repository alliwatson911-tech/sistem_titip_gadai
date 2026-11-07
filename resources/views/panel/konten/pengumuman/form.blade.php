@extends('layouts.panel')
@section('judul', $p->exists ? 'Ubah pengumuman' : 'Pengumuman baru')
@section('isi')
    <h1>{{ $p->exists ? 'Ubah pengumuman' : 'Pengumuman baru' }}</h1>
    <form class="form" method="post" action="{{ $p->exists ? route('panel.pengumuman.update', $p) : route('panel.pengumuman.store') }}">
        @csrf @if ($p->exists) @method('PUT') @endif
        <x-bidang nama="judul" label="Judul" :nilai="$p->judul" :wajib="true" maxlength="150" />
        <div class="bidang"><label for="f-isi">Isi</label><textarea id="f-isi" name="isi" maxlength="1000" required>{{ old('isi', $p->isi) }}</textarea>@error('isi')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <div class="baris-2">
            <x-bidang nama="mulai" label="Mulai tayang" tipe="date" :nilai="$p->mulai?->toDateString()" bantuan="Kosong = langsung." />
            <x-bidang nama="selesai" label="Selesai tayang" tipe="date" :nilai="$p->selesai?->toDateString()" bantuan="Kosong = tanpa batas." />
        </div>
        @if ($cabang)
            <div class="bidang"><label for="f-branch_id">Cabang</label>
                <select id="f-branch_id" name="branch_id"><option value="">Semua cabang</option>
                    @foreach ($cabang as $c)<option value="{{ $c->id }}" @selected(old('branch_id', $p->branch_id) == $c->id)>{{ $c->nama }}</option>@endforeach
                </select></div>
        @endif
        <label class="centang"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $p->is_published))> Terbitkan di situs</label>
        <button class="btn btn--utama" type="submit">Simpan</button> <a class="btn" href="{{ route('panel.pengumuman.index') }}">Batal</a>
    </form>
@endsection
