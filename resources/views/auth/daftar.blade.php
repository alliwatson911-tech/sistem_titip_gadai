@extends('layouts.base')
@section('judul', 'Daftar — linygadai')
@section('isi')
    <h1>Daftar calon anggota</h1>
    <p>Isi data sesuai KTP. Admin cabang akan memverifikasi data Anda sebelum Anda dapat mengajukan titip gadai.</p>
    <form method="post" action="{{ route('daftar') }}" novalidate>
        @csrf
        @foreach ([
            ['nama_lengkap','Nama lengkap sesuai KTP','text','name'],
            ['nik','NIK (16 digit)','text','off'],
            ['telepon','Nomor telepon / WhatsApp','tel','tel'],
            ['email','Email','email','email'],
        ] as [$nama, $label, $tipe, $ac])
            <label for="{{ $nama }}">{{ $label }}</label>
            <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" value="{{ old($nama) }}" autocomplete="{{ $ac }}"
                   @if ($nama === 'nik') inputmode="numeric" maxlength="16" @endif required>
            @error($nama)<p class="galat" role="alert">{{ $message }}</p>@enderror
        @endforeach

        <label for="branch_id">Cabang terdekat</label>
        <select id="branch_id" name="branch_id" required>
            <option value="">Pilih cabang</option>
            @foreach ($cabang as $c)
                <option value="{{ $c->id }}" @selected(old('branch_id') == $c->id)>{{ $c->nama }}</option>
            @endforeach
        </select>
        @error('branch_id')<p class="galat" role="alert">{{ $message }}</p>@enderror

        <label for="password">Kata sandi (min. 10 karakter, huruf dan angka)</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required>
        @error('password')<p class="galat" role="alert">{{ $message }}</p>@enderror

        <label for="password_confirmation">Ulangi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

        <label style="font-weight:400"><input type="checkbox" name="setuju" value="1" @checked(old('setuju'))> Saya menyatakan data di atas benar.</label>
        @error('setuju')<p class="galat" role="alert">{{ $message }}</p>@enderror

        <button type="submit">Daftar</button>
    </form>
@endsection
