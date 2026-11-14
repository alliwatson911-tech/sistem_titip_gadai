@extends('layouts.publik')

@section('judul', 'Legalitas & Perizinan — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Informasi badan hukum, perizinan usaha, dan dokumen legalitas operasional PERSIDEN HP Titip Gadai.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Tentang Kami" :backUrl="route('publik.tentang')" :crumbs="['Tentang Kami' => route('publik.tentang'), 'Legalitas' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Kepatuhan &amp; Transparansi</p>
                <h1>Legalitas &amp; Dokumen Perizinan</h1>
                <p class="pengantar">PERSIDEN HP Titip Gadai beroperasi dengan komitmen kepatuhan terhadap regulasi hukum perdata dan perdagangan yang berlaku di Negara Kesatuan Republik Indonesia.</p>
            </div>

            <div class="syarat-highlight-box" style="margin-bottom: 2.5rem; border-left-color: var(--kuningan);" data-muncul>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <span style="display: inline-block; font-size: .8rem; font-weight: 600; padding: .3rem .75rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px; margin-bottom: .5rem;">
                            [ISI: Menunggu Salinan Dokumen dari Klien]
                        </span>
                        <h2 style="font-size: 1.25rem; margin: 0; color: var(--tinta);">Publikasi Dokumen Legalitas Resmi</h2>
                    </div>
                </div>
                <p style="margin: .75rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--muted);">
                    Halaman ini disediakan sebagai pusat transparansi informasi badan usaha, Nomor Induk Berusaha (NIB), perizinan operasional loket cabang, dan ketentuan hukum perjanjian penitipan barang. Seluruh rincian perizinan akan ditampilkan secara lengkap setelah diverifikasi bersama klien.
                </p>
            </div>

            <!-- Kerangka Komponen Legalitas -->
            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">01. BADAN USAHA</span>
                    </div>
                    <h3>Legalitas Perusahaan</h3>
                    <p style="color: var(--muted); font-size: .95rem; line-height: 1.6;">
                        Nama Badan Usaha: <strong>PERSIDEN HP</strong><br>
                        Status Badan Hukum: <em>[Menunggu Konfirmasi Klien]</em><br>
                        Akta Pendirian &amp; SK Kemenkumham: <em>[Menunggu Penyerahan Berkas]</em>
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">02. PERIZINAN BERUSAHA</span>
                    </div>
                    <h3>Nomor Induk Berusaha (NIB)</h3>
                    <p style="color: var(--muted); font-size: .95rem; line-height: 1.6;">
                        Nomor NIB: <em>[Menunggu Konfirmasi Klien]</em><br>
                        Klasifikasi Baku Lapangan Usaha (KBLI): Jasa penitipan dan perdagangan barang bergerak.<br>
                        Status Operasional: Aktif beroperasi di 7 cabang.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">03. PERJANJIAN &amp; AKAD</span>
                    </div>
                    <h3>Perjanjian Titip Gadai Sah</h3>
                    <p style="color: var(--muted); font-size: .95rem; line-height: 1.6;">
                        Setiap transaksi penitipan barang dilandasi oleh Nota Perjanjian Titip Gadai yang memuat identitas penitip, data fisik barang jaminan, nilai yang diterima, tarif jasa penitipan, dan tanggal jatuh tempo yang mengikat kedua belah pihak secara hukum perdata.
                    </p>
                </article>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

