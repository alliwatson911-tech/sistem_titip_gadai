{{-- Ilustrasi Vektor Kategori Jaminan linygadai (Emas, Elektronik, BPKB Kendaraan, Properti, Surat Berharga) --}}
@switch($jenis)
    @case('emas')
        <svg viewBox="0 0 320 180" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <defs>
                <linearGradient id="goldGrad-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FBEA9D"/>
                    <stop offset="40%" stop-color="#E5C358"/>
                    <stop offset="70%" stop-color="#D4AF37"/>
                    <stop offset="100%" stop-color="#9A7718"/>
                </linearGradient>
                <linearGradient id="goldRing-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFF2B2"/>
                    <stop offset="60%" stop-color="#D4AF37"/>
                    <stop offset="100%" stop-color="#7B5B0D"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="90" r="75" fill="rgba(212, 175, 55, 0.12)"/>
            <!-- Batangan Emas -->
            <g class="anim-svg">
                <polygon points="95,65 185,65 170,125 80,125" fill="url(#goldGrad-{{ $id ?? 'def' }})" stroke="#8F6910" stroke-width="1.5"/>
                <polygon points="185,65 205,75 190,135 170,125" fill="#C19927" stroke="#8F6910" stroke-width="1.5"/>
                <polygon points="80,125 170,125 190,135 100,135" fill="#8F6910"/>
                <text x="132" y="90" font-family="'Instrument Sans', sans-serif" font-weight="700" font-size="11" fill="#5A4004" text-anchor="middle" letter-spacing="1">FINE GOLD</text>
                <text x="132" y="104" font-family="'IBM Plex Mono', monospace" font-weight="700" font-size="12" fill="#3D2B02" text-anchor="middle" letter-spacing="0.5">999.9</text>
                <text x="132" y="117" font-family="'Instrument Sans', sans-serif" font-weight="600" font-size="9" fill="#5A4004" text-anchor="middle">100g · CERTIFIED</text>
            </g>
            <!-- Kalung Emas -->
            <ellipse cx="225" cy="115" rx="36" ry="18" fill="none" stroke="url(#goldRing-{{ $id ?? 'def' }})" stroke-width="5" stroke-dasharray="7 3"/>
            <!-- Cincin Emas Permata -->
            <g class="anim-svg">
                <circle cx="230" cy="82" r="21" fill="none" stroke="url(#goldRing-{{ $id ?? 'def' }})" stroke-width="4.5"/>
                <polygon points="230,53 237,62 223,62" fill="#FFFFFF" stroke="#D4AF37" stroke-width="1.5"/>
                <polygon points="230,53 226,62 234,62" fill="#EAF6F2"/>
            </g>
        </svg>
        @break

    @case('elektronik')
        <svg viewBox="0 0 320 180" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <defs>
                <linearGradient id="screenGrad-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#164E3D"/>
                    <stop offset="100%" stop-color="#0F4C3A"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="90" r="75" fill="rgba(15, 76, 58, 0.08)"/>
            <!-- Laptop -->
            <g class="anim-svg">
                <rect x="70" y="42" width="145" height="92" rx="6" fill="#2C3330" stroke="#0F4C3A" stroke-width="2"/>
                <rect x="76" y="48" width="133" height="80" rx="3" fill="url(#screenGrad-{{ $id ?? 'def' }})"/>
                <rect x="86" y="60" width="40" height="6" rx="2" fill="#D4AF37"/>
                <rect x="86" y="72" width="70" height="4" rx="2" fill="rgba(255,255,255,0.4)"/>
                <rect x="86" y="80" width="55" height="4" rx="2" fill="rgba(255,255,255,0.3)"/>
                <path d="M140 105 L160 85 L180 95 L195 75" fill="none" stroke="#D4AF37" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M50 134 L235 134 L225 144 L60 144 Z" fill="#D8D3C8" stroke="#0F4C3A" stroke-width="1.8"/>
                <rect x="130" y="136" width="25" height="3" rx="1.5" fill="#8F8A7E"/>
            </g>
            <!-- Smartphone -->
            <g class="anim-svg">
                <rect x="210" y="55" width="48" height="92" rx="8" fill="#1C2120" stroke="#D4AF37" stroke-width="2"/>
                <rect x="214" y="60" width="40" height="82" rx="5" fill="#143D31"/>
                <rect x="227" y="63" width="14" height="4" rx="2" fill="#000"/>
                <rect x="220" y="74" width="28" height="14" rx="3" fill="rgba(212, 175, 55, 0.3)"/>
                <circle cx="234" cy="110" r="10" fill="none" stroke="#D4AF37" stroke-width="2"/>
                <path d="M234 105 v5 h3" stroke="#D4AF37" stroke-width="1.5" stroke-linecap="round"/>
            </g>
        </svg>
        @break

    @case('kendaraan')
        <svg viewBox="0 0 320 180" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <defs>
                <linearGradient id="bpkbCover-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#143D31"/>
                    <stop offset="100%" stop-color="#09281F"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="90" r="75" fill="rgba(212, 175, 55, 0.1)"/>
            <!-- Buku BPKB -->
            <g class="anim-svg">
                <rect x="68" y="42" width="85" height="105" rx="5" fill="#EFE9DE" stroke="#B8A995" stroke-width="1.5" transform="rotate(-6 110 94)"/>
                <rect x="65" y="40" width="85" height="105" rx="5" fill="url(#bpkbCover-{{ $id ?? 'def' }})" stroke="#D4AF37" stroke-width="2" transform="rotate(-6 107 92)"/>
                <g transform="rotate(-6 107 92)">
                    <circle cx="107" cy="74" r="14" fill="none" stroke="#D4AF37" stroke-width="1.8"/>
                    <path d="M107 65 l3 5 5 1-4 4 1 5-5-3-5 3 1-5-4-4 5-1z" fill="#D4AF37"/>
                    <text x="107" y="104" font-family="'Fraunces', Georgia, serif" font-weight="700" font-size="14" fill="#D4AF37" text-anchor="middle" letter-spacing="2">BPKB</text>
                    <text x="107" y="116" font-family="'Instrument Sans', sans-serif" font-weight="600" font-size="7" fill="#E4EFEA" text-anchor="middle" letter-spacing="1">REPUBLIK INDONESIA</text>
                    <rect x="73" y="46" width="69" height="93" rx="3" fill="none" stroke="#D4AF37" stroke-width="0.8" stroke-dasharray="3 2"/>
                </g>
            </g>
            <!-- Mobil & Motor -->
            <g class="anim-svg">
                <path d="M165 110 L185 88 L235 88 L255 106 L275 110 A6 6 0 0 1 280 116 L280 128 L155 128 L155 116 A6 6 0 0 1 165 110 Z" fill="#0F4C3A" stroke="#D4AF37" stroke-width="2"/>
                <circle cx="185" cy="128" r="13" fill="#222" stroke="#D4AF37" stroke-width="2"/>
                <circle cx="185" cy="128" r="5" fill="#EFE9DE"/>
                <circle cx="250" cy="128" r="13" fill="#222" stroke="#D4AF37" stroke-width="2"/>
                <circle cx="250" cy="128" r="5" fill="#EFE9DE"/>
                <polygon points="188,94 212,94 212,108 174,108" fill="#E4EFEA" opacity="0.85"/>
                <polygon points="218,94 233,94 248,108 218,108" fill="#E4EFEA" opacity="0.85"/>
                <path d="M140 85 L154 68 L168 76 L162 96" fill="none" stroke="#D4AF37" stroke-width="3" stroke-linecap="round"/>
                <circle cx="143" cy="98" r="12" fill="none" stroke="#0F4C3A" stroke-width="3.5"/>
            </g>
        </svg>
        @break

    @case('properti')
        <svg viewBox="0 0 320 180" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <defs>
                <linearGradient id="shmCover-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#1B4332"/>
                    <stop offset="100%" stop-color="#0F4C3A"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="90" r="75" fill="rgba(27, 67, 50, 0.08)"/>
            <!-- Sertifikat Tanah SHM/SHGB -->
            <g class="anim-svg">
                <rect x="65" y="38" width="95" height="110" rx="4" fill="#F8F5F0" stroke="#C9BFA8" stroke-width="1.5" transform="rotate(-4 112 93)"/>
                <rect x="62" y="35" width="95" height="110" rx="4" fill="url(#shmCover-{{ $id ?? 'def' }})" stroke="#D4AF37" stroke-width="2" transform="rotate(-4 109 90)"/>
                <g transform="rotate(-4 109 90)">
                    <rect x="70" y="43" width="79" height="94" rx="2" fill="none" stroke="#D4AF37" stroke-width="0.8" stroke-dasharray="4 2"/>
                    <circle cx="109" cy="68" r="13" fill="#D4AF37" fill-opacity="0.2" stroke="#D4AF37" stroke-width="1.5"/>
                    <path d="M109 61 v14 M102 68 h14" stroke="#D4AF37" stroke-width="1.8"/>
                    <text x="109" y="96" font-family="'Fraunces', Georgia, serif" font-weight="700" font-size="11" fill="#D4AF37" text-anchor="middle" letter-spacing="1">SERTIFIKAT</text>
                    <text x="109" y="108" font-family="'Instrument Sans', sans-serif" font-weight="700" font-size="8" fill="#FFFFFF" text-anchor="middle" letter-spacing="1">HAK MILIK (SHM)</text>
                    <text x="109" y="120" font-family="'IBM Plex Mono', monospace" font-size="6.5" fill="#E4EFEA" text-anchor="middle">BADAN PERTANAHAN</text>
                </g>
            </g>
            <!-- Rumah Modern -->
            <g class="anim-svg">
                <polygon points="215,55 165,92 265,92" fill="#D4AF37" stroke="#8F6910" stroke-width="2"/>
                <rect x="175" y="92" width="80" height="50" fill="#FFFFFF" stroke="#0F4C3A" stroke-width="2"/>
                <rect x="203" y="110" width="24" height="32" rx="2" fill="#0F4C3A"/>
                <circle cx="221" cy="126" r="2" fill="#D4AF37"/>
                <rect x="183" y="102" width="14" height="16" rx="1.5" fill="#E4EFEA" stroke="#0F4C3A" stroke-width="1.5"/>
                <rect x="233" y="102" width="14" height="16" rx="1.5" fill="#E4EFEA" stroke="#0F4C3A" stroke-width="1.5"/>
            </g>
        </svg>
        @break

    @case('berharga')
    @default
        <svg viewBox="0 0 320 180" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <defs>
                <linearGradient id="bondGrad-{{ $id ?? 'def' }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFFFFF"/>
                    <stop offset="100%" stop-color="#F4EFE6"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="90" r="75" fill="rgba(212, 175, 55, 0.12)"/>
            <!-- Lembar Dokumen Surat Berharga -->
            <g class="anim-svg">
                <rect x="85" y="32" width="150" height="115" rx="5" fill="#EAE3D2" stroke="#B8A995" stroke-width="1.5" transform="rotate(3 160 89)"/>
                <rect x="80" y="30" width="150" height="115" rx="5" fill="url(#bondGrad-{{ $id ?? 'def' }})" stroke="#0F4C3A" stroke-width="2"/>
                <rect x="88" y="38" width="134" height="99" rx="3" fill="none" stroke="#D4AF37" stroke-width="1.2" stroke-dasharray="4 2"/>
                <rect x="92" y="42" width="126" height="91" rx="2" fill="none" stroke="#0F4C3A" stroke-width="0.6"/>
                <text x="155" y="58" font-family="'Fraunces', Georgia, serif" font-weight="700" font-size="11" fill="#0F4C3A" text-anchor="middle" letter-spacing="1.5">SURAT BERHARGA</text>
                <text x="155" y="70" font-family="'Instrument Sans', sans-serif" font-weight="600" font-size="7" fill="#8F6910" text-anchor="middle" letter-spacing="1">OBLIGASI &amp; DEPOSITO RESMI</text>
                <line x1="105" y1="78" x2="205" y2="78" stroke="#0F4C3A" stroke-width="1.5" stroke-dasharray="4 2"/>
                <line x1="105" y1="88" x2="185" y2="88" stroke="rgba(15, 76, 58, 0.3)" stroke-width="1.5"/>
                <line x1="105" y1="96" x2="195" y2="96" stroke="rgba(15, 76, 58, 0.3)" stroke-width="1.5"/>
                <line x1="105" y1="104" x2="165" y2="104" stroke="rgba(15, 76, 58, 0.3)" stroke-width="1.5"/>
                <circle cx="205" cy="112" r="16" fill="#D4AF37" stroke="#8F6910" stroke-width="1.5"/>
                <circle cx="205" cy="112" r="12" fill="none" stroke="#5A4004" stroke-width="1" stroke-dasharray="2 2"/>
                <text x="205" y="117" font-family="'Fraunces', Georgia, serif" font-weight="700" font-size="12" fill="#0F4C3A" text-anchor="middle">L</text>
            </g>
        </svg>
        @break
@endswitch
