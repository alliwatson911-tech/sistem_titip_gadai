@extends('layouts.publik')

@section('judul', 'Promo & Program Khusus — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Informasi program promo, potongan jasa penitipan, dan penawaran khusus di seluruh cabang PERSIDEN HP Titip Gadai Makassar & Gowa.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Promo' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Program &amp; Penawaran</p>
                <h1>Promo &amp; Informasi PERSIDEN HP</h1>
                <p class="pengantar">Temukan program promo terbaru, potongan tarif jasa penitipan, dan pengumuman resmi untuk penitip di seluruh cabang kami.</p>
            </div>

            @if(isset($promo) && $promo->isNotEmpty())
                <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                    @foreach($promo as $p)
                        <article class="syarat-card" data-muncul>
                            <div class="syarat-card-top" style="justify-content: space-between; align-items: center;">
                                <span class="syarat-card-num" style="background: var(--kuningan-muda); color: var(--kuningan-tua);">PROMO</span>
                                @if($p->mulai || $p->selesai)
                                    <span style="font-size: .8rem; color: var(--muted);">
                                        {{ $p->mulai ? \Carbon\Carbon::parse($p->mulai)->translatedFormat('d M Y') : '' }}
                                        @if($p->selesai) s.d. {{ \Carbon\Carbon::parse($p->selesai)->translatedFormat('d M Y') }} @endif
                                    </span>
                                @endif
                            </div>
                            <h2 style="font-size: 1.25rem; margin: .5rem 0 .75rem; color: var(--tinta);">{{ $p->judul }}</h2>
                            <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">{{ $p->isi }}</p>
                            @if($p->tautan)
                                <div style="margin-top: 1rem;">
                                    <a href="{{ $p->tautan }}" class="tautan-ringkas" style="font-size: .9rem;">Pelajari Selengkapnya &rarr;</a>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="panel-form" style="padding: 2.5rem; text-align: center; margin-bottom: 3.5rem; background: var(--kertas-2);" data-muncul>
                    <div style="font-size: 2.5rem; margin-bottom: .75rem;" aria-hidden="true">🏷️</div>
                    <h2 style="font-size: 1.35rem; color: var(--tinta); margin-bottom: .5rem;">Belum Ada Promo Aktif</h2>
                    <p style="color: var(--muted); max-width: 50ch; margin: 0 auto 1.5rem; font-size: .95rem; line-height: 1.6;">
                        Saat ini belum ada program promo khusus yang sedang berlangsung. Silakan kunjungi cabang PERSIDEN HP terdekat atau hubungi loket kami untuk konsultasi penitipan terbaik.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('publik.cabang') }}" class="tombol tombol--emas" style="text-decoration: none;">Lihat Lokasi Cabang</a>
                        <a href="{{ route('publik.simulasi') }}" class="tombol tombol--garis" style="text-decoration: none;">Hitung Simulasi Titip</a>
                    </div>
                </div>
            @endif

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

