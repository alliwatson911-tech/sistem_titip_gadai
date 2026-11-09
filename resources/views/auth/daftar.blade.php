@extends('layouts.publik')

@section('judul', 'Pendaftaran Calon Anggota — linygadai')
@section('deskripsi', 'Daftar sebagai calon anggota linygadai untuk mengajukan titip gadai online dan memantau status barang elektronik Anda.')
@section('robots', 'noindex,nofollow')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem); max-width: 44rem;">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Daftar' => '']" />

            <div class="halaman-header" style="text-align: center; border-bottom: none; margin-bottom: 1.5rem;">
                <p class="kicker">Calon Anggota Baru</p>
                <h1>Daftar Akun Anggota linygadai</h1>
                <p class="pengantar" style="margin: 0 auto; max-width: 55ch;">
                    Isi data identitas diri Anda sesuai KTP. Admin cabang akan memverifikasi data sebelum transaksi titip gadai dapat diproses.
                </p>
            </div>

            <form class="panel-form" method="post" action="{{ route('daftar') }}" novalidate style="padding: 2.2rem 2rem; box-shadow: var(--shadow-md);">
                @csrf
                <input type="hidden" name="form" value="daftar">

                <div class="grid-2">
                    <div>
                        <label for="nama_lengkap" style="margin-top: 0;">Nama Lengkap (sesuai KTP)</label>
                        <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" autocomplete="name" placeholder="Nama sesuai kartu identitas" required autofocus>
                        @error('nama_lengkap')
                            <p class="galat" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nik" style="margin-top: 0;">NIK (16 Digit Angka)</label>
                        <input id="nik" name="nik" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" autocomplete="off" placeholder="7371xxxxxxxxxxxx" required>
                        @error('nik')
                            <p class="galat" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid-2" style="margin-top: .5rem;">
                    <div>
                        <label for="telepon">Nomor Telepon / WhatsApp</label>
                        <input id="telepon" name="telepon" type="tel" value="{{ old('telepon') }}" autocomplete="tel" placeholder="08xxxxxxxxxx" required>
                        @error('telepon')
                            <p class="galat" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email">Alamat Email Aktif</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required>
                        @error('email')
                            <p class="galat" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="margin-top: .5rem;">
                    <label for="branch_id">Pilih Cabang Terdekat</label>
                    <select id="branch_id" name="branch_id" required>
                        <option value="">-- Pilih Cabang Operasional --</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->id }}" @selected(old('branch_id') == $c->id)>
                                {{ $c->nama }} @if($c->alamat) ({{ $c->alamat }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="galat" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid-2" style="margin-top: .5rem;">
                    <div>
                        <label for="password">Kata Sandi</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Min. 10 karakter" required>
                        @error('password')
                            <p class="galat" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation">Ulangi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi" required>
                    </div>
                </div>
                <p class="catatan-kecil" style="margin: .4rem 0 1rem;">Kombinasi minimal 10 karakter, wajib memuat huruf dan angka.</p>

                <label class="centang">
                    <input type="checkbox" name="setuju" value="1" @checked(old('setuju'))>
                    <span>Saya menyatakan bahwa seluruh data identitas di atas adalah benar dan sah sesuai dokumen resmi KTP saya.</span>
                </label>
                @error('setuju')
                    <p class="galat" role="alert">{{ $message }}</p>
                @enderror

                <button class="btn btn--utama" type="submit" style="width: 100%; margin-top: 1.5rem; min-height: 48px; font-size: 1rem;">
                    Daftar Sebagai Calon Anggota
                </button>

                <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px dashed var(--garis); text-align: center; font-size: .92rem;">
                    <p style="margin: 0; color: var(--muted);">
                        Sudah memiliki akun terdaftar?
                        <a href="{{ route('masuk') }}" style="color: var(--kuningan-tua); font-weight: 700; text-decoration: none; margin-left: .25rem;">
                            Masuk ke akun Anda di sini &rarr;
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection
