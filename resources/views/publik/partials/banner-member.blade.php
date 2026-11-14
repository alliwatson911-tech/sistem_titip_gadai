<div class="wrap">
    <div class="banner-member" data-muncul>
        <div class="banner-member-wrap">
            <div class="banner-member-teks">
                <div class="banner-member-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    <span>Portal Penitip PERSIDEN HP</span>
                </div>
                <h2>Pantau Status Barang Titipan &amp; Nota Digital Secara Real-Time</h2>
                <p>Sebagai penitip di PERSIDEN HP, Anda dapat memantau status titipan barang, foto segel, nilai yang diterima, jasa penitipan, tanggal jatuh tempo, dan riwayat pembayaran langsung dari akun pribadi Anda.</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: .75rem; flex-shrink: 0;">
                <a class="btn btn--utama" href="{{ auth()->check() ? route('panel') : route('masuk') }}">
                    {{ auth()->check() ? 'Buka Panel Penitip' : 'Masuk ke Akun Penitip' }}
                </a>
                @guest
                    <span style="font-size: .84rem; opacity: .8; text-align: center; color: var(--kertas);">
                        Pendaftaran akun dilakukan di loket cabang
                    </span>
                @endguest
            </div>
        </div>
    </div>
</div>

