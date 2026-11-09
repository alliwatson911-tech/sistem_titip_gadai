<div class="wrap">
    <div class="banner-member" data-muncul>
        <div class="banner-member-wrap">
            <div class="banner-member-teks">
                <div class="banner-member-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    <span>Layanan Anggota Online</span>
                </div>
                <h2>Daftar Anggota, Pantau Status Gadai & Nota Digital dari Rumah</h2>
                <p>Sebagai anggota linygadai, Anda dapat mengajukan taksiran awal secara online, memantau masa simpan & jatuh tempo, serta melakukan verifikasi pembayaran loket secara transparan.</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: .75rem; flex-shrink: 0;">
                <a class="btn btn--utama" href="{{ auth()->check() ? route('panel') : '#akun' }}">
                    {{ auth()->check() ? 'Buka Panel Anggota' : 'Daftar Akun Anggota' }}
                </a>
                @guest
                    <a class="btn btn--gelap btn--kecil" style="border-color: rgba(244, 239, 230, .4); text-align: center;" href="#akun">
                        Sudah punya akun? Masuk
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

