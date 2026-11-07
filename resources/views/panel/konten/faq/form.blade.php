@extends('layouts.panel')
@section('judul', $f->exists ? 'Ubah item' : 'Item baru')
@section('isi')
    <h1>{{ $f->exists ? 'Ubah item' : 'Item baru' }}</h1>
    <form class="form" method="post" action="{{ $f->exists ? route('panel.faq.update', $f) : route('panel.faq.store') }}">
        @csrf @if ($f->exists) @method('PUT') @endif
        <div class="bidang"><label for="f-kelompok">Kelompok</label>
            <select id="f-kelompok" name="kelompok"><option value="faq" @selected(old('kelompok', $f->kelompok) === 'faq')>Tanya jawab</option><option value="informasi" @selected(old('kelompok', $f->kelompok) === 'informasi')>Informasi</option></select></div>
        <x-bidang nama="pertanyaan" label="Pertanyaan / judul" :nilai="$f->pertanyaan" :wajib="true" maxlength="200" />
        <div class="bidang"><label for="f-jawaban">Jawaban</label><textarea id="f-jawaban" name="jawaban" maxlength="1500" required>{{ old('jawaban', $f->jawaban) }}</textarea>@error('jawaban')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <x-bidang nama="urutan" label="Urutan tampil" tipe="number" :nilai="$f->urutan ?? 0" min="0" max="999" />
        @if ($cabang)
            <div class="bidang"><label for="f-branch_id">Cabang</label>
                <select id="f-branch_id" name="branch_id"><option value="">Semua cabang</option>
                    @foreach ($cabang as $c)<option value="{{ $c->id }}" @selected(old('branch_id', $f->branch_id) == $c->id)>{{ $c->nama }}</option>@endforeach
                </select></div>
        @endif
        <label class="centang"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $f->is_published))> Terbitkan di situs</label>
        <button class="btn btn--utama" type="submit">Simpan</button> <a class="btn" href="{{ route('panel.faq.index') }}">Batal</a>
    </form>
@endsection
