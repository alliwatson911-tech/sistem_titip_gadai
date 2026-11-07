@extends('layouts.panel')
@section('judul', $u->exists ? 'Ubah staf' : 'Tambah staf')
@section('isi')
    <h1>{{ $u->exists ? 'Ubah staf' : 'Tambah staf' }}</h1>
    <form class="form" method="post" action="{{ $u->exists ? route('panel.pengguna.update', $u) : route('panel.pengguna.store') }}">
        @csrf @if ($u->exists) @method('PUT') @endif
        <x-bidang nama="name" label="Nama" :nilai="$u->name" :wajib="true" maxlength="120" />
        <x-bidang nama="email" label="Email" tipe="email" :nilai="$u->email" :wajib="true" />
        <div class="bidang"><label for="f-role">Peran</label>
            <select id="f-role" name="role" required>
                @foreach (['owner', 'manager', 'admin', 'teller', 'analis'] as $r)
                    <option value="{{ $r }}" @selected(old('role', $u->role?->value) === $r)>{{ \App\Enums\Role::from($r)->label() }}</option>
                @endforeach
            </select>@error('role')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        <div class="bidang"><label for="f-branch_id">Cabang</label>
            <select id="f-branch_id" name="branch_id"><option value="">— (hanya untuk Owner)</option>
                @foreach ($cabang as $c)<option value="{{ $c->id }}" @selected(old('branch_id', $u->branch_id) == $c->id)>{{ $c->nama }}</option>@endforeach
            </select>
            <p class="bantuan">Manager, Admin, Teller, dan Analis wajib memilih satu cabang.</p>
            @error('branch_id')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
        @if ($u->exists)
            <div class="bidang"><label for="f-status">Status</label>
                <select id="f-status" name="status"><option value="aktif" @selected(old('status', $u->status) === 'aktif')>Aktif</option><option value="nonaktif" @selected(old('status', $u->status) === 'nonaktif')>Nonaktif</option></select>
                <p class="bantuan">Akun nonaktif langsung dikeluarkan dari sesi yang sedang berjalan.</p></div>
        @else
            <p class="bantuan">Kata sandi sementara dibuat otomatis dan ditampilkan sekali setelah akun dibuat.</p>
        @endif
        <button class="btn btn--utama" type="submit">Simpan</button> <a class="btn" href="{{ route('panel.pengguna.index') }}">Batal</a>
    </form>
@endsection
