@php
    $tagline = $konten['tagline'] ?? 'Titip hari ini, tenang sampai tebus.';
    $judulHero = preg_replace('/\b(tenang)\b/iu', '<em>$1</em>', e($tagline));
    $jumlahCabang = $cabang->except('PUSAT')->count();
@endphp
<section class="hero" id="beranda" data-spy="beranda" aria-labelledby="judul-hero">
    <div class="wrap">
        <div>
            <div class="eyebrow-pill">
                <span class="live-pulse-dot" aria-hidden="true"></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>TITIP GADAI ELEKTRONIK RESMI · BIAYA JELAS DI MUKA</span>
            </div>

            <h1 id="judul-hero">{!! $judulHero !!}</h1>
            <p class="lead">{{ $konten['sambutan'] ?? '' }}</p>

            <ul class="hero-perks">
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Taksiran transparan dengan biaya sewa tempat & admin jelas di muka</span>
                </li>
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Barang elektronik disimpan aman dalam brankas khusus bersegel resmi</span>
                </li>
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Fleksibel: dapat diperpanjang, dicicil, atau ditebus kapan saja</span>
                </li>
            </ul>

            <div class="aksi">
                <a class="btn btn--utama" href="{{ route('publik.simulasi') }}">Hitung Simulasi Titip</a>
                <a class="btn" href="{{ route('publik.layanan') }}">Lihat Layanan Gadai</a>
            </div>
        </div>

        <div class="hero-offset-frame">
            <div class="hero-showcase">
                <div class="hero-teller-frame">
                    <img src="{{ asset('images/hero-teller-linygadai.png') }}"
                         alt="Loket Resmi Gadai Linygadai Makassar - Pelayanan Aman dan Transparan"
                         class="hero-teller-img"
                         width="480"
                         height="480"
                         fetchpriority="high">
                </div>
                <div class="hero-floating-card">
                    <div class="floating-badge-seal" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="floating-info">
                        <b>Loket Resmi linygadai</b>
                        <span>Tenor {{ $token['tenor'] }} Hari · Brankas Bersegel</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticker Bergerak Jenis Barang Jaminan yang Diterima -->
    <div class="wm-ticker-container" aria-label="Daftar barang dan jaminan berharga yang diterima linygadai">
        <div class="wm-track slow"><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7" y="2.5" width="10" height="19" rx="2.5"></rect><path d="M11 18.5h2"></path></svg>Handphone</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="10" rx="1.5"></rect><path d="M2.5 19h19"></path></svg>Laptop</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M4 8h3l1.5-2.5h7L17 8h3v11H4z"></path><circle cx="12" cy="13.5" r="3.5"></circle></svg>Kamera</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M7 4h10l3 5-8 11L4 9z"></path><path d="M4 9h16M9.5 4L12 9l2.5-5M12 9v11"></path></svg>Perhiasan Emas</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 17l2.5-8h13L21 17z"></path><path d="M8 13h8"></path></svg>Emas Batangan</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="12" rx="2"></rect><path d="M9 21h6M12 17v4"></path></svg>TV &amp; Elektronik</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="9" rx="4.5"></rect><path d="M8 10.5v4M6 12.5h4M16 11.5h.01M18 13.5h.01"></path></svg>Konsol Game</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7.5" y="6.5" width="9" height="11" rx="3"></rect><path d="M9 6.5L10 2.5h4l1 4M9 17.5l1 4h4l1-4M12 10v2.5l1.5 1"></path></svg>Jam Tangan</span><span class="wm-pill"><svg viewBox="0 0 24 24"><circle cx="6" cy="16" r="3"></circle><circle cx="18" cy="16" r="3"></circle><path d="M6 16l4-7h5l3 7M9 9H7M13 12h5"></path></svg>BPKB Motor</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 15v-3l2-5h14l2 5v3zM3 12h18"></path><circle cx="7.5" cy="15.5" r="1.5"></circle><circle cx="16.5" cy="15.5" r="1.5"></circle></svg>BPKB Mobil</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7" y="2.5" width="10" height="19" rx="2.5"></rect><path d="M11 18.5h2"></path></svg>Handphone</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="10" rx="1.5"></rect><path d="M2.5 19h19"></path></svg>Laptop</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M4 8h3l1.5-2.5h7L17 8h3v11H4z"></path><circle cx="12" cy="13.5" r="3.5"></circle></svg>Kamera</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M7 4h10l3 5-8 11L4 9z"></path><path d="M4 9h16M9.5 4L12 9l2.5-5M12 9v11"></path></svg>Perhiasan Emas</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 17l2.5-8h13L21 17z"></path><path d="M8 13h8"></path></svg>Emas Batangan</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="12" rx="2"></rect><path d="M9 21h6M12 17v4"></path></svg>TV &amp; Elektronik</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="9" rx="4.5"></rect><path d="M8 10.5v4M6 12.5h4M16 11.5h.01M18 13.5h.01"></path></svg>Konsol Game</span><span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7.5" y="6.5" width="9" height="11" rx="3"></rect><path d="M9 6.5L10 2.5h4l1 4M9 17.5l1 4h4l1-4M12 10v2.5l1.5 1"></path></svg>Jam Tangan</span><span class="wm-pill"><svg viewBox="0 0 24 24"><circle cx="6" cy="16" r="3"></circle><circle cx="18" cy="16" r="3"></circle><path d="M6 16l4-7h5l3 7M9 9H7M13 12h5"></path></svg>BPKB Motor</span><span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 15v-3l2-5h14l2 5v3zM3 12h18"></path><circle cx="7.5" cy="15.5" r="1.5"></circle><circle cx="16.5" cy="15.5" r="1.5"></circle></svg>BPKB Mobil</span></div>
    </div>

    <div class="wrap hero-bawah">
        <div class="hero-stats" data-muncul>
            <div class="stat-item"><b>{{ $jumlahCabang }} Cabang</b><span>Aktif di Makassar (A & B)</span></div>
            <div class="stat-item"><b>{{ $token['tenor'] }} Hari</b><span>Tenor standar, dapat diperpanjang</span></div>
            <div class="stat-item"><b>s.d. 80%</b><span>Plafon nilai taksiran barang</span></div>
            <div class="stat-item"><b>15 Menit</b><span>Proses taksir cepat langsung cair</span></div>
        </div>

        <div class="ringkas" data-muncul>
            <div class="ringkas-kepala">
                <p class="kicker">Tentang kami</p>
                <h2>Singkatnya tentang linygadai</h2>
            </div>
            <div class="ringkas-isi">
                <p>{{ $konten['ringkasan'] ?? '' }}</p>
                <ol class="ringkas-langkah" aria-label="Cara kami menangani barang Anda">
                    <li><b>1</b> Diperiksa fisik</li>
                    <li><b>2</b> Ditaksir analis</li>
                    <li><b>3</b> Disegel stiker</li>
                    <li><b>4</b> Disimpan dalam brankas</li>
                </ol>
            </div>
        </div>
    </div>
</section>
