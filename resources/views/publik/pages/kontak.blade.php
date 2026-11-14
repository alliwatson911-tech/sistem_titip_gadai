@extends('layouts.publik')

@section('judul', 'Kontak & Lokasi Cabang — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Hubungi cabang PERSIDEN HP Titip Gadai di Makassar dan Gowa. Jam operasional buka setiap hari pukul 08.00 – 22.00 WITA.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Kontak' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Hubungi &amp; Profil Perusahaan</p>
                <h1>Kontak Cabang PERSIDEN HP</h1>
                <p class="pengantar">Temukan lokasi loket cabang terdekat di wilayah Kota Makassar dan Kabupaten Gowa. Melayani masyarakat sejak tahun 2005 dengan transaksi titip gadai dan konsultasi taksiran setiap hari mulai pukul <strong>08.00 s.d. 22.00 WITA</strong>.</p>
            </div>

            <!-- Banner Terpercaya Sejak 2005 & Akun Instagram Resmi -->
            <div class="trust-ig-banner" data-muncul>
                <div class="trust-ig-konten">
                    <div class="trust-ig-badge-star">
                        <span class="star-text">TRUSTED</span>
                        <span class="star-icons">★★★★★</span>
                        <span class="star-year">SEJAK 2005</span>
                    </div>
                    <div class="trust-ig-deskripsi">
                        <strong>PERSIDEN HP · Terpercaya Sejak Tahun 2005</strong>
                        <p>Lebih dari 20 tahun mendampingi masyarakat Makassar dalam layanan jual beli gawai &amp; titip gadai terpercaya. Ikuti informasi resmi, update promo, dan katalog kami di Instagram.</p>
                    </div>
                </div>
                <div class="trust-ig-aksi">
                    <a href="https://www.instagram.com/persidenhp.official/" target="_blank" rel="noopener noreferrer" class="btn-ig-official">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>@persidenhp.official</span>
                    </a>
                </div>
            </div>

            <!-- Jam Layanan Seragam Box -->
            <div class="syarat-highlight-box" style="margin-bottom: 2.5rem; border-left-color: var(--kuningan);" data-muncul>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <h2 style="font-size: 1.2rem; margin: 0 0 .25rem; color: var(--tinta);">Jam Operasional Seluruh Cabang</h2>
                        <p style="margin: 0; color: var(--muted); font-size: .95rem;">Buka setiap hari (Senin s.d. Minggu): <strong>08.00 – 22.00 WITA</strong></p>
                    </div>
                    <span style="font-weight: 600; font-size: .85rem; padding: .4rem .85rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px;">
                        7 Cabang Makassar &amp; Gowa
                    </span>
                </div>
            </div>

            <!-- Filter / Pencarian Cabang -->
            <div style="margin-bottom: 2rem;">
                <label for="cariCabang" style="display: block; font-weight: 600; font-size: .9rem; margin-bottom: .5rem; color: var(--tinta);">Cari Cabang Terdekat:</label>
                <input type="text" id="cariCabang" placeholder="Ketik nama cabang atau wilayah (cth: BTP, Antang, Gowa, Rappocini)..." 
                       style="width: 100%; max-width: 500px; padding: .75rem 1rem; border: 1.5px solid var(--garis); border-radius: var(--radius-m); font-size: .95rem; background: #fff;"
                       onkeyup="filterCabang()">
            </div>

            <!-- Grid 7 Cabang -->
            <div class="syarat-grid" id="gridCabang" style="margin-bottom: 3.5rem;">
                @php
                    $daftarCabang = $cabang->where('kode', '!=', 'PUSAT');
                @endphp

                @foreach($daftarCabang as $c)
                    @php
                        $waClean = preg_replace('/\D/', '', (string) $c->whatsapp);
                        if ($waClean !== '' && str_starts_with($waClean, '0')) {
                            $waClean = '62' . substr($waClean, 1);
                        }
                    @endphp
                    <article class="syarat-card item-cabang" data-nama="{{ strtolower($c->nama . ' ' . $c->alamat) }}" data-muncul>
                        <div class="syarat-card-top" style="justify-content: space-between; align-items: flex-start;">
                            <span class="syarat-card-num" style="background: var(--navy-tua, #002665); color: #fff;">{{ $c->kode }}</span>
                            @if($c->is_verified)
                                <span style="font-size: .75rem; background: rgba(30,90,60,.12); color: #1e5a3c; padding: .25rem .6rem; border-radius: 9999px; font-weight: 600;">Terverifikasi</span>
                            @else
                                <span style="font-size: .75rem; background: rgba(184,134,43,.15); color: #8a6400; padding: .25rem .6rem; border-radius: 9999px; font-weight: 600;" title="Menunggu verifikasi resmi klien">[Menunggu Konfirmasi]</span>
                            @endif
                        </div>

                        <h2 style="font-size: 1.25rem; margin: .5rem 0 .5rem; color: var(--tinta);">{{ $c->nama_publik }}</h2>
                        
                        <p style="font-size: .9rem; line-height: 1.6; color: var(--muted); margin-bottom: 1rem; min-height: 48px;">
                            {{ $c->alamat }}
                        </p>

                        <div style="font-size: .85rem; line-height: 1.6; margin-bottom: 1.25rem; border-top: 1px dashed var(--garis); padding-top: .75rem;">
                            <div style="margin-bottom: .35rem;">
                                <strong>Jam Layanan:</strong> {{ $c->jam_layanan }}
                            </div>

                            <div style="margin-bottom: .35rem;">
                                <strong>WhatsApp / Telp:</strong>
                                @if($c->is_verified && $c->whatsapp)
                                    <a href="https://wa.me/{{ $waClean }}" target="_blank" rel="noopener noreferrer" style="color: var(--kuningan-tua); font-weight: 600; text-decoration: none;">
                                        {{ $c->whatsapp }}
                                    </a>
                                @else
                                    <span style="color: var(--muted); font-style: italic;">[Menunggu Konfirmasi Klien]</span>
                                @endif
                            </div>

                            @if($c->instagram)
                                <div>
                                    <strong>Instagram:</strong>
                                    @if($c->is_verified)
                                        <a href="https://www.instagram.com/{{ ltrim($c->instagram, '@') }}/" target="_blank" rel="noopener noreferrer" style="color: var(--kuningan-tua); font-weight: 600; text-decoration: none;">
                                            {{ '@' . ltrim($c->instagram, '@') }}
                                        </a>
                                    @else
                                        <span style="color: var(--muted); font-style: italic;">[Menunggu Konfirmasi Klien]</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div style="display: flex; gap: .5rem; flex-wrap: wrap;">
                            @if($c->gmaps_link)
                                <a href="{{ $c->gmaps_link }}" target="_blank" rel="noopener noreferrer" class="tombol tombol--garis" style="padding: .5rem .85rem; font-size: .85rem; text-decoration: none; flex: 1; text-align: center;">
                                    📍 Buka di Google Maps
                                </a>
                            @endif
                            @if($c->is_verified && $waClean)
                                <a href="https://wa.me/{{ $waClean }}" target="_blank" rel="noopener noreferrer" class="tombol tombol--emas" style="padding: .5rem .85rem; font-size: .85rem; text-decoration: none; text-align: center;">
                                    Chat WA
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Navigasi Terpadu ke Profil & Tentang Kami -->
            <div class="syarat-highlight-box" style="margin-bottom: 3rem; border-left-color: var(--kuningan);" data-muncul>
                <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: .75rem;">
                    <h2 style="font-size: 1.25rem; color: var(--tinta); margin: 0;">Mengenal PERSIDEN HP Lebih Dekat</h2>
                    <span style="font-size: .82rem; font-weight: 700; color: var(--kuningan-tua); background: var(--kuningan-muda); padding: .3rem .75rem; border-radius: 9999px;">
                        Profil &amp; Legalitas
                    </span>
                </div>
                <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin: 0 0 1.25rem;">
                    PERSIDEN HP beroperasi sejak tahun 2005 dan terus berkomitmen memberikan layanan titip gadai yang menenangkan, berpegang pada nilai Aman, Cepat, dan Terpercaya bagi seluruh penitip.
                </p>
                <div style="display: flex; gap: .75rem; flex-wrap: wrap;">
                    <a href="{{ route('publik.tentang') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .88rem;">Profil Lengkap Perusahaan &rarr;</a>
                    <a href="{{ route('publik.sambutan') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .88rem;">Sambutan Pimpinan</a>
                    <a href="{{ route('publik.visi-misi') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .88rem;">Visi &amp; Misi</a>
                    <a href="{{ route('publik.legalitas') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .88rem;">Legalitas</a>
                </div>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>

    <script>
        function filterCabang() {
            var input = document.getElementById('cariCabang').value.toLowerCase();
            var items = document.querySelectorAll('.item-cabang');
            items.forEach(function(item) {
                var nama = item.getAttribute('data-nama') || '';
                if (nama.indexOf(input) > -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
    @include('publik.partials.kaki')
@endsection

