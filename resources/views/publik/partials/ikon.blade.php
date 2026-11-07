{{-- Ikon garis per kategori barang. Variabel: $k --}}
<svg viewBox="0 0 48 48" aria-hidden="true">
    @switch($k)
        @case('smartphone')
            <rect x="14" y="5" width="20" height="38" rx="3.5"/><path d="M21 37h6"/>
            @break
        @case('laptop')
            <rect x="10" y="11" width="28" height="20" rx="2"/><path d="M5 36h38M19 36l1 2h8l1-2"/>
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
