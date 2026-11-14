@php
    $tagline = $konten['tagline'] ?? 'Layanan Titip Gadai Terpercaya di Makassar';
    $judulHero = e($tagline);
    $jumlahCabang = max(7, $cabang->except('PUSAT')->count());
@endphp
<section class="hero" id="beranda" data-spy="beranda" aria-labelledby="judul-hero">
    <div class="wrap">
        <div>
            <div class="brand-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>PERSIDEN HP · Titip Gadai Aman, Cepat, Terpercaya</span>
            </div>

            <h1 id="judul-hero">{{ $judulHero }}</h1>
            <p class="lead">Layanan titip gadai terpercaya untuk laptop, smartphone, tablet, kamera, BPKB kendaraan bermotor, dan surat berharga dengan biaya jasa transparan dan proses langsung di loket cabang.</p>

            <ul class="hero-perks">
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Taksiran transparan dengan rincian jasa penitipan jelas di muka</span>
                </li>
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Barang &amp; surat berharga disimpan aman dalam tempat khusus bersegel</span>
                </li>
                <li>
                    <span class="perk-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg></span>
                    <span>Fleksibel: durasi mingguan dapat diperpanjang atau ditebus kapan saja</span>
                </li>
            </ul>

            <div class="aksi">
                <a class="btn btn--utama" href="{{ route('publik.simulasi') }}">Hitung Simulasi Jasa</a>
                <a class="btn" href="{{ route('publik.layanan') }}">Lihat Layanan Penitipan</a>
            </div>
        </div>

        <div class="hero-offset-frame">
            <div class="hero-showcase" style="background: transparent; border: none; box-shadow: none;">
                <div class="hero-teller-frame" style="background: transparent; border-radius: var(--radius-lg); padding: 1rem; text-align: center;">
                    <img src="{{ asset('images/logo-persiden-hp.png') }}"
                         alt="Logo PERSIDEN HP Titip Gadai Makassar"
                         class="hero-teller-img"
                         style="max-width: 100%; height: auto; object-fit: contain; filter: drop-shadow(0 14px 28px rgba(0,38,101,0.22));"
                         fetchpriority="high">
                </div>
            </div>
        </div>
    </div>

    <!-- Ticker Bergerak Jenis Barang Jaminan yang Diterima PERSIDEN HP -->
    <div class="wm-ticker-container" aria-label="Daftar barang dan jaminan yang diterima PERSIDEN HP">
        <div class="wm-track slow">
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="10" rx="1.5"></rect><path d="M2.5 19h19"></path></svg>Laptop</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7" y="2.5" width="10" height="19" rx="2.5"></rect><path d="M11 18.5h2"></path></svg>Smartphone / HP</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"></rect><circle cx="12" cy="18" r="1"></circle></svg>Tablet / iPad</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M4 8h3l1.5-2.5h7L17 8h3v11H4z"></path><circle cx="12" cy="13.5" r="3.5"></circle></svg>Kamera &amp; Lensa</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><circle cx="6" cy="16" r="3"></circle><circle cx="18" cy="16" r="3"></circle><path d="M6 16l4-7h5l3 7M9 9H7M13 12h5"></path></svg>BPKB Motor</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 15v-3l2-5h14l2 5v3zM3 12h18"></path><circle cx="7.5" cy="15.5" r="1.5"></circle><circle cx="16.5" cy="15.5" r="1.5"></circle></svg>BPKB Mobil</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>Sertifikat Tanah / Properti</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>Surat Berharga</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>Barang Berharga Lainnya</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="10" rx="1.5"></rect><path d="M2.5 19h19"></path></svg>Laptop</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="7" y="2.5" width="10" height="19" rx="2.5"></rect><path d="M11 18.5h2"></path></svg>Smartphone / HP</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"></rect><circle cx="12" cy="18" r="1"></circle></svg>Tablet / iPad</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M4 8h3l1.5-2.5h7L17 8h3v11H4z"></path><circle cx="12" cy="13.5" r="3.5"></circle></svg>Kamera &amp; Lensa</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><circle cx="6" cy="16" r="3"></circle><circle cx="18" cy="16" r="3"></circle><path d="M6 16l4-7h5l3 7M9 9H7M13 12h5"></path></svg>BPKB Motor</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 15v-3l2-5h14l2 5v3zM3 12h18"></path><circle cx="7.5" cy="15.5" r="1.5"></circle><circle cx="16.5" cy="15.5" r="1.5"></circle></svg>BPKB Mobil</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>Sertifikat Tanah / Properti</span>
            <span class="wm-pill"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>Surat Berharga</span>
        </div>
    </div>

    <div class="wrap hero-bawah">
        <div class="hero-stats" data-muncul>
            <div class="stat-item"><b>{{ $jumlahCabang }} Cabang</b><span>Makassar dan Sekitarnya</span></div>
            <div class="stat-item"><b>1 &amp; 2 Minggu</b><span>Pilihan durasi titip fleksibel</span></div>
            <div class="stat-item"><b>Buka Setiap Hari</b><span>Pukul 08.00 - 22.00 WITA</span></div>
            <div class="stat-item"><b>Aman Bersegel</b><span>Disimpan di ruang khusus cabang</span></div>
        </div>

        <div class="ringkas" data-muncul>
            <div class="ringkas-kepala">
                <p class="kicker">Tentang kami</p>
                <h2>Singkatnya tentang PERSIDEN HP</h2>
            </div>
            <div class="ringkas-isi">
                <p>PERSIDEN HP melayani penitipan barang elektronik (laptop, handphone, tablet, kamera), BPKB kendaraan, dan surat berharga dengan akad yang jelas, penaksiran transparan, dan ruang penyimpanan bersegel stiker khusus di seluruh jaringan cabang Makassar.</p>
                <ol class="ringkas-langkah" aria-label="Cara kami menangani barang Anda">
                    <li><b>1</b> Diperiksa fisik di loket</li>
                    <li><b>2</b> Ditaksir petugas loket</li>
                    <li><b>3</b> Disegel stiker khusus</li>
                    <li><b>4</b> Disimpan aman sampai tebus</li>
                </ol>
            </div>
        </div>
    </div>
</section>
