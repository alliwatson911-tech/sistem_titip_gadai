@php
    $wa = preg_replace('/\D/', '', (string) $pusat?->whatsapp);
    if ($wa !== '' && str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
    $namaSosmed = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'youtube' => 'YouTube'];
    $sosmed = collect($konten['sosmed'] ?? [])->filter(fn ($u) => is_string($u) && str_starts_with($u, 'https://'));
    $adaKontak = $pusat && ($pusat->alamat || $pusat->telepon || $pusat->whatsapp || $pusat->email || $pusat->jam_buka);
    $punyaPeta = $pusat && $pusat->lat !== null && $pusat->lng !== null;
    $cabangOperasional = $cabang->except('PUSAT')->filter(fn ($c) => $c->alamat || $c->telepon);
@endphp
<section class="bagian bagian--gelap" id="tentang" data-spy="tentang" aria-labelledby="judul-tentang">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Tentang kami</p>
            <h2 id="judul-tentang">Temui kami langsung, tanyakan apa saja</h2>
        </div>
        <div class="tentang">
            <div>
                @if ($adaKontak)
                <dl class="kontak">
                    @if ($wa !== '')<div><dt>WhatsApp admin</dt><dd><a href="https://wa.me/{{ $wa }}" rel="noopener">{{ $pusat->whatsapp }}</a></dd></div>@endif
                    @if ($pusat->telepon)<div><dt>Telepon</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $pusat->telepon) }}">{{ $pusat->telepon }}</a></dd></div>@endif
                    @if ($pusat->email)<div><dt>Email</dt><dd><a href="mailto:{{ $pusat->email }}">{{ $pusat->email }}</a></dd></div>@endif
                    @if ($pusat->alamat)<div><dt>Kantor pusat</dt><dd>{{ $pusat->alamat }}</dd></div>@endif
                    @if ($pusat->jam_buka)<div><dt>Jam layanan</dt><dd>{{ $pusat->jam_buka }}</dd></div>@endif
                </dl>
                @else
                    <p>Informasi kontak resmi sedang dilengkapi oleh pengelola situs. Sementara itu, pertanyaan Anda dapat disampaikan lewat akun anggota.</p>
                @endif

                @if ($cabangOperasional->isNotEmpty())
                    <div class="cabang-daftar">
                        @foreach ($cabangOperasional as $c)
                            <p><b>{{ $c->nama }}</b><br>{{ $c->alamat }}@if ($c->telepon) · {{ $c->telepon }}@endif</p>
                        @endforeach
                    </div>
                @endif

                @if ($sosmed->isNotEmpty())
                    <div class="sosmed">
                        @foreach ($sosmed as $kunci => $url)
                            <a href="{{ $url }}" rel="noopener noreferrer" target="_blank">{{ $namaSosmed[$kunci] ?? ucfirst($kunci) }}</a>
                        @endforeach
                    </div>
                @endif

                <div class="izin">
                    <b>Perizinan usaha</b>
                    @if (! empty($konten['izin_usaha']))
                        {{ $konten['izin_usaha'] }}
                    @else
                        Informasi perizinan resmi akan ditampilkan di sini setelah dilengkapi dan diverifikasi oleh pengelola.
                    @endif
                </div>
            </div>

            <div>
                @if ($punyaPeta)
                    <div class="peta">
                        <iframe title="Peta lokasi kantor pusat linygadai" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ urlencode((float) $pusat->lat.','.(float) $pusat->lng) }}&amp;z=16&amp;output=embed"></iframe>
                    </div>
                @else
                    <div class="peta-kosong">
                        <div>
                            <svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 43s14-12.5 14-23a14 14 0 0 0-28 0c0 10.5 14 23 14 23z" stroke-linejoin="round"/><circle cx="24" cy="20" r="5"/></svg>
                            <p style="margin:0">Peta kantor pusat tampil di sini setelah koordinat lokasi diisi.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
