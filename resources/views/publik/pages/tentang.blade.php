@extends('layouts.publik')

@section('judul', 'Tentang Kami & Kontak Cabang — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Profil perusahaan PERSIDEN HP Titip Gadai sejak 2005: pimpinan, visi & misi, legalitas, serta informasi kontak lengkap 7 jaringan cabang di Makassar dan Gowa.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Tentang & Kontak' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Profil Perusahaan &amp; Layanan Kontak</p>
                <h1>Tentang PERSIDEN HP Titip Gadai</h1>
                <p class="pengantar">Mengenal lebih dekat PERSIDEN HP: Pelopor layanan titip gadai dan solusi dana tunai terpercaya sejak tahun 2005 di Kota Makassar dan Kabupaten Gowa. Komitmen <strong>Aman · Cepat · Terpercaya</strong> dengan jaringan 7 loket cabang strategis.</p>
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
                        <p>Lebih dari 20 tahun mendampingi masyarakat Sulawesi Selatan dalam transaksi jual beli handphone baru &amp; bekas serta layanan titip gadai elektronik, kendaraan, dan surat berharga.</p>
                    </div>
                </div>
                <div class="trust-ig-aksi">
                    <a href="https://www.instagram.com/persidenhp.official/" target="_blank" rel="noopener noreferrer" class="btn-ig-official">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>@persidenhp.official</span>
                    </a>
                </div>
            </div>

            <!-- 4 Navigasi Sub Menu Tentang Kami -->
            <div class="lembar-grid" style="margin-bottom: 3.5rem;">
                <a href="{{ route('publik.sambutan') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">01. Pimpinan</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Sambutan Pimpinan &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Komitmen manajemen dalam menghadirkan solusi penitipan barang yang aman, bersahabat, dan menenangkan bagi setiap penitip.
                    </p>
                </a>

                <a href="{{ route('publik.visi-misi') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">02. Fondasi</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Visi &amp; Misi &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Landasan nilai utama perusahaan: Aman, Cepat, dan Terpercaya dalam setiap langkah operasional loket cabang.
                    </p>
                </a>

                <a href="{{ route('publik.legalitas') }}" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">03. Kepatuhan</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Dokumen Legalitas &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Transparansi informasi badan usaha, kepatuhan hukum perdata dalam akad penitipan, dan integritas operasional.
                    </p>
                </a>

                <a href="#kontak-cabang" class="lembar panel-form" style="padding: 1.8rem; text-decoration: none; color: inherit; display: block;" data-muncul>
                    <div class="nomor" style="margin-bottom: .6rem; color: var(--kuningan-tua);">04. Lokasi &amp; Kontak</div>
                    <h2 style="font-size: 1.3rem; margin: 0 0 .5rem; color: var(--tinta);">Daftar 7 Cabang &rarr;</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        Alamat lengkap, peta Google Maps, dan jam operasional 08.00 – 22.00 WITA untuk seluruh cabang di Makassar dan Gowa.
                    </p>
                </a>
            </div>

            <!-- Ringkasan Sejarah & Nilai Layanan -->
            <div class="syarat-highlight-box" style="margin-bottom: 3.5rem; border-left-color: var(--kuningan);" data-muncul>
                <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: .75rem;">
                    <h2 style="font-size: 1.35rem; color: var(--tinta); margin: 0;">Sejarah Singkat &amp; Nilai yang Kami Junjung</h2>
                    <span style="font-size: .82rem; font-weight: 700; color: var(--kuningan-tua); background: var(--kuningan-muda); padding: .3rem .75rem; border-radius: 9999px;">
                        Telah Membantu Sejak 2005
                    </span>
                </div>
                <p style="font-size: .98rem; line-height: 1.7; color: var(--tinta-2); margin: 0 0 1rem;">
                    Berdiri sejak tahun 2005, PERSIDEN HP berawal dari dedikasi dalam penyediaan produk telekomunikasi dan gawai berkualitas di Kota Makassar. Berkembang bersama kepercayaan masyarakat, kami memperluas jangkauan layanan melalui unit usaha <strong>PERSIDEN HP Titip Gadai</strong> yang mengedepankan keamanan dan ketenangan pemilik barang.
                </p>
                <p style="font-size: .98rem; line-height: 1.7; color: var(--tinta-2); margin: 0 0 1.25rem;">
                    Setiap barang yang dititipkan disimpan rapi dalam tempat khusus bersegel tanpa dioperasikan selama masa penitipan. Untuk penitipan BPKB kendaraan, fisik kendaraan tetap digunakan oleh penitip demi mendukung mobilitas harian dan usaha. Seluruh perhitungan jasa penitipan disampaikan terbuka di muka tanpa biaya tersembunyi.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="{{ route('publik.cara-penitipan') }}" class="tombol tombol--garis" style="text-decoration: none; font-size: .9rem;">Pelajari Cara Penitipan</a>
                    <a href="#kontak-cabang" class="tombol tombol--emas" style="text-decoration: none; font-size: .9rem;">Lihat Kontak 7 Cabang &darr;</a>
                </div>
            </div>

            <!-- BAGIAN TERPADU: KONTAK & JARINGAN 7 CABANG -->
            <section id="kontak-cabang" style="scroll-margin-top: 90px; margin-bottom: 3.5rem;">
                <div class="halaman-header" style="text-align: left; margin-bottom: 2rem;">
                    <p class="kicker">Hubungi &amp; Kunjungi Kami</p>
                    <h2 style="font: 700 clamp(1.5rem, 3vw, 2.1rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .5rem;">Kontak Cabang &amp; Jam Layanan PERSIDEN HP</h2>
                    <p class="pengantar" style="margin: 0; max-width: 75ch;">Temukan lokasi loket cabang terdekat di wilayah Kota Makassar dan Kabupaten Gowa. Seluruh cabang kami melayani transaksi titip gadai, perpanjangan, dan konsultasi taksiran setiap hari mulai pukul <strong>08.00 s.d. 22.00 WITA</strong>.</p>
                </div>

                <!-- Jam Layanan Seragam Box -->
                <div class="syarat-highlight-box" style="margin-bottom: 2rem; border-left-color: var(--kuningan);" data-muncul>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <strong style="font-size: 1.15rem; display: block; margin-bottom: .25rem; color: var(--tinta);">Jam Operasional Seluruh Cabang</strong>
                            <p style="margin: 0; color: var(--muted); font-size: .95rem;">Buka setiap hari (Senin s.d. Minggu): <strong>08.00 – 22.00 WITA</strong></p>
                        </div>
                        <div style="display: flex; gap: .5rem; align-items: center; flex-wrap: wrap;">
                            <span style="font-weight: 600; font-size: .85rem; padding: .4rem .85rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px;">
                                7 Cabang Makassar &amp; Gowa
                            </span>
                            <a href="https://www.instagram.com/persidenhp.official/" target="_blank" rel="noopener noreferrer" style="font-size: .85rem; color: var(--navy-tua); font-weight: 600; text-decoration: none; padding: .4rem .85rem; background: rgba(0,38,101,.06); border-radius: 9999px;">
                                📸 @persidenhp.official
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter / Pencarian Cabang -->
                <div style="margin-bottom: 2rem;" data-muncul>
                    <label for="cariCabang" style="display: block; font-weight: 600; font-size: .9rem; margin-bottom: .5rem; color: var(--tinta);">Cari Cabang Terdekat:</label>
                    <input type="text" id="cariCabang" placeholder="Ketik nama cabang atau wilayah (cth: BTP, Antang, Gowa, Rappocini)..." 
                           style="width: 100%; max-width: 500px; padding: .75rem 1rem; border: 1.5px solid var(--garis); border-radius: var(--radius-m); font-size: .95rem; background: #fff;"
                           onkeyup="filterCabang()">
                </div>

                <!-- Grid 7 Cabang -->
                <div class="syarat-grid" id="gridCabang" style="margin-bottom: 3rem;">
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

                            <h3 style="font-size: 1.25rem; margin: .5rem 0 .5rem; color: var(--tinta);">{{ $c->nama_publik }}</h3>
                            
                            <p style="font-size: .9rem; line-height: 1.6; color: var(--muted); margin-bottom: 1rem; min-height: 48px;">
                                {{ $c->alamat }}
                            </p>

                            <div style="font-size: .85rem; line-height: 1.6; margin-bottom: 1.25rem; border-top: 1px dashed var(--garis); padding-top: .75rem;">
                                <div style="margin-bottom: .35rem;">
                                    <strong>Jam Layanan:</strong> {{ $c->jam_layanan ?? '08.00 - 22.00 WITA' }}
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
                                        <a href="https://www.instagram.com/{{ ltrim($c->instagram, '@') }}/" target="_blank" rel="noopener noreferrer" style="color: var(--kuningan-tua); font-weight: 600; text-decoration: none;">
                                            {{ '@' . ltrim($c->instagram, '@') }}
                                        </a>
                                    </div>
                                @else
                                    <div>
                                        <strong>Instagram Resmi:</strong>
                                        <a href="https://www.instagram.com/persidenhp.official/" target="_blank" rel="noopener noreferrer" style="color: var(--kuningan-tua); font-weight: 600; text-decoration: none;">
                                            @persidenhp.official
                                        </a>
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

                <!-- Peta Kantor Pusat / Makassar -->
                <div class="lembar panel-form" style="padding: 1.5rem; margin-bottom: 3rem;" data-muncul>
                    <h3 style="font-size: 1.15rem; margin: 0 0 .75rem; color: var(--tinta);">Peta Wilayah Layanan PERSIDEN HP Makassar</h3>
                    <div style="border-radius: var(--radius-m); overflow: hidden; border: 1px solid var(--garis);">
                        @if ($pusat && $pusat->lat !== null && $pusat->lng !== null)
                            <iframe title="Peta Lokasi Kantor Pusat PERSIDEN HP Makassar" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps?q={{ urlencode((float) $pusat->lat.','.(float) $pusat->lng) }}&amp;z=15&amp;output=embed"
                                    style="width: 100%; height: 380px; border: 0;"></iframe>
                        @else
                            <iframe title="Peta Wilayah Layanan PERSIDEN HP Makassar" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps?q=Makassar+Sulawesi+Selatan&amp;z=12&amp;output=embed"
                                    style="width: 100%; height: 380px; border: 0;"></iframe>
                        @endif
                    </div>
                </div>
            </section>

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
