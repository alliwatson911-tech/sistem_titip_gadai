<footer class="kaki-modern">
    <div class="wrap">
        <div class="kaki-grid">
            <!-- Kolom 1: Merek & Profil -->
            <div class="kaki-kolom">
                <a class="merek" href="#beranda" style="color: var(--kertas); margin-bottom: .85rem;">
                    <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#F4EFE6" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <div class="merek-teks">
                        <span class="merek-nama" style="color: var(--kertas);">linygadai</span>
                        <span class="merek-sub" style="color: var(--kuningan);">Titip Gadai Elektronik</span>
                    </div>
                </a>
                <p style="margin:.85rem 0 .6rem; font-size: .95rem; line-height: 1.6; opacity: .85;">{{ $konten['tagline'] ?? 'Titip hari ini, tenang sampai tebus.' }}</p>
                @if ($pusat?->jam_buka)
                    <p style="font-size: .88rem; color: var(--kuningan); font-family: var(--f-mono); margin: 0;">Jam Layanan: {{ $pusat->jam_buka }}</p>
                @endif
            </div>

            <!-- Kolom 2: Navigasi Cepat -->
            <div class="kaki-kolom">
                <h4>Navigasi Pintas</h4>
                <ul aria-label="Navigasi cepat situs">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#produk">Produk Gadai</a></li>
                    <li><a href="#standar">Standar Layanan</a></li>
                    <li><a href="#panduan">Panduan Titip</a></li>
                    <li><a href="#simulasi">Hitung Simulasi</a></li>
                    <li><a href="#faq">FAQ & Informasi</a></li>
                    <li><a href="#tentang">Tentang & Cabang</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Layanan & Cabang -->
            <div class="kaki-kolom">
                <h4>Cabang & Kontak</h4>
                <ul>
                    @php
                        $cabangList = $cabang->except('PUSAT')->filter(fn ($c) => $c->alamat || $c->telepon);
                    @endphp
                    @foreach ($cabangList as $c)
                        <li style="font-size: .92rem; line-height: 1.4; opacity: .85;">
                            <strong style="color: #fff;">{{ $c->nama }}</strong><br>
                            {{ $c->alamat }}
                            @if ($c->telepon)<br><span style="font-family: var(--f-mono); font-size: .85rem; color: var(--kuningan);">Telp: {{ $c->telepon }}</span>@endif
                        </li>
                    @endforeach
                    @if ($pusat?->email)
                        <li><a href="mailto:{{ $pusat->email }}">Email: {{ $pusat->email }}</a></li>
                    @endif
                </ul>
            </div>

            <!-- Kolom 4: Keamanan & Akun -->
            <div class="kaki-kolom">
                <h4>Akun & Keamanan</h4>
                <ul>
                    @auth
                        <li><a href="{{ route('panel') }}">Panel Saya</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; color:inherit; font:inherit; cursor:pointer; opacity:.85;">Keluar</button>
                            </form>
                        </li>
                    @else
                        <li><a href="#akun">Masuk Anggota</a></li>
                        <li><a href="#akun">Daftar Akun Baru</a></li>
                    @endauth
                    <li style="margin-top: .6rem; font-size: .85rem; line-height: 1.5; opacity: .75;">
                        Barang disimpan dalam brankas khusus bersegel stiker resmi. Data identitas dienkripsi.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bagian Bawah Footer -->
        <div class="kaki-bawah">
            <div>
                &copy; {{ date('Y') }} <strong>linygadai</strong> &mdash; Titip Gadai Elektronik Resmi Makassar. Hak cipta dilindungi.
            </div>
            <div>
                <a href="#beranda" style="color: var(--kuningan); text-decoration: none; font-weight: 600;">Kembali ke Atas &uarr;</a>
            </div>
        </div>
    </div>
</footer>
