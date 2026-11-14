@php
    $pusatModel = isset($pusat) ? $pusat : null;
    $cabangList = isset($cabang) ? $cabang->except('PUSAT')->filter(fn ($c) => $c->alamat || $c->telepon) : collect();
    $tagline = config('istilah.merek.tagline', 'Aman · Cepat · Terpercaya');
    $jamLayanan = config('istilah.merek.jam_standar', '08.00 - 22.00 WITA');
@endphp
<footer class="kaki-modern">
    <div class="wrap">
        <div class="kaki-grid">
            <!-- Kolom 1: Merek & Profil -->
            <div class="kaki-kolom">
                <a class="merek" href="#beranda" style="color: var(--kertas); margin-bottom: .85rem; align-items: center; gap: .75rem;">
                    <img src="{{ asset('images/logo-persiden-hp-compact.png') }}" alt="Logo PERSIDEN HP" style="height: 42px; width: auto; object-fit: contain;">
                    <div class="merek-teks">
                        <span class="merek-nama" style="color: var(--kertas);">PERSIDEN <span style="color: var(--merah-hp);">HP</span></span>
                        <span class="merek-sub" style="color: var(--kuningan);">Titip Gadai</span>
                    </div>
                </a>
                <p style="margin:.85rem 0 .6rem; font-size: .95rem; line-height: 1.6; opacity: .85;">{{ $tagline }}</p>
                <p style="font-size: .88rem; color: var(--kuningan); font-family: var(--f-mono); margin: 0;">Jam Layanan: {{ $jamLayanan }}</p>
            </div>

            <!-- Kolom 2: Navigasi Cepat -->
            <div class="kaki-kolom">
                <h4>Navigasi Pintas</h4>
                <ul aria-label="Navigasi cepat situs">
                    <li><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li><a href="{{ route('publik.tentang') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('publik.layanan') }}">Layanan Penitipan</a></li>
                    <li><a href="{{ route('publik.simulasi') }}">Simulasi Jasa</a></li>
                    <li><a href="{{ route('publik.cara-penitipan') }}">Cara Penitipan</a></li>
                    <li><a href="{{ route('publik.syarat') }}">Syarat &amp; Ketentuan</a></li>
                    <li><a href="{{ route('publik.promo') }}">Promo</a></li>
                    <li><a href="{{ route('publik.kontak') }}">Kontak &amp; Cabang</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Layanan & Cabang -->
            <div class="kaki-kolom">
                <h4>Cabang &amp; Kontak</h4>
                <ul>
                    @foreach ($cabangList->take(4) as $c)
                        <li style="font-size: .92rem; line-height: 1.4; opacity: .85;">
                            <strong style="color: #fff;">{{ $c->nama_tampilan ?? $c->nama }}</strong><br>
                            {{ $c->alamat }}
                            @if ($c->is_verified && $c->telepon)<br><span style="font-family: var(--f-mono); font-size: .85rem; color: var(--kuningan);">Telp/WA: {{ $c->telepon }}</span>@endif
                        </li>
                    @endforeach
                    <li><a href="{{ route('publik.kontak') }}" style="color: var(--kuningan); font-weight: 600;">Lihat Semua Cabang &rarr;</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Keamanan & Akun -->
            <div class="kaki-kolom">
                <h4>Akses Masuk</h4>
                <ul>
                    @auth
                        <li><a href="{{ route('panel') }}">Panel Saya</a></li>
                        <li>
                            <form action="{{ route('keluar') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; color:inherit; font:inherit; cursor:pointer; opacity:.85;">Keluar</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('masuk') }}">Masuk Penitip / Staf</a></li>
                    @endauth
                    <li style="margin-top: .6rem; font-size: .85rem; line-height: 1.5; opacity: .75;">
                        Pendaftaran akun penitip dilakukan langsung di loket cabang resmi oleh petugas saat penyerahan barang titipan.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bagian Bawah Footer -->
        <div class="kaki-bawah">
            <div class="kaki-hakcipta">
                &copy; {{ date('Y') }} <strong>PERSIDEN HP</strong> &mdash; Titip Gadai Aman, Cepat, Terpercaya. Hak cipta dilindungi.
            </div>
            <div class="kaki-dev">
                <a href="https://www.linyjayainformatika.co.id/" target="_blank" rel="noopener noreferrer" class="kaki-dev-link" title="Kunjungi PT Linijayainformatika">
                    Developed by PT Linijayainformatika
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline-block; vertical-align:middle; margin-left:3px; opacity:.85;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
            </div>
            <div class="kaki-kembali">
                <a href="#beranda" style="color: var(--kuningan); text-decoration: none; font-weight: 600;">Kembali ke Atas &uarr;</a>
            </div>
        </div>
    </div>
</footer>
