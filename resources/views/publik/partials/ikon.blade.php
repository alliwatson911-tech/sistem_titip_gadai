{{-- Ikon garis orisinal per kategori barang & agunan. Variabel: $k --}}
<svg class="ikon-agunan" width="48" height="48" viewBox="0 0 48 48" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    @switch($k)
        @case('smartphone')
            <rect x="14" y="5" width="20" height="38" rx="3.5"/><path d="M21 37h6"/><circle cx="24" cy="9" r="1"/>
            @break
        @case('laptop')
            <rect x="9" y="11" width="30" height="20" rx="2"/><path d="M4 36h40M19 36l1.5 2h7l1.5-2"/>
            @break
        @case('bpkb-mobil')
            <!-- Mobil & Dokumen BPKB -->
            <path d="M7 26l3-8h22l4 8M5 26h32a2 2 0 0 1 2 2v6H5v-6a2 2 0 0 1 2-2z"/>
            <circle cx="11" cy="34" r="3"/><circle cx="31" cy="34" r="3"/>
            <rect x="26" y="6" width="16" height="18" rx="2" stroke-dasharray="2 2"/>
            <path d="M30 11h8M30 15h5"/>
            @break
        @case('bpkb-motor')
            <!-- Sepeda Motor & BPKB -->
            <circle cx="11" cy="33" r="5"/><circle cx="37" cy="33" r="5"/>
            <path d="M11 33l7-12h8l5 12M18 21l3-7h5M23 14h5"/>
            <rect x="25" y="5" width="15" height="13" rx="1.5" stroke-dasharray="2 2"/>
            <path d="M28 9h9M28 12h6"/>
            @break
        @case('sertifikat-rumah')
            <!-- Rumah & Sertifikat Bersegel -->
            <path d="M6 22L20 9l10 9v18H6zM15 36V26h8v10"/>
            <rect x="24" y="8" width="18" height="26" rx="2" fill="var(--kertas, #F4EFE6)"/>
            <path d="M28 14h10M28 18h10M28 22h6"/>
            <circle cx="33" cy="28" r="3" fill="var(--kuningan, #B8862B)"/>
            @break
        @case('sertifikat-tanah')
            <!-- Kavling Tanah & Sertifikat SHM -->
            <path d="M6 34l12-8 14 6 10-6v8l-10 6-14-6-12 8z"/>
            <rect x="14" y="6" width="20" height="26" rx="2" fill="var(--kertas, #F4EFE6)"/>
            <path d="M18 12h12M18 16h12M18 20h8"/>
            <circle cx="24" cy="26" r="3" fill="var(--kuningan, #B8862B)"/>
            @break
        @case('lainnya')
            <!-- Logam Mulia / Emas Batangan & Surat Berharga -->
            <path d="M8 26l7-14h18l7 14-7 14H15z"/>
            <path d="M15 12l9 28M33 12L24 40M8 26h32"/>
            @break
        @case('kamera')
            <rect x="5" y="15" width="38" height="24" rx="3"/><circle cx="24" cy="27" r="7"/><path d="M15 15l3-6h12l3 6"/>
            @break
        @case('konsol')
            <rect x="6" y="17" width="36" height="16" rx="8"/><path d="M15 25h6M18 22v6"/><circle cx="31" cy="23.5" r="1.6"/><circle cx="35" cy="27" r="1.6"/>
            @break
        @case('televisi')
            <rect x="5" y="9" width="38" height="25" rx="2"/><path d="M15 41h18M24 34v7"/>
            @break
        @case('smartwatch')
            <rect x="13" y="14" width="22" height="20" rx="6"/><path d="M18 14l1.5-9h9L30 14M18 34l1.5 9h9L30 34"/>
            @break
        @default
            <rect x="8" y="8" width="32" height="32" rx="4"/>
    @endswitch
</svg>
