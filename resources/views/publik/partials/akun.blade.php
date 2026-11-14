@php
    $dariMasuk = old('form') === 'masuk' || !old('form');
@endphp
<section class="bagian bagian--kertas2" id="akun" data-spy="akun" aria-labelledby="judul-akun">
    <div class="wrap" style="max-width: 40rem;">
        <div class="kepala-bagian" style="text-align: center; display: block; margin-bottom: 2rem;">
            <p class="nomor" style="justify-content: center;">Portal Masuk</p>
            <h2 id="judul-akun" style="margin: .6rem auto 0; font-size: clamp(1.8rem, 3.5vw, 2.4rem);">Masuk ke Akun Penitip &amp; Petugas</h2>
            <p class="pengantar" style="margin: .75rem auto 0; font-size: .95rem; color: var(--tinta-2);">Gunakan email dan kata sandi yang telah didaftarkan untuk memantau transaksi titip gadai.</p>
        </div>

        @auth
            <div class="sudah-masuk" style="text-align: center;">
                <p style="margin-bottom:1rem">Anda sedang masuk sebagai <b>{{ auth()->user()->name }}</b>.</p>
                <a class="btn btn--utama" href="{{ route('panel') }}">Buka Panel Transaksi</a>
            </div>
        @else
            @if (session('status'))<p class="sudah-masuk" role="status" style="margin-bottom:1.5rem; text-align: center;">{{ session('status') }}</p>@endif
            <div class="akun" style="display: block;">
                <form class="panel-form" method="post" action="{{ route('masuk') }}" novalidate style="box-shadow: var(--shadow-md); padding: 2rem 1.8rem;">
                    @csrf
                    <input type="hidden" name="form" value="masuk">
                    <h3 style="margin-bottom: 1.25rem;">Masuk Akun</h3>
                    
                    <label for="m-email" style="margin-top: 0;">Alamat Email</label>
                    <input id="m-email" name="email" type="email" value="{{ $dariMasuk ? old('email') : '' }}" autocomplete="username" placeholder="nama@email.com" required class="@if ($dariMasuk) @error('email') is-invalid input-error-shake @enderror @endif">
                    @if ($dariMasuk) @error('email')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror @endif

                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: .9rem;">
                        <label for="m-sandi" style="margin: 0;">Kata Sandi</label>
                        <a href="{{ route('sandi.lupa') }}" style="font-size: .84rem; color: var(--kuningan-tua); text-decoration: none;">Lupa kata sandi?</a>
                    </div>
                    <input id="m-sandi" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required class="@if ($dariMasuk) @error('password') is-invalid input-error-shake @enderror @endif">
                    @if ($dariMasuk) @error('password')
                        <p class="galat" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror @endif

                    <label class="centang" style="margin-top: 1rem;"><input type="checkbox" name="ingat" value="1"> Ingat saya di perangkat ini</label>
                    <button class="btn btn--utama" type="submit" style="width: 100%; margin-top: 1.4rem;">Masuk ke Akun</button>

                    <div style="margin-top: 1.5rem; padding-top: 1.15rem; border-top: 1px dashed var(--garis); text-align: center; font-size: .86rem; color: var(--muted); line-height: 1.5;">
                        <p style="margin: 0;">
                            Pendaftaran penitip baru diinput langsung oleh petugas di loket cabang resmi saat menyerahkan barang titipan. Password sementara akan dikirimkan ke email atau WhatsApp Anda.
                        </p>
                    </div>
                </form>
            </div>
        @endauth
    </div>
</section>
