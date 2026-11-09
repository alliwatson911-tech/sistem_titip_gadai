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
            <svg class="label-barang" viewBox="0 0 320 430" role="img" aria-label="Ilustrasi label barang bernomor segel dengan keterangan jatuh tempo {{ $token['tenor'] }} hari">
                <path d="M160 -4 C160 34 118 40 126 74" fill="none" stroke="#1B2A2F" stroke-width="2.5"/>
                <path class="kertas-tag" d="M92 62h136l34 34v296a12 12 0 0 1-12 12H70a12 12 0 0 1-12-12V96z"/>
                <circle cx="160" cy="92" r="10" fill="#F4EFE6" stroke="#1B2A2F" stroke-width="2.5"/>
                <text x="82" y="152" font-family="IBM Plex Mono, monospace" font-size="13" letter-spacing="2.4">NOTA GADAI</text>
                <text x="82" y="174" font-family="IBM Plex Mono, monospace" font-size="12" opacity=".7">No. LG-0000 (resmi)</text>
                <path d="M82 190h156" stroke="#1B2A2F" stroke-width="1.5" stroke-dasharray="4 5"/>
                <text x="82" y="272" font-family="Fraunces, Georgia, serif" font-size="92" font-weight="600" letter-spacing="-3">{{ $token['tenor'] }}</text>
                <text x="86" y="300" font-family="IBM Plex Mono, monospace" font-size="14" letter-spacing="2">HARI · JATUH TEMPO</text>
                <path d="M82 322h156" stroke="#1B2A2F" stroke-width="1.5" stroke-dasharray="4 5"/>
                <text x="82" y="352" font-family="IBM Plex Mono, monospace" font-size="12">Segel Brankas No. ___</text>
                <text x="82" y="376" font-family="IBM Plex Mono, monospace" font-size="11" opacity=".7">Perpanjang / Cicil / Tebus</text>
                <g transform="rotate(-12 212 244)">
                    <circle cx="212" cy="244" r="30" fill="#B8862B" stroke="#1B2A2F" stroke-width="2.5"/>
                    <circle cx="212" cy="244" r="24" fill="none" stroke="#1B2A2F" stroke-width="1.5" stroke-dasharray="3 4"/>
                    <path d="M205 233v22h16" fill="none" stroke="#1B2A2F" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
            </svg>
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
