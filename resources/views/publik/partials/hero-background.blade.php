{{-- Background penuh layar khusus hero utama (linygadai) --}}
<div class="hero-bg-canvas" aria-hidden="true">
    {{-- 1. Lapisan Gradasi Dasar: Hangat Khas Kertas Perkamen & Gading --}}
    <div class="hero-bg-gradient"></div>

    {{-- 2. Bidang Lengkung Organik Kanan (Tinta & Lumut) & Pola Titik (Dot Matrix) --}}
    <svg class="hero-bg-shapes" viewBox="0 0 1440 850" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            {{-- Gradasi Bidang Kanan: Paduan Tinta (#1B2A2F) dan Lumut (#243E33) --}}
            <linearGradient id="grad-wave-utama" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#1B2A2F" stop-opacity="0.94"/>
                <stop offset="50%" stop-color="#223B30" stop-opacity="0.96"/>
                <stop offset="100%" stop-color="#162226" stop-opacity="0.98"/>
            </linearGradient>

            {{-- Gradasi Aksen Kuningan Lembut --}}
            <linearGradient id="grad-kuningan-glow" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#B8862B" stop-opacity="0.4"/>
                <stop offset="60%" stop-color="#7F5A0E" stop-opacity="0.12"/>
                <stop offset="100%" stop-color="#B8862B" stop-opacity="0"/>
            </linearGradient>

            {{-- Pola Titik-Titik (Dot Grid Matrix) Warna Kuningan --}}
            <pattern id="pola-titik-hero" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="1.6" fill="#B8862B" fill-opacity="0.3"/>
            </pattern>

            {{-- Pola Garis Halus Arsiran Kertas --}}
            <pattern id="pola-arsir-hero" x="0" y="0" width="36" height="36" patternTransform="rotate(45)" patternUnits="userSpaceOnUse">
                <line x1="0" y1="0" x2="0" y2="36" stroke="#B8862B" stroke-width="0.75" stroke-opacity="0.08"/>
            </pattern>
        </defs>

        {{-- Bidang Arsiran di Latar Belakang --}}
        <rect x="720" y="0" width="720" height="500" fill="url(#pola-arsir-hero)"/>

        {{-- Bidang Lengkung Aksen Kuningan Belakang --}}
        <path d="M720,0 C880,140 820,410 1020,580 C1180,720 1320,770 1440,850 L1440,0 Z" fill="url(#grad-kuningan-glow)"/>

        {{-- Bidang Lengkung Utama (Tinta & Lumut Khas LinyGadai) --}}
        <path d="M780,0 C930,120 890,390 1070,540 C1220,670 1340,730 1440,810 L1440,0 Z" fill="url(#grad-wave-utama)"/>

        {{-- Area Pola Titik (Dot Grid Matrix) --}}
        <rect x="880" y="40" width="480" height="280" fill="url(#pola-titik-hero)" opacity="0.85"/>

        {{-- Lingkaran Aksen Halus di Latar Belakang Kanan --}}
        <circle cx="1260" cy="180" r="130" fill="none" stroke="#B8862B" stroke-width="1.5" stroke-opacity="0.22" stroke-dasharray="6 6"/>
        <circle cx="1260" cy="180" r="175" fill="none" stroke="#F4EFE6" stroke-width="1" stroke-opacity="0.1"/>
    </svg>

    {{-- 3. Wadah Dekorasi Emblem & Kartu Mengambang yang Sejajar dengan .wrap --}}
    <div class="hero-bg-decor-wrap">
        {{-- Emblem 1: Logam Mulia / Emas Batangan 999.9 (Kanan Atas Tag) --}}
        <div class="hero-floating-emblem emblem-emas" title="Jaminan Emas & Logam Mulia">
            <svg class="emblem-svg" viewBox="0 0 120 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad-emas-batang" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FCE68A"/>
                        <stop offset="35%" stop-color="#EAB308"/>
                        <stop offset="70%" stop-color="#CA8A04"/>
                        <stop offset="100%" stop-color="#854D0E"/>
                    </linearGradient>
                    <linearGradient id="grad-emas-kilau" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.85"/>
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="26,82 86,96 102,68 42,54" fill="#0E191C" opacity="0.45"/>
                <polygon points="24,32 74,20 98,54 48,66" fill="#FDE047"/>
                <polygon points="48,66 98,54 94,76 44,88" fill="#CA8A04"/>
                <polygon points="24,32 48,66 44,88 20,54" fill="#EAB308"/>
                <polygon points="28,34 70,24 76,32 34,42" fill="url(#grad-emas-kilau)"/>
                <text x="56" y="44" font-family="'IBM Plex Mono', monospace" font-size="7.5" font-weight="700" fill="#713F12" letter-spacing="1" transform="rotate(13 56 44)">FINE GOLD</text>
                <text x="58" y="53" font-family="'IBM Plex Mono', monospace" font-size="8.5" font-weight="800" fill="#713F12" letter-spacing="1.2" transform="rotate(13 58 53)">999.9</text>
                <text x="64" y="60" font-family="'IBM Plex Mono', monospace" font-size="6" font-weight="600" fill="#854D0E" transform="rotate(13 64 60)">CERTIFIED</text>
            </svg>
            <div class="emblem-label">
                <b>Logam Mulia</b>
                <span>Kadar 999.9 Resmi</span>
            </div>
        </div>

        {{-- Emblem 2: Elektronik & Gadget Terlindungi (Kanan Bawah Tag) --}}
        <div class="hero-floating-emblem emblem-gadget" title="Gadai Gadget Elektronik Aman">
            <svg class="emblem-svg" viewBox="0 0 110 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad-gadget-layar" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38BDF8"/>
                        <stop offset="60%" stop-color="#0284C7"/>
                        <stop offset="100%" stop-color="#0369A1"/>
                    </linearGradient>
                </defs>
                <rect x="22" y="32" width="66" height="42" rx="4" fill="#0F172A" stroke="#475569" stroke-width="1.5"/>
                <rect x="26" y="36" width="58" height="34" rx="2" fill="url(#grad-gadget-layar)"/>
                <polygon points="14,74 96,74 90,80 20,80" fill="#64748B"/>
                <rect x="66" y="42" width="28" height="44" rx="5" fill="#1E293B" stroke="#B8862B" stroke-width="1.5"/>
                <rect x="69" y="45" width="22" height="35" rx="3" fill="#0F172A"/>
                <circle cx="80" cy="62" r="7" fill="#10B981"/>
                <path d="M77 62l2 2 4-4" stroke="#FFFFFF" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="emblem-label">
                <b>Gadget & Laptop</b>
                <span>Taksiran Tertinggi</span>
            </div>
        </div>

        {{-- Kartu Info Mengambang 1: Taksir Kilat 15 Menit (Kiri Atas Tag) --}}
        <div class="hero-floating-badge badge-cair">
            <div class="badge-icon-wrap icon-hijau">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="badge-text-wrap">
                <b>Cair 15 Menit</b>
                <span>Langsung tunai / transfer</span>
            </div>
        </div>

        {{-- Kartu Info Mengambang 2: Brankas Bersegel Resmi (Kiri Bawah Tag) --}}
        <div class="hero-floating-badge badge-brankas">
            <div class="badge-icon-wrap icon-kuningan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="badge-text-wrap">
                <b>Brankas Bersegel</b>
                <span>Terjamin aman 100%</span>
            </div>
        </div>
    </div>
</div>
