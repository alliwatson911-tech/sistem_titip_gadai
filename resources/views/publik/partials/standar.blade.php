@php $standar = $konten['standar_layanan'] ?? []; @endphp
@if ($standar)
<section class="bagian" id="standar" data-spy="beranda" aria-labelledby="judul-standar">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Standar Layanan</p>
            <div>
                <h2 id="judul-standar">Keunggulan & Standar Transaksi linygadai</h2>
                <p class="pengantar" style="margin-top:1rem">Enam prinsip layanan yang selalu kami pegang teguh untuk menjaga keamanan barang elektronik dan kenyamanan setiap anggota.</p>
            </div>
        </div>

        <div class="fitur-grid">
            @foreach ($standar as $idx => $s)
                <article class="kartu-fitur" data-muncul>
                    <div class="fitur-icon-box" aria-hidden="true">
                        @if ($idx === 0)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        @elseif ($idx === 1)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        @elseif ($idx === 2)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        @elseif ($idx === 3)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        @elseif ($idx === 4)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        @endif
                    </div>
                    <h3>{{ $s['judul'] }}</h3>
                    <p>{{ $isi($s['isi']) }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
