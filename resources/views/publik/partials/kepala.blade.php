<header class="kepala">
    <div class="wrap">
        <a class="merek" href="{{ route('beranda') }}" aria-label="linygadai, ke beranda">
            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#D4AF37" stroke="#0F4C3A" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#0F4C3A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="merek-teks">
                <span class="merek-nama">linygadai</span>
                <span class="merek-sub">Cabang A & B · Makassar</span>
            </div>
        </a>

        <button type="button" class="tombol-menu" id="tombol-menu" aria-expanded="false" aria-controls="menu" aria-label="Buka menu navigasi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>

        <nav class="menu" id="menu" aria-label="Menu utama">
            <a href="{{ route('beranda') }}" @if(request()->routeIs('beranda')) aria-current="page" @endif>Beranda</a>
            <a href="{{ route('publik.layanan') }}" @if(request()->routeIs('publik.layanan')) aria-current="page" @endif>Layanan</a>
            <a href="{{ route('publik.standar') }}" @if(request()->routeIs('publik.standar')) aria-current="page" @endif>Standar</a>
            <a href="{{ route('publik.panduan') }}" @if(request()->routeIs('publik.panduan')) aria-current="page" @endif>Panduan</a>
            <a href="{{ route('publik.syarat') }}" @if(request()->routeIs('publik.syarat')) aria-current="page" @endif>Syarat &amp; Ketentuan</a>
            <a href="{{ route('publik.simulasi') }}" @if(request()->routeIs('publik.simulasi')) aria-current="page" @endif>Simulasi</a>
            <a href="{{ route('publik.faq') }}" @if(request()->routeIs('publik.faq')) aria-current="page" @endif>FAQ</a>
            <a href="{{ route('publik.tentang') }}" @if(request()->routeIs('publik.tentang')) aria-current="page" @endif>Tentang</a>

            <div class="nav-actions-mobile">
                @auth
                    <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
                @else
                    <a class="btn btn--kecil" href="{{ route('masuk') }}">Masuk</a>
                    <a class="btn btn--utama btn--kecil" href="{{ route('daftar') }}">Daftar</a>
                @endauth
            </div>
        </nav>

        <div class="nav-actions-desktop">
            @auth
                <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
            @else
                <a class="btn btn--kecil" href="{{ route('masuk') }}">Masuk</a>
                <a class="btn btn--utama btn--kecil" href="{{ route('daftar') }}">Daftar</a>
            @endauth
        </div>
    </div>
</header>
