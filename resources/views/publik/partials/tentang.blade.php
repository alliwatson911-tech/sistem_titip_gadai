@php
    $wa = preg_replace('/\D/', '', (string) $pusat?->whatsapp);
    if ($wa !== '' && str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
    $namaSosmed = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'youtube' => 'YouTube'];
    $sosmed = collect($konten['sosmed'] ?? [])->filter(fn ($u) => is_string($u) && str_starts_with($u, 'https://'));
    $adaKontak = $pusat && ($pusat->alamat || $pusat->telepon || $pusat->whatsapp || $pusat->email || $pusat->jam_buka);
    $cabangOperasional = $cabang->where('kode', '!=', 'PUSAT');
@endphp
<section class="bagian bagian--gelap" id="tentang" data-spy="tentang" aria-labelledby="judul-tentang">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Tentang Kami</p>
            <h2 id="judul-tentang">PERSIDEN HP Titip Gadai Makassar</h2>
            <p class="pengantar" style="margin-top: .75rem; color: rgba(255,255,255,.8);">
                Layanan penitipan barang berharga dan surat berharga dengan komitmen <strong>Aman · Cepat · Terpercaya</strong> di 7 cabang Makassar &amp; Gowa.
            </p>
        </div>
        <div class="tentang">
            <div>
                <dl class="kontak">
                    <div>
                        <dt>Jam Layanan Seluruh Cabang</dt>
                        <dd><strong>08.00 &ndash; 22.00 WITA</strong> (Buka Setiap Hari)</dd>
                    </div>
                    <div>
                        <dt>Kantor Pusat</dt>
                        <dd>{{ $pusat?->alamat ?? 'Makassar, Sulawesi Selatan' }}</dd>
                    </div>
                    @if ($wa !== '')
                        <div>
                            <dt>WhatsApp Kantor Pusat</dt>
                            <dd><a href="https://wa.me/{{ $wa }}" rel="noopener" style="color: var(--kuningan);">{{ $pusat->whatsapp }}</a></dd>
                        </div>
                    @endif
                    @if ($pusat?->telepon)
                        <div>
                            <dt>Telepon</dt>
                            <dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $pusat->telepon) }}" style="color: var(--kuningan);">{{ $pusat->telepon }}</a></dd>
                        </div>
                    @endif
                    @if ($pusat?->email)
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:{{ $pusat->email }}" style="color: var(--kuningan);">{{ $pusat->email }}</a></dd>
                        </div>
                    @endif
                </dl>

                @if ($cabangOperasional->isNotEmpty())
                    <div style="margin: 1.5rem 0 1rem;">
                        <b style="color: #fff; font-size: 1rem; display: block; margin-bottom: .75rem;">Jaringan 7 Cabang:</b>
                        <div class="cabang-daftar" style="display: grid; gap: .75rem;">
                            @foreach ($cabangOperasional as $c)
                                <div style="background: rgba(255,255,255,.06); padding: .75rem 1rem; border-radius: var(--radius-m); border: 1px solid rgba(255,255,255,.1);">
                                    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: .5rem; flex-wrap: wrap;">
                                        <b style="color: var(--kuningan);">{{ $c->nama_publik }}</b>
                                        <span style="font-size: .8rem; color: rgba(255,255,255,.7);">08.00 – 22.00 WITA</span>
                                    </div>
                                    <p style="margin: .35rem 0 .5rem; font-size: .85rem; color: rgba(255,255,255,.8); line-height: 1.5;">
                                        {{ $c->alamat }}
                                    </p>
                                    @if($c->gmaps_link)
                                        <a href="{{ $c->gmaps_link }}" target="_blank" rel="noopener noreferrer" style="font-size: .8rem; color: var(--kuningan); text-decoration: none;">
                                            📍 Buka di Google Maps &rarr;
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($sosmed->isNotEmpty())
                    <div class="sosmed" style="margin-top: 1.25rem; display: flex; gap: .75rem; flex-wrap: wrap;">
                        @foreach ($sosmed as $kunci => $url)
                            <a href="{{ $url }}" rel="noopener noreferrer" target="_blank" style="color: var(--kuningan); font-size: .88rem; text-decoration: underline;">
                                {{ $namaSosmed[$kunci] ?? ucfirst($kunci) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="izin" style="margin-top: 1.25rem; font-size: .85rem; opacity: .85; line-height: 1.5;">
                    <b style="color: #fff; display: block; margin-bottom: .25rem;">Informasi Perizinan &amp; Legalitas:</b>
                    @if (! empty($konten['izin_usaha']))
                        {{ $konten['izin_usaha'] }}
                    @else
                        Informasi perizinan resmi akan ditampilkan di sini setelah dilengkapi dan diverifikasi oleh pengelola.
                    @endif
                </div>

                <div style="display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem;">
                    <a href="{{ route('publik.sambutan') }}" class="btn btn--kecil" style="background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3);">Sambutan Pimpinan</a>
                    <a href="{{ route('publik.visi-misi') }}" class="btn btn--kecil" style="background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3);">Visi &amp; Misi</a>
                    <a href="{{ route('publik.legalitas') }}" class="btn btn--kecil" style="background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3);">Legalitas</a>
                    <a href="{{ route('publik.cabang') }}" class="btn btn--kecil btn--utama">Daftar Cabang</a>
                </div>
            </div>

            <div>
                <!-- Peta Kantor Pusat / 7 Cabang Makassar -->
                <div class="peta" style="border-radius: var(--radius-m); overflow: hidden; border: 1px solid rgba(255,255,255,.2);">
                    @if ($pusat && $pusat->lat !== null && $pusat->lng !== null)
                        <iframe title="Peta Lokasi Kantor Pusat PERSIDEN HP Makassar" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ urlencode((float) $pusat->lat.','.(float) $pusat->lng) }}&amp;z=15&amp;output=embed"
                                style="width: 100%; height: 380px; border: 0;"></iframe>
                    @else
                        <iframe title="Peta Lokasi Cabang PERSIDEN HP Makassar" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q=Makassar+Sulawesi+Selatan&amp;z=12&amp;output=embed"
                                style="width: 100%; height: 380px; border: 0;"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
