@php
    $dariMasuk = old('form') === 'masuk';
    $dariDaftar = old('form') === 'daftar';
    $cabangDaftar = $cabang->except('PUSAT');
@endphp
<section class="bagian bagian--kertas2" id="akun" data-spy="akun" aria-labelledby="judul-akun">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Masuk akun</p>
            <h2 id="judul-akun">Anggota lama masuk di sini, calon anggota mendaftar</h2>
        </div>

        @auth
            <div class="sudah-masuk">
                <p style="margin-bottom:1rem">Anda masuk sebagai <b>{{ auth()->user()->name }}</b>.</p>
                <a class="btn btn--utama" href="{{ route('panel') }}">Buka panel</a>
            </div>
        @else
            @if (session('status'))<p class="sudah-masuk" role="status" style="margin-bottom:1.5rem">{{ session('status') }}</p>@endif
            <div class="akun">
                <form class="panel-form" method="post" action="{{ route('masuk') }}" novalidate>
                    @csrf
                    <input type="hidden" name="form" value="masuk">
                    <h3>Masuk</h3>
                    @include('auth.partials.demo-accounts', ['emailId' => 'm-email', 'passwordId' => 'm-sandi'])
                    <label for="m-email">Email</label>
                    <input id="m-email" name="email" type="email" value="{{ $dariMasuk ? old('email') : '' }}" autocomplete="username" required class="@if ($dariMasuk) @error('email') is-invalid input-error-shake @enderror @endif">
                    @if ($dariMasuk) @error('email')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror @endif
                    <label for="m-sandi">Kata sandi</label>
                    <input id="m-sandi" name="password" type="password" autocomplete="current-password" required class="@if ($dariMasuk) @error('password') is-invalid input-error-shake @enderror @endif">
                    @if ($dariMasuk) @error('password')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror @endif
                    <label class="centang"><input type="checkbox" name="ingat" value="1"> Ingat saya di perangkat ini</label>
                    <button class="btn btn--gelap" type="submit">Masuk</button>
                    <p style="margin:1rem 0 0"><a href="{{ route('sandi.lupa') }}">Lupa kata sandi?</a></p>
                </form>

                <form class="panel-form" method="post" action="{{ route('daftar') }}" novalidate>
                    @csrf
                    <input type="hidden" name="form" value="daftar">
                    <h3>Daftar calon anggota</h3>
                    <p style="margin:0 0 .5rem">Isi sesuai KTP. Admin cabang memverifikasi data Anda sebelum Anda dapat mengajukan.</p>
                    <div class="grid-2">
                        <div>
                            <label for="d-nama">Nama lengkap</label>
                            <input id="d-nama" name="nama_lengkap" value="{{ $dariDaftar ? old('nama_lengkap') : '' }}" autocomplete="name" required>
                            @if ($dariDaftar) @error('nama_lengkap')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                        </div>
                        <div>
                            <label for="d-nik">NIK (16 digit)</label>
                            <input id="d-nik" name="nik" value="{{ $dariDaftar ? old('nik') : '' }}" inputmode="numeric" maxlength="16" autocomplete="off" required>
                            @if ($dariDaftar) @error('nik')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                        </div>
                        <div>
                            <label for="d-telepon">Telepon / WhatsApp</label>
                            <input id="d-telepon" name="telepon" type="tel" value="{{ $dariDaftar ? old('telepon') : '' }}" autocomplete="tel" required>
                            @if ($dariDaftar) @error('telepon')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                        </div>
                        <div>
                            <label for="d-email">Email</label>
                            <input id="d-email" name="email" type="email" value="{{ $dariDaftar ? old('email') : '' }}" autocomplete="email" required>
                            @if ($dariDaftar) @error('email')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                        </div>
                    </div>
                    <label for="d-cabang">Cabang terdekat</label>
                    <select id="d-cabang" name="branch_id" required>
                        <option value="">Pilih cabang</option>
                        @foreach ($cabangDaftar as $c)
                            <option value="{{ $c->id }}" @selected($dariDaftar && old('branch_id') == $c->id)>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                    @if ($dariDaftar) @error('branch_id')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                    <div class="grid-2">
                        <div>
                            <label for="d-sandi">Kata sandi</label>
                            <input id="d-sandi" name="password" type="password" autocomplete="new-password" required>
                            @if ($dariDaftar) @error('password')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                        </div>
                        <div>
                            <label for="d-sandi2">Ulangi kata sandi</label>
                            <input id="d-sandi2" name="password_confirmation" type="password" autocomplete="new-password" required>
                        </div>
                    </div>
                    <p class="catatan-kecil" style="margin:.4rem 0 0">Minimal 10 karakter, memuat huruf dan angka.</p>
                    <label class="centang"><input type="checkbox" name="setuju" value="1" @checked($dariDaftar && old('setuju'))> Saya menyatakan data di atas benar.</label>
                    @if ($dariDaftar) @error('setuju')<p class="galat" role="alert">{{ $message }}</p>@enderror @endif
                    <button class="btn btn--utama" type="submit">Daftar</button>
                </form>
            </div>
        @endauth
    </div>
</section>
