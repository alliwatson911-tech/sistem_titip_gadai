@extends('layouts.publik')

@section('judul', 'Visi & Misi — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Visi, misi, dan nilai-nilai pelayanan PERSIDEN HP Titip Gadai dalam melayani masyarakat Makassar dan sekitarnya.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Tentang Kami" :backUrl="route('publik.tentang')" :crumbs="['Tentang Kami' => route('publik.tentang'), 'Visi & Misi' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Tentang Kami</p>
                <h1>Visi &amp; Misi Perusahaan</h1>
                <p class="pengantar">Landasan dan arah gerak PERSIDEN HP Titip Gadai dalam memberikan layanan terbaik dengan moto <strong>Aman · Cepat · Terpercaya</strong>.</p>
            </div>

            <div class="lembar-grid" style="margin-bottom: 3.5rem;">
                <!-- Visi -->
                <div class="lembar panel-form" style="padding: 2rem;" data-muncul>
                    <span style="display: inline-block; font-size: .8rem; font-weight: 600; padding: .3rem .75rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px; margin-bottom: 1rem;">
                        [ISI: Menunggu Rumusan Final Klien]
                    </span>
                    <h2 style="font-size: 1.4rem; color: var(--tinta); border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1rem;">
                        Visi Kami
                    </h2>
                    <p style="font-size: 1.05rem; line-height: 1.7; color: var(--tinta-2);">
                        Menjadi jaringan penyedia jasa penitipan barang berharga dan titip gadai pilihan utama masyarakat di Makassar dan Sulawesi Selatan yang dikenal karena kecepatan layanan, kejujuran taksiran, serta jaminan keamanan barang yang terpercaya.
                    </p>
                </div>

                <!-- Misi -->
                <div class="lembar panel-form" style="padding: 2rem;" data-muncul>
                    <span style="display: inline-block; font-size: .8rem; font-weight: 600; padding: .3rem .75rem; background: var(--kuningan-muda); color: var(--kuningan-tua); border-radius: 9999px; margin-bottom: 1rem;">
                        [ISI: Menunggu Rumusan Final Klien]
                    </span>
                    <h2 style="font-size: 1.4rem; color: var(--tinta); border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1rem;">
                        Misi Kami
                    </h2>
                    <ol class="syarat" style="margin: 0; padding-left: 1.2rem; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Memberikan kemudahan akses dana jangka pendek dengan proses taksiran langsung di loket cabang.</li>
                        <li>Menjaga barang jaminan milik nasabah secara bertanggung jawab dan tanpa dipergunakan selama masa penitipan.</li>
                        <li>Menerapkan skema biaya dan jasa penitipan yang transparan dan terjangkau dengan opsi durasi 1 atau 2 minggu.</li>
                        <li>Memperluas jangkauan layanan melalui jaringan cabang yang nyaman dan mudah dijangkau di seluruh penjuru kota.</li>
                    </ol>
                </div>
            </div>

            <!-- Nilai-Nilai Utama -->
            <div style="margin-bottom: 1.5rem;">
                <p class="kicker">Nilai Layanan</p>
                <h2 style="font: 700 clamp(1.4rem, 2.5vw, 1.8rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 1.5rem;">
                    Tiga Pilar Utama PERSIDEN HP
                </h2>
            </div>

            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--navy-tua, #002665); color: #fff;">01</span>
                    </div>
                    <h3>Aman</h3>
                    <p>Barang titipan disimpan di lokasi penyimpanan khusus setiap cabang, tidak digunakan, dan diverifikasi bersama saat penebusan.</p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kuningan); color: var(--tinta);">02</span>
                    </div>
                    <h3>Cepat</h3>
                    <p>Pemeriksaan kondisi barang dan pencairan nilai dilakukan langsung di loket cabang tanpa proses berbelit.</p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--merah-hp, #FA0001); color: #fff;">03</span>
                    </div>
                    <h3>Terpercaya</h3>
                    <p>Seluruh rincian biaya dan durasi dijelaskan di muka, tercatat di nota fisik dan dapat dipantau langsung lewat portal digital nasabah.</p>
                </article>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

