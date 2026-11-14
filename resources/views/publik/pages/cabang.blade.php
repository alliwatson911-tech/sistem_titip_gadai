@extends('layouts.publik')

@section('judul', 'Daftar Cabang — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Daftar lengkap 7 cabang PERSIDEN HP Titip Gadai di Makassar dan Gowa. Buka setiap hari pukul 08.00 – 22.00 WITA.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Tentang Kami" :backUrl="route('publik.tentang')" :crumbs="['Tentang Kami' => route('publik.tentang'), 'Daftar Cabang' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Jaringan Pelayanan</p>
                <h1>Daftar 7 Cabang PERSIDEN HP</h1>
                <p class="pengantar">Kunjungi loket cabang terdekat kami di area Makassar dan Gowa. Seluruh cabang melayani penaksiran dan transaksi titip gadai setiap hari mulai pukul <strong>08.00 – 22.00 WITA</strong>.</p>
            </div>

            <!-- Jam Layanan Seragam Box -->
            <div class="syarat-highlight-box" style="margin-bottom: 2.5rem; border-left-color: var(--kuningan);" data-muncul>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <h2 style="font-size: 1.2rem; margin: 0 0 .25rem; color: var(--tinta);">Jam Operasional Seragam</h2>
                        <p style="margin: 0; color: var(--muted); font-size: .95rem;">Seluruh 7 cabang buka serempak: <strong>08.00 – 22.00 WITA</strong> (Senin s.d. Minggu).</p>
                    </div>
                    <span style="font-weight: 600; font-size: .85rem; padding: .4rem .85rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px;">
                        Makassar &amp; Gowa
                    </span>
                </div>
            </div>

            <!-- Pencarian Cabang -->
            <div style="margin-bottom: 2rem;">
                <label for="cariCabang" style="display: block; font-weight: 600; font-size: .9rem; margin-bottom: .5rem; color: var(--tinta);">Temukan Cabang di Sekitar Anda:</label>
                <input type="text" id="cariCabang" placeholder="Cari berdasarkan nama jalan, kecamatan, atau wilayah..." 
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

