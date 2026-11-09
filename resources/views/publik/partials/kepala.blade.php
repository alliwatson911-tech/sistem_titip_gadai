<header class="kepala">
    <div class="wrap">
        <a class="merek" href="#beranda" aria-label="linygadai, ke atas halaman">
            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#1B2A2F" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="merek-teks">
                <span class="merek-nama">linygadai</span>
                <span class="merek-sub">Makassar · Cabang A & B</span>
            </div>
        </a>

        <button type="button" class="tombol-menu" id="tombol-menu" aria-expanded="false" aria-controls="menu" aria-label="Buka menu navigasi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>

        <nav class="menu" id="menu" aria-label="Menu utama">
            <a href="#beranda">Beranda</a>
            <a href="#produk">Produk</a>
            <a href="#standar">Standar</a>
            <a href="#panduan">Panduan</a>
            <a href="#simulasi">Simulasi</a>
            <a href="#faq">FAQ</a>
            <a href="#tentang">Tentang</a>

            <div class="nav-actions-mobile">
                <a class="btn btn--kecil" href="#simulasi">Hitung Simulasi</a>
                @auth
                    <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
                @else
                    <a class="btn btn--utama btn--kecil" href="#akun">Masuk / Daftar</a>
                @endauth
            </div>
        </nav>

        <div class="nav-actions nav-actions-desktop">
            <a class="btn btn--kecil" href="#simulasi">Hitung Simulasi</a>
            @auth
                <a class="btn btn--utama btn--kecil" href="{{ route('panel') }}">Panel Saya</a>
            @else
                <a class="btn btn--utama btn--kecil" href="#akun">Masuk / Daftar</a>
            @endauth
        </div>
    </div>
</header>
