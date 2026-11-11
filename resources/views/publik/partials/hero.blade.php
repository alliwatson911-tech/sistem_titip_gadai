@php
    $tagline = $konten['tagline'] ?? 'Titip hari ini, tenang sampai tebus.';
    $judulHero = preg_replace('/\b(tenang)\b/iu', '<em>$1</em>', e($tagline));
    $jumlahCabang = $cabang->except('PUSAT')->count();
@endphp
<section class="hero" id="beranda" data-spy="beranda" aria-labelledby="judul-hero">
    <div class="wrap">
        <div>
            <div class="eyebrow-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>TITIP GADAI ELEKTRONIK RESMI · BIAYA JELAS DI MUKA</span>
            </div>

            <h1 id="judul-hero">{!! $judulHero !!}</h1>
            <p class="lead">{{ $konten['sambutan'] ?? '' }}</p>

            <ul class="hero-perks">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>Taksiran transparan dengan biaya sewa tempat & admin jelas di muka</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>Barang elektronik disimpan aman dalam brankas khusus bersegel resmi</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
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
