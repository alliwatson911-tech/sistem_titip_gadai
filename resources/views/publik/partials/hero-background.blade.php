{{-- resources/views/publik/partials/hero-background.blade.php --}}
{{-- Lapis latar belakang lebar penuh (full-width) khusus hero dashboard publik linygadai --}}
<div class="hero-bg-layer" aria-hidden="true">
    {{-- 1. Kanvas SVG Vektor: Bidang kurva melengkung, gradasi emas/kuningan hangat, & pola titik sekuritas --}}
    <svg class="hero-bg-svg" viewBox="0 0 1440 880" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
        <defs>
            {{-- Pola titik-titik aksen (security matrix dots) bernuansa kuningan --}}
            <pattern id="hero-titik-aksen" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="1.8" fill="#B8862B" opacity="0.3"/>
            </pattern>

            {{-- Pola grid halus pelindung nilai (security mesh) di area kanan --}}
            <pattern id="hero-grid-halus" x="0" y="0" width="56" height="56" patternUnits="userSpaceOnUse">
                <path d="M 56 0 L 0 0 0 56" fill="none" stroke="rgba(27, 42, 47, 0.04)" stroke-width="1"/>
                <circle cx="0" cy="0" r="1.8" fill="#B8862B" opacity="0.18"/>
            </pattern>

            {{-- Gradasi kurva diagonal utama (transisi kertas gading ke kuningan hangat) --}}
            <linearGradient id="grad-kurva-utama" x1="820" y1="0" x2="1440" y2="880" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FDF6E2" stop-opacity="0.9"/>
                <stop offset="40%" stop-color="#F7EACD" stop-opacity="0.75"/>
                <stop offset="80%" stop-color="#EFE0B8" stop-opacity="0.55"/>
                <stop offset="100%" stop-color="#E0CC94" stop-opacity="0.38"/>
            </linearGradient>

            {{-- Gradasi lapisan kedua untuk efek kedalaman berlapis --}}
            <linearGradient id="grad-kurva-lapis2" x1="940" y1="0" x2="1440" y2="760" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.8"/>
                <stop offset="50%" stop-color="#FCF7EB" stop-opacity="0.45"/>
                <stop offset="100%" stop-color="#D9BF80" stop-opacity="0.25"/>
            </linearGradient>

            {{-- Gradasi aksen bulat emas bersinar di belakang barang --}}
            <radialGradient id="grad-pendar-emas" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#B8862B" stop-opacity="0.16"/>
                <stop offset="65%" stop-color="#B8862B" stop-opacity="0.04"/>
                <stop offset="100%" stop-color="#B8862B" stop-opacity="0"/>
            </radialGradient>
        </defs>

        {{-- Lapis 1: Pola grid halus di area kanan (mulai x: 780 agar sisi kiri teks bersih) --}}
        <rect x="780" y="0" width="660" height="880" fill="url(#hero-grid-halus)"/>

        {{-- Lapis 2: Pendar hangat bundar di belakang area nota & barang gadai --}}
        <circle cx="1120" cy="460" r="320" fill="url(#grad-pendar-emas)"/>

        {{-- Lapis 3: Bidang kurva organik lebar penuh di sisi kanan --}}
        <path d="M920 0 C1040 140 990 340 1120 520 C1240 700 1340 780 1440 820 L1440 0 Z" fill="url(#grad-kurva-utama)"/>

        {{-- Lapis 4: Bidang kurva kedua yang lebih anggun, membentuk kedalaman berlapis --}}
        <path d="M1060 0 C1150 180 1120 380 1250 560 C1350 700 1400 760 1440 780 L1440 0 Z" fill="url(#grad-kurva-lapis2)"/>

        {{-- Lapis 5: Blok pola titik-titik (dot-matrix) terfokus di kanan atas --}}
        <rect x="1140" y="32" width="260" height="190" rx="16" fill="url(#hero-titik-aksen)"/>

        {{-- Lapis 6: Garis lengkung ritmis kuningan & lumut di sisi kanan --}}
        <path d="M960 0 C1070 160 1020 380 1160 540 C1280 700 1370 780 1440 820" stroke="#B8862B" stroke-width="2" stroke-dasharray="6 8" opacity="0.32"/>
        <path d="M1020 0 C1120 170 1080 390 1210 550 C1310 690 1390 760 1440 800" stroke="#3E5C4A" stroke-width="1.5" opacity="0.2"/>
    </svg>

    {{-- 2. Elemen Orisinal & Emblem Titip Gadai (Vektor Bebas Hotlink & Responsif) --}}
    <div class="hero-bg-emblems">
        {{-- Kartu Info Mengambang 1 (Atas Kanan): Taksir Kilat 15 Menit --}}
        <div class="hero-float-card hero-float-card--atas">
            <div class="float-card-icon float-card-icon--hijau">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="float-card-content">
                <b class="float-card-title">Taksir Kilat 15 Menit</b>
                <span class="float-card-sub">Pencairan tunai di cabang A & B</span>
            </div>
        </div>

        {{-- Emblem A: Komposisi Emas Batangan Murni (Fine Gold 999.9) - Simbol Nilai Taksiran Tertinggi --}}
        <div class="hero-emblem hero-emblem--emas" title="Simbol Taksiran Emas & Logam Mulia Resmi">
            <svg viewBox="0 0 160 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="emblem-svg">
                <defs>
                    <linearGradient id="emas-muka" x1="0" y1="0" x2="160" y2="120" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFE885"/>
                        <stop offset="28%" stop-color="#F2BA36"/>
                        <stop offset="68%" stop-color="#C98616"/>
                        <stop offset="100%" stop-color="#9E5D04"/>
                    </linearGradient>
                    <linearGradient id="emas-kilap" x1="20" y1="10" x2="140" y2="60" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.85"/>
                        <stop offset="40%" stop-color="#FFF2AA" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#C98616" stop-opacity="0"/>
                    </linearGradient>
                    <linearGradient id="emas-samping" x1="0" y1="0" x2="0" y2="100" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#BD7E10"/>
                        <stop offset="100%" stop-color="#6E4002"/>
                    </linearGradient>
                    <filter id="bayang-emas" x="-20%" y="-20%" width="150%" height="150%">
                        <feDropShadow dx="2" dy="8" stdDeviation="8" flood-color="#3A2402" flood-opacity="0.22"/>
                    </filter>
                </defs>
                <g filter="url(#bayang-emas)">
                    {{-- Batang Belakang (Kedua) --}}
                    <path d="M50 18 L125 18 L142 38 L40 38 Z" fill="#E2A627"/>
                    <path d="M125 18 L142 38 L142 54 L125 34 Z" fill="url(#emas-samping)"/>

                    {{-- Batang Utama Depan --}}
                    <path d="M22 42 L112 42 L132 64 L12 64 Z" fill="url(#emas-muka)"/>
                    <path d="M28 44 L58 44 L44 62 L18 62 Z" fill="url(#emas-kilap)"/>
                    <path d="M12 64 L132 64 L132 88 L12 88 Z" fill="url(#emas-samping)"/>
                    <path d="M132 64 L146 50 L146 74 L132 88 Z" fill="#7A4703"/>

                    <text x="70" y="55" font-family="'IBM Plex Mono', monospace" font-size="7.5" font-weight="700" fill="#693B01" letter-spacing="1.2" text-anchor="middle">FINE GOLD</text>
                    <text x="70" y="61" font-family="'IBM Plex Mono', monospace" font-size="5" font-weight="600" fill="#7F4904" letter-spacing="0.8" text-anchor="middle">999.9 · 100g</text>

                    <path d="M118 36 Q118 44 122 44 Q118 44 118 52 Q118 44 114 44 Q118 44 118 36 Z" fill="#FFFFFF"/>
                </g>
            </svg>
            <span class="emblem-label">Emas & Logam Mulia</span>
        </div>

        {{-- Kartu Info Mengambang 2 (Kanan Bawah): Brankas Bersegel Resmi --}}
        <div class="hero-float-card hero-float-card--bawah">
            <div class="float-card-icon float-card-icon--emas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <div class="float-card-content">
                <b class="float-card-title">Brankas Bersegel Resmi</b>
                <span class="float-card-sub">Stiker nomor unik di depan nasabah</span>
            </div>
        </div>

        {{-- Emblem B: Perangkat Elektronik Presisi (Laptop & Ponsel Cerdas) --}}
        <div class="hero-emblem hero-emblem--elektronik" title="Simbol Titip Gadai Elektronik & Gadget">
            <svg viewBox="0 0 160 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="emblem-svg">
                <defs>
                    <linearGradient id="grad-layar-laptop" x1="28" y1="20" x2="108" y2="76" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#1B2A2F"/>
                        <stop offset="100%" stop-color="#2D4750"/>
                    </linearGradient>
                    <linearGradient id="grad-layar-ponsel" x1="106" y1="36" x2="140" y2="92" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#2D4750"/>
                        <stop offset="100%" stop-color="#1B2A2F"/>
                    </linearGradient>
                    <filter id="bayang-gadget" x="-20%" y="-20%" width="150%" height="150%">
                        <feDropShadow dx="3" dy="9" stdDeviation="7" flood-color="#1B2A2F" flood-opacity="0.18"/>
                    </filter>
                </defs>
                <g filter="url(#bayang-gadget)">
                    <rect x="26" y="18" width="84" height="56" rx="4" fill="#1B2A2F" stroke="#B8862B" stroke-width="1.2"/>
                    <rect x="30" y="22" width="76" height="48" rx="2" fill="url(#grad-layar-laptop)"/>
                    <circle cx="68" cy="46" r="13" fill="rgba(184, 134, 43, 0.2)" stroke="#B8862B" stroke-width="1.2"/>
                    <path d="M64 40 V52 H72" stroke="#FFFCF5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>

                    <path d="M14 74 L122 74 L114 82 L22 82 Z" fill="#2E4047" stroke="#1B2A2F" stroke-width="1"/>
                    <rect x="56" y="75" width="24" height="3" rx="1.5" fill="#4B636C"/>

                    <rect x="106" y="34" width="34" height="58" rx="5" fill="#1B2A2F" stroke="#B8862B" stroke-width="1.2"/>
                    <rect x="109" y="38" width="28" height="50" rx="3" fill="url(#grad-layar-ponsel)"/>
                    <circle cx="123" cy="36" r="1" fill="#4B636C"/>
                    <path d="M123 53 L123 59 M120 56 L123 59 L127 54" stroke="#3E5C4A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="123" cy="56" r="7" fill="none" stroke="#3E5C4A" stroke-width="1.2"/>
                </g>
            </svg>
            <span class="emblem-label">Laptop & Smartphone</span>
        </div>
    </div>
</div>
