{{-- Ikon garis 24x24 (dekoratif). Pakai: @include('partials.ikon', ['n' => 'uang']) --}}
<svg class="ikon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($n)
    @case('beranda')<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>@break
    @case('anggota')<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5"/><circle cx="17" cy="9" r="2.4"/><path d="M16.5 14.6c2.6.2 4.5 1.9 4.5 4.4"/>@break
    @case('uang')<circle cx="12" cy="12" r="9"/><path d="M14.8 9.2c-.5-1-1.5-1.5-2.8-1.5-1.6 0-2.7.8-2.7 2 0 3 5.6 1.4 5.6 4.3 0 1.2-1.2 2-2.9 2-1.4 0-2.5-.6-3-1.7M12 6v1.7M12 16.3V18"/>@break
    @case('cair')<path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 19h16"/>@break
    @case('koin')<ellipse cx="12" cy="7" rx="7" ry="3"/><path d="M5 7v5c0 1.7 3.1 3 7 3s7-1.3 7-3V7"/><path d="M5 12v5c0 1.7 3.1 3 7 3s7-1.3 7-3v-5"/>@break
    @case('waspada')<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18v.2"/>@break
    @case('verifikasi')<circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/>@break
    @case('berkas')<path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 13h7M9 17h7"/>@break
    @case('jam')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
    @case('lelang')<path d="M14 4l6 6"/><path d="M12 6l6 6-4 4-6-6 4-4z"/><path d="M9 13l-5 5"/><path d="M3 21h9"/>@break
    @case('laporan')<path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/>@break
    @case('audit')<path d="M12 3l8 3v6c0 4.5-3.3 7.8-8 9-4.7-1.2-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>@break
    @case('gadai')<rect x="3" y="7" width="18" height="12" rx="2"/><path d="M8 7V5h8v2M3 12h18"/>@break
    @case('pengajuan')<path d="M5 4h14v16H5z"/><path d="M9 9h6M9 13h6M9 17h3"/>@break
    @case('pengumuman')<path d="M4 10v4h3l7 4V6L7 10H4z"/><path d="M18 9c1.3 1.3 1.3 4.7 0 6"/>@break
    @case('faq')<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 114 2c-1 .7-1.5 1.1-1.5 2.2M12 17v.2"/>@break
    @case('lokasi')<path d="M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>@break
    @case('situs')<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.5 5.5 3.5 9S14.5 18.5 12 21c-2.5-2.5-3.5-5.5-3.5-9S9.5 5.5 12 3z"/>@break
    @case('profil')<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>@break
    @case('pengguna')<circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.5 3.1-5.5 7-5.5s7 2 7 5.5"/><path d="M19 4l1 1.5L22 5"/>@break
    @case('kunci')<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 118 0v3"/>@break
    @case('cari')<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>@break
    @case('keluar')<path d="M10 4H5v16h5"/><path d="M15 8l4 4-4 4M19 12H9"/>@break
    @case('luar')<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v6H4V6h6"/>@break
    @default<circle cx="12" cy="12" r="3"/>
@endswitch
</svg>
