{{-- Latar Belakang Penuh (Full-Width Decorative Layer) untuk Hero Dashboard Beranda --}}
<div class="hero-bg-layer" aria-hidden="true">
    {{-- Gradasi Dasar Lebar Penuh (Parchment ke Kuningan Luminous) --}}
    <div class="hero-bg-wash"></div>

    {{-- Kanvas Vektor SVG: Gelombang Organik, Guilloche Dokumen Berharga, Koin & Segel --}}
    <svg class="hero-bg-svg" viewBox="0 0 1440 860" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            {{-- Gradasi Bidang Melengkung Kanan --}}
            <linearGradient id="hbg-sweep" x1="680" y1="0" x2="1440" y2="860" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#F4EFE6" stop-opacity="0" />
                <stop offset="35%" stop-color="#EAE2D2" stop-opacity="0.45" />
                <stop offset="70%" stop-color="#FDF6E2" stop-opacity="0.85" />
                <stop offset="100%" stop-color="#EAE2D2" stop-opacity="0.75" />
            </linearGradient>

            <linearGradient id="hbg-glow" x1="1100" y1="120" x2="1100" y2="600" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#B8862B" stop-opacity="0.18" />
                <stop offset="50%" stop-color="#B8862B" stop-opacity="0.06" />
                <stop offset="100%" stop-color="#B8862B" stop-opacity="0" />
            </linearGradient>

            <linearGradient id="hbg-gold-line" x1="800" y1="0" x2="1440" y2="700" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#B8862B" stop-opacity="0.05" />
                <stop offset="50%" stop-color="#B8862B" stop-opacity="0.38" />
                <stop offset="100%" stop-color="#7F5A0E" stop-opacity="0.12" />
            </linearGradient>

            <linearGradient id="hbg-moss-line" x1="750" y1="200" x2="1440" y2="860" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#3E5C4A" stop-opacity="0.04" />
                <stop offset="50%" stop-color="#3E5C4A" stop-opacity="0.22" />
                <stop offset="100%" stop-color="#3E5C4A" stop-opacity="0.06" />
            </linearGradient>

            {{-- Pola Titik (Dot Matrix) Emas Khas Nota --}}
            <pattern id="hbg-dots" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="1.5" fill="#B8862B" fill-opacity="0.32" />
            </pattern>
        </defs>

        {{-- 1. Bidang Warna Diagonal / Lengkung Organik di Kanan --}}
        <path d="M720 0C840 180 920 280 980 420C1040 560 1140 700 1440 760V0H720Z" fill="url(#hbg-sweep)" />
        <path d="M860 0C960 210 1060 380 1160 520C1230 620 1330 710 1440 740V0H860Z" fill="url(#hbg-glow)" />

        {{-- 2. Pola Titik (Dot Matrix Accent) di Area Transisi --}}
        <rect x="940" y="80" width="192" height="144" fill="url(#hbg-dots)" rx="8" />

        {{-- 3. Garis Kontur Gelombang Halus --}}
        <path d="M720 0C840 180 920 280 980 420C1040 560 1140 700 1440 760" stroke="url(#hbg-gold-line)" stroke-width="2" stroke-linecap="round" />
        <path d="M760 0C880 190 955 300 1015 440C1075 580 1175 720 1440 800" stroke="url(#hbg-moss-line)" stroke-width="1.5" stroke-dasharray="4 6" />

        {{-- 4. Pola Guilloche Dokumen Berharga (Security Print Lines / Tanda Sah & Aman) --}}
        <g opacity="0.85">
            <path d="M680 120 C 820 180, 960 90, 1140 200 C 1260 280, 1340 220, 1440 310" stroke="#B8862B" stroke-opacity="0.20" stroke-width="1.2" fill="none"/>
            <path d="M690 135 C 830 195, 970 105, 1150 215 C 1270 295, 1350 235, 1440 325" stroke="#B8862B" stroke-opacity="0.16" stroke-width="1" fill="none"/>
            <path d="M700 150 C 840 210, 980 120, 1160 230 C 1280 310, 1360 250, 1440 340" stroke="#B8862B" stroke-opacity="0.12" stroke-width="0.9" fill="none"/>
            <path d="M710 165 C 850 225, 990 135, 1170 245 C 1290 325, 1370 265, 1440 355" stroke="#3E5C4A" stroke-opacity="0.14" stroke-width="1" fill="none"/>
            <path d="M720 180 C 860 240, 1000 150, 1180 260 C 1300 340, 1380 280, 1440 370" stroke="#B8862B" stroke-opacity="0.10" stroke-width="0.8" fill="none"/>
        </g>

        {{-- 5. Motif Koin Emas & Cincin Presisi (Precision Appraiser & Gold Seal Ring Watermark) --}}
        <g transform="translate(1220, 240)" opacity="0.7">
            {{-- Cincin Luar Bergerigi Halus --}}
            <circle cx="0" cy="0" r="105" stroke="#B8862B" stroke-opacity="0.22" stroke-width="1.5" stroke-dasharray="3 5" fill="none" />
            <circle cx="0" cy="0" r="95" stroke="#B8862B" stroke-opacity="0.18" stroke-width="1" fill="none" />
            <circle cx="0" cy="0" r="75" stroke="#B8862B" stroke-opacity="0.14" stroke-width="2" fill="none" />
            <circle cx="0" cy="0" r="55" stroke="#3E5C4A" stroke-opacity="0.16" stroke-width="1" stroke-dasharray="6 4" fill="none" />
            <circle cx="0" cy="0" r="30" stroke="#B8862B" stroke-opacity="0.25" stroke-width="1.5" fill="none" />
            {{-- Garis Bidik Penaksir (Crosshairs) --}}
            <line x1="-115" y1="0" x2="115" y2="0" stroke="#B8862B" stroke-opacity="0.15" stroke-width="1" />
            <line x1="0" y1="-115" x2="0" y2="115" stroke="#B8862B" stroke-opacity="0.15" stroke-width="1" />
        </g>

        {{-- 6. Aksen Cincin Sekunder di Bagian Bawah Kanan --}}
        <g transform="translate(1360, 560)" opacity="0.65">
            <circle cx="0" cy="0" r="80" stroke="#B8862B" stroke-opacity="0.18" stroke-width="1.2" stroke-dasharray="4 6" fill="none" />
            <circle cx="0" cy="0" r="50" stroke="#3E5C4A" stroke-opacity="0.15" stroke-width="1" fill="none" />
        </g>
    </svg>
</div>
