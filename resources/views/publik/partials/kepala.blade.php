<header class="kepala">
    <div class="wrap">
        <a class="merek" href="{{ route('beranda') }}" aria-label="PERSIDEN HP, ke beranda">
            <img src="{{ asset('images/logo-persiden-hp-compact.png') }}" alt="Logo PERSIDEN HP" class="merek-logo-img">
            <div class="merek-teks">
                <span class="merek-nama">PERSIDEN <span class="merah">HP</span></span>
                <span class="merek-sub">Titip Gadai · Makassar</span>
            </div>
        </a>

        <button type="button" class="tombol-menu" id="tombol-menu" aria-expanded="false" aria-controls="menu" aria-label="Buka menu navigasi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>

        <nav class="menu" id="menu" aria-label="Menu utama">
            <a href="{{ route('beranda') }}" @if(request()->routeIs('beranda')) aria-current="page" @endif>Beranda</a>
            <a href="{{ route('publik.tentang') }}" @if(request()->routeIs('publik.tentang*') || request()->routeIs('publik.kontak')) aria-current="page" @endif>Tentang Kami</a>
            <a href="{{ route('publik.layanan') }}" @if(request()->routeIs('publik.layanan*')) aria-current="page" @endif>Layanan</a>
            <a href="{{ route('publik.simulasi') }}" @if(request()->routeIs('publik.simulasi')) aria-current="page" @endif>Simulasi Jasa</a>
            <a href="{{ route('publik.cara-penitipan') }}" @if(request()->routeIs('publik.cara-penitipan')) aria-current="page" @endif>Cara Penitipan</a>
            <a href="{{ route('publik.syarat') }}" @if(request()->routeIs('publik.syarat')) aria-current="page" @endif>Syarat &amp; Ketentuan</a>
            <a href="{{ route('publik.promo') }}" @if(request()->routeIs('publik.promo')) aria-current="page" @endif>Promo</a>

            <div class="nav-actions-mobile">
                @auth
                    <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
                @else
                    <a class="btn btn--utama btn--kecil" href="{{ route('masuk') }}">Masuk</a>
                @endauth
            </div>
        </nav>

        <div class="nav-actions-desktop">
            @auth
                <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
            @else
                <a class="btn btn--utama btn--kecil" href="{{ route('masuk') }}">Masuk</a>
            @endauth
        </div>
    </div>
</header>
