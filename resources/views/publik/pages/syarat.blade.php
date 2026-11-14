@extends('layouts.publik')

@section('judul', 'Syarat & Ketentuan Penitipan — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Syarat dan ketentuan lengkap layanan titip gadai PERSIDEN HP: kelengkapan identitas, kriteria barang jaminan, jasa penitipan, jatuh tempo, dan FAQ.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Syarat & Ketentuan' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Ketentuan Layanan</p>
                <h1>Syarat &amp; Ketentuan Titip Gadai</h1>
                <p class="pengantar">Ketentuan umum dan persyaratan kelengkapan yang berlaku dalam akad titip gadai barang berharga dan surat berharga di seluruh cabang PERSIDEN HP. Terpercaya melayani sejak tahun 2005.</p>
            </div>

            <!-- Banner Terpercaya Sejak 2005 & Instagram Resmi -->
            <div class="trust-ig-banner" data-muncul>
                <div class="trust-ig-konten">
                    <div class="trust-ig-badge-star">
                        <span class="star-text">TRUSTED</span>
                        <span class="star-icons">★★★★★</span>
                        <span class="star-year">SEJAK 2005</span>
                    </div>
                    <div class="trust-ig-deskripsi">
                        <strong>PERSIDEN HP · Terpercaya Sejak Tahun 2005</strong>
                        <p>Berpengalaman lebih dari 20 tahun melayani jual beli dan titip gadai handphone &amp; barang berharga di Makassar. Syarat transparan, taksiran adil di loket, dan barang tersimpan aman tanpa dipakai.</p>
                    </div>
                </div>
                <div class="trust-ig-aksi">
                    <a href="https://www.instagram.com/persidenhp.official/" target="_blank" rel="noopener noreferrer" class="btn-ig-official">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>@persidenhp.official</span>
                    </a>
                </div>
            </div>

            <!-- Kotak Syarat Kelengkapan Dokumen & Penitip -->
            <div class="syarat-highlight-box" data-muncul>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                    <h2 style="margin: 0; padding: 0; border: 0; font-size: 1.4rem; color: var(--tinta);">Syarat Umum Kelengkapan Penitip</h2>
                    <span style="font: 600 .75rem/1 var(--f-mono); text-transform: uppercase; letter-spacing: .08em; background: rgba(30, 90, 60, .12); color: #1e5a3c; padding: .4rem .75rem; border-radius: 9999px; border: 1px solid rgba(30, 90, 60, .3);">Wajib Dipenuhi</span>
                </div>
                <p style="margin: 0 0 1.25rem; font-size: .98rem; line-height: 1.6; color: var(--muted);">
                    Pastikan Anda dan barang jaminan yang diajukan memenuhi kriteria dasar di bawah ini sebelum datang ke loket cabang PERSIDEN HP:
                </p>
                <ul class="syarat" style="gap: 1.1rem;">
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Identitas Asli:</strong> Membawa e-KTP fisik asli yang masih berlaku untuk pencatatan NIK oleh petugas loket cabang.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Kecakapan Hukum:</strong> Penitip berusia minimal 18 tahun atau telah cakap hukum menurut peraturan perundang-undangan Republik Indonesia.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Kepemilikan Sah:</strong> Barang jaminan merupakan milik pribadi sah penitip. Apabila atas nama orang lain/keluarga, wajib menyertakan bukti kepemilikan dan identitas pendukung.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Bebas Sengketa:</strong> Barang bukan merupakan objek sitaan peradilan, sengketa hak waris/pihak ketiga, atau hasil tindak pidana.
                    </li>
                </ul>
            </div>

            <!-- Persyaratan Spesifik Berdasarkan Kategori Barang Klien -->
            <div style="margin: 3rem 0 1.5rem;">
                <p class="kicker">Kategori Jaminan</p>
                <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .75rem;">Persyaratan Spesifik per Jenis Barang</h2>
                <p style="color: var(--muted); font-size: 1rem; max-width: 65ch; margin: 0 0 1.5rem;">
                    Setiap jenis barang jaminan memiliki standar pemeriksaan kondisi, kelengkapan wajib, dan mekanisme penyimpanan yang disesuaikan:
                </p>
            </div>

            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kuningan-muda); color: var(--kuningan-tua);">01. ELEKTRONIK</span>
                    </div>
                    <h3>Laptop, HP, Tab &amp; Kamera</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Unit dapat dinyalakan normal dan fungsi perangkat bekerja baik (layar sentuh, kamera depan/belakang, mikrofon/speaker, dan performa baterai).</li>
                        <li>Wajib logout dan hapus seluruh kunci akun (iCloud/Apple ID, Akun Google, Mi/Samsung Account, pola/sandi layar) di hadapan petugas sebelum penaksiran akhir.</li>
                        <li>Membawa charger atau adaptor pengisi daya asli dan kelengkapan unit (kotak/dus bila ada).</li>
                        <li><strong>Penyimpanan &amp; Segel:</strong> Unit fisik disegel menggunakan stiker pengaman khusus berparaf penitip dan disimpan di tempat penyimpanan aman cabang tanpa pernah dioperasikan selama masa penitipan.</li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kertas-2); color: var(--tinta);">02. KENDARAAN</span>
                    </div>
                    <h3>BPKB Mobil &amp; BPKB Motor</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>BPKB asli beserta faktur pembelian sah.</li>
                        <li>STNK asli yang masih aktif masa berlakunya.</li>
                        <li>Kuitansi jual beli bermeterai dan KTP pemilik awal bila belum balik nama.</li>
                        <li>Pemeriksaan nomor rangka dan mesin di kantor cabang.</li>
                        <li><strong>Penyimpanan:</strong> Dokumen BPKB disimpan aman di cabang.</li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kertas-2); color: var(--tinta);">03. PROPERTI</span>
                    </div>
                    <h3>Sertifikat Rumah &amp; Tanah</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Sertifikat Asli: Sertifikat Hak Milik (SHM) atau Hak Guna Bangunan (SHGB) aktif.</li>
                        <li>Bukti lunas PBB tahun berjalan dan identitas pemilik.</li>
                        <li>Pengecekan keabsahan dokumen dan survei lokasi.</li>
                        <li><strong>Penyimpanan:</strong> Fisik sertifikat disimpan dengan aman di kantor cabang.</li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kertas-2); color: var(--tinta);">04. LAINNYA</span>
                    </div>
                    <h3>Barang &amp; Surat Berharga Lainnya</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Dokumen instrumen berharga legal yang dapat diverifikasi keabsahannya.</li>
                        <li>Nota/bukti perolehan sah dari penerbit resmi.</li>
                        <li>Pemeriksaan fisik langsung di hadapan penitip sebelum penetapan taksiran.</li>
                        <li><strong>Penyimpanan:</strong> Disimpan dalam loker penyimpanan khusus cabang.</li>
                    </ul>
                </article>
            </div>

            <!-- Ketentuan Pokok Transaksi -->
            <div style="margin-bottom: 1.5rem;">
                <p class="kicker">Ketentuan Pokok</p>
                <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .75rem;">Ketentuan Transaksi Penitipan</h2>
                <p style="color: var(--muted); font-size: 1rem; max-width: 65ch; margin: 0 0 2rem;">
                    Aturan dasar yang berlaku dalam setiap transaksi titip gadai di PERSIDEN HP.
                </p>
            </div>

            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 01</span>
                    </div>
                    <h3>Pemeriksaan &amp; Nilai yang Diterima</h3>
                    <p>
                        Pemeriksaan dilakukan secara langsung di hadapan calon penitip berdasarkan kondisi fisik, fungsi operasional, dan kelengkapan barang. Penetapan nilai yang diterima dijelaskan secara terbuka sebelum nota dicetak.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 02</span>
                    </div>
                    <h3>Jangka Waktu Titip &amp; Jatuh Tempo</h3>
                    <p>
                        Penitip dapat memilih durasi penitipan selama <strong>1 minggu</strong> atau <strong>2 minggu</strong>. Tanggal jatuh tempo tercantum secara pasti pada fisik Nota Penitipan dan akun portal digital penitip.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 03</span>
                    </div>
                    <h3>Jasa Penitipan</h3>
                    <p>
                        Besaran tarif jasa penitipan disesuaikan dengan jenis barang dan durasi yang dipilih. Seluruh rincian tarif jasa penitipan dan biaya administrasi (jika ada) disampaikan secara terbuka di muka tanpa biaya tersembunyi.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 04</span>
                    </div>
                    <h3>Perpanjangan Masa Titip</h3>
                    <p>
                        Penitip dapat memperpanjang masa simpan sebelum atau saat jatuh tempo dengan melunasi jasa penitipan periode berjalan terlebih dahulu di loket cabang.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 05</span>
                    </div>
                    <h3>Pembayaran Sebagian Pokok</h3>
                    <p>
                        Penitip diperbolehkan mencicil atau membayar sebagian dari nilai pokok yang diterima untuk memperkecil beban jasa penitipan pada periode berikutnya.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 06</span>
                    </div>
                    <h3>Pelunasan &amp; Pengambilan Barang</h3>
                    <p>
                        Penebusan dapat dilakukan kapan saja sebelum jatuh tempo. Pengambilan barang jaminan wajib dilakukan langsung oleh pemilik sah dengan menunjukkan fisik Nota Penitipan dan e-KTP asli.
                    </p>
                </article>
            </div>

            <!-- Bagian FAQ (Tanya Jawab) -->
            <div id="faq" style="scroll-margin-top: 100px; margin-bottom: 3.5rem;">
                <div class="halaman-header" style="text-align: left; margin-bottom: 2rem;">
                    <p class="kicker">Tanya Jawab</p>
                    <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .5rem;">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                    <p class="pengantar" style="margin: 0;">Jawaban atas pertanyaan umum seputar layanan titip gadai, barang jaminan, dan tata cara transaksi di PERSIDEN HP.</p>
                </div>

                @if(isset($faq) && $faq->isNotEmpty())
                    <div class="akordeon" data-muncul>
                        @foreach ($faq as $f)
                            <details name="faq" style="background: #fff; margin-bottom: .75rem; border: 1.5px solid var(--garis); border-radius: var(--radius-m); padding: 1rem 1.25rem;">
                                <summary style="font-weight: 600; cursor: pointer; color: var(--tinta); font-size: 1.05rem;">{{ $f->pertanyaan }}</summary>
                                <div class="jawab" style="margin-top: .75rem; padding-top: .75rem; border-top: 1px dashed var(--garis); font-size: .95rem; line-height: 1.7; color: var(--tinta-2);">
                                    <p style="margin: 0;">{{ $f->jawaban }}</p>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    <div class="panel-form" style="padding: 2rem; background: var(--kertas-2);" data-muncul>
                        <p style="margin: 0; color: var(--muted); font-size: .95rem;">
                            Untuk pertanyaan lebih lanjut yang belum tercantum, silakan hubungi petugas kami di cabang terdekat atau melalui kontak WhatsApp cabang.
                        </p>
                    </div>
                @endif
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection
