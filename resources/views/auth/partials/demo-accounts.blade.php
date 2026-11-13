@php
    $emailTarget = $emailId ?? 'email';
    $passwordTarget = $passwordId ?? 'password';
    $defaultPassword = 'LinyGadai2026!';
    $uid = substr(md5($emailTarget . $passwordTarget), 0, 6);

    $akunDemo = [
        [
            'role' => 'ADMIN',
            'judul' => 'Admin / Pengurus',
            'email' => 'admin.a@linygadai.test',
            'dot' => '#EF4444',
            'badge_bg' => '#D4AF37',
            'badge_color' => '#143D31',
            'badge_border' => 'none',
        ],
        [
            'role' => 'MANAGER',
            'judul' => 'Manajer Operasional',
            'email' => 'manager.a@linygadai.test',
            'dot' => '#F59E0B',
            'badge_bg' => '#0F3254',
            'badge_color' => '#FFFFFF',
            'badge_border' => 'none',
        ],
        [
            'role' => 'ANALYST',
            'judul' => 'Analis Pembiayaan',
            'email' => 'analis.a@linygadai.test',
            'dot' => '#6366F1',
            'badge_bg' => '#F0F9FF',
            'badge_color' => '#0284C7',
            'badge_border' => '1.5px solid #BAE6FD',
        ],
        [
            'role' => 'TELLER',
            'judul' => 'Teller / Petugas Kas',
            'email' => 'teller.a@linygadai.test',
            'dot' => '#10B981',
            'badge_bg' => '#ECFDF5',
            'badge_color' => '#047857',
            'badge_border' => '1.5px solid #A7F3D0',
        ],
        [
            'role' => 'MEMBER',
            'judul' => 'Budi Santoso (Anggota)',
            'email' => 'anggota.a@linygadai.test',
            'dot' => '#0EA5E9',
            'badge_bg' => '#F0FDFA',
            'badge_color' => '#0F766E',
            'badge_border' => '1.5px solid #99F6E4',
        ],
        [
            'role' => 'OWNER',
            'judul' => 'Owner / Pemilik',
            'email' => 'owner@linygadai.test',
            'dot' => '#D4AF37',
            'badge_bg' => '#0F4C3A',
            'badge_color' => '#F8F5F0',
            'badge_border' => '1.5px solid #D4AF37',
        ],
    ];
@endphp

<div class="demo-akun-box" id="demo-box-{{ $uid }}">
    <div class="demo-akun-header">
        <div class="demo-akun-header-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--kuningan-tua);" aria-hidden="true">
                <path d="M21 2l-2 2m-1.5 1.5L16 7l-1.5-1.5L13 7l-1.5-1.5L10 7 8.5 5.5 7 7l-1.5-1.5L4 7 2 5v14l3-3 2 2 2-2 2 2 2-2 2 2 2-2 2 2 3-3V2z"/>
                <circle cx="8" cy="14" r="2"/>
                <path d="M10 14h10"/>
                <path d="M16 11v6"/>
            </svg>
            <span>PILIH AKUN DEMO UNTUK UJI COBA:</span>
        </div>
        <div class="demo-akun-pw-pill" title="Kata sandi standar seluruh akun demo">
            {{ $defaultPassword }}
        </div>
    </div>

    <div class="demo-akun-list" role="list" aria-label="Daftar akun demo untuk uji coba">
        @foreach ($akunDemo as $ak)
            <div class="demo-akun-card"
                 role="button"
                 tabindex="0"
                 data-email="{{ $ak['email'] }}"
                 data-password="{{ $defaultPassword }}"
                 data-role="{{ $ak['role'] }}"
                 data-target-email="{{ $emailTarget }}"
                 data-target-password="{{ $passwordTarget }}"
                 data-box-uid="{{ $uid }}"
                 title="Klik untuk memilih akun {{ $ak['judul'] }}">
                <div class="demo-akun-left">
                    <span class="demo-akun-dot" style="background-color: {{ $ak['dot'] }};" aria-hidden="true"></span>
                    <div class="demo-akun-info">
                        <div class="demo-akun-nama">{{ $ak['judul'] }}</div>
                        <div class="demo-akun-email">{{ $ak['email'] }}</div>
                    </div>
                </div>
                <div class="demo-akun-badge" style="background-color: {{ $ak['badge_bg'] }}; color: {{ $ak['badge_color'] }}; border: {{ $ak['badge_border'] }};">
                    {{ $ak['role'] }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="demo-akun-status" id="demo-status-{{ $uid }}" role="status" aria-live="polite"></div>
</div>

