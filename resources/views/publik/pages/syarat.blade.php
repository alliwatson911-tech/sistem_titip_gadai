@extends('layouts.publik')

@section('judul', 'Syarat & Ketentuan Titip Gadai — linygadai')
@section('deskripsi', 'Syarat dan ketentuan lengkap layanan titip gadai elektronik linygadai: persyaratan kelengkapan dokumen, kriteria barang jaminan, biaya sewa modal, jatuh tempo, dan hak penebusan.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Syarat & Ketentuan' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Ketentuan Resmi</p>
                <h1>Syarat &amp; Ketentuan Layanan Titip Gadai</h1>
                <p class="pengantar">Ketentuan umum dan persyaratan kelengkapan yang berlaku dalam perjanjian titip gadai barang elektronik di seluruh cabang resmi linygadai.</p>
            </div>

            <!-- Kotak Syarat Kelengkapan Dokumen & Barang (Pindahan dari Panduan) -->
            <div class="syarat-highlight-box" data-muncul>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                    <h2 style="margin: 0; padding: 0; border: 0; font-size: 1.4rem;">Syarat Umum Kelengkapan Dokumen &amp; Penggadai</h2>
                    <span style="font: 600 .75rem/1 var(--f-mono); text-transform: uppercase; letter-spacing: .08em; background: rgba(30, 90, 60, .12); color: var(--lumut); padding: .4rem .75rem; border-radius: 9999px; border: 1px solid rgba(30, 90, 60, .3);">Wajib Dipenuhi</span>
                </div>
                <p style="margin: 0 0 1.25rem; font-size: .98rem; line-height: 1.6; color: var(--muted);">
                    Pastikan Anda dan jaminan yang diajukan memenuhi kriteria dasar di bawah ini sebelum datang ke loket kantor cabang linygadai:
                </p>
                <ul class="syarat" style="gap: 1.1rem;">
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Identitas Asli:</strong> Membawa e-KTP fisik asli yang masih berlaku dan data terdaftar resmi di Disdukcapil.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Kecakapan Hukum:</strong> Pemohon berusia minimal 18 tahun atau telah cakap hukum menurut peraturan perundang-undangan Republik Indonesia.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Kepemilikan Sah:</strong> Agunan merupakan milik pribadi sah pemohon. Apabila atas nama orang lain/keluarga, wajib menyertakan surat kuasa bermeterai dan identitas pemilik asal.
                    </li>
                    <li style="font-size: 1rem; line-height: 1.5; color: var(--tinta);">
                        <strong>Bebas Sengketa:</strong> Agunan bukan merupakan objek sitaan peradilan, sengketa hak waris/pihak ketiga, atau hasil tindak pidana kejahatan.
                    </li>
                </ul>
            </div>

            <!-- Persyaratan Spesifik Berdasarkan 4 Kelompok Jaminan -->
            <div style="margin: 3rem 0 1.5rem;">
                <p class="nomor" style="margin-bottom: .4rem;">Kategori Agunan</p>
                <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .75rem;">Persyaratan Spesifik per Jenis Jaminan</h2>
                <p style="color: var(--muted); font-size: 1rem; max-width: 65ch; margin: 0 0 1.5rem;">
                    Setiap jenis agunan memiliki standar pemeriksaan teknis, dokumen wajib, dan mekanisme penyimpanan yang disesuaikan:
                </p>
            </div>

            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kuningan-muda); color: var(--kuningan-tua);">01. ELEKTRONIK</span>
                    </div>
                    <h3>Laptop &amp; Smartphone</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Unit dapat dinyalakan dan fungsi perangkat normal (layar, audio, kamera, baterai).</li>
                        <li>Wajib logout seluruh kunci akun (iCloud, Apple ID, Akun Google/Gmail, Mi/Samsung Account).</li>
                        <li>Membawa charger/adaptor pengisi daya asli dan perlengkapan dus jika ada.</li>
                        <li><strong>Penyimpanan:</strong> Unit fisik disimpan di brankas ber-AC dengan segel bernomor seri khusus.</li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--lumut-muda); color: var(--lumut);">02. KENDARAAN</span>
                    </div>
                    <h3>BPKB Mobil &amp; BPKB Motor</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>BPKB asli dan faktur pembelian sah.</li>
                        <li>STNK asli yang masih aktif masa berlakunya.</li>
                        <li>Kuitansi jual beli bermeterai dan KTP pemilik awal bila belum balik nama.</li>
                        <li>Cek fisik kendaraan (nomor rangka &amp; nomor mesin) di kantor cabang linygadai.</li>
                        <li><strong>Penyimpanan:</strong> BPKB disimpan di brankas khasanah. <em>Fisik kendaraan tetap Anda gunakan.</em></li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: rgba(184, 134, 43, .15); color: var(--kuningan-tua);">03. PROPERTI</span>
                    </div>
                    <h3>Sertifikat Rumah &amp; Tanah</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Sertifikat Asli: Sertifikat Hak Milik (SHM) atau Hak Guna Bangunan (SHGB) aktif.</li>
                        <li>Bukti lunas PBB (Surat Tanda Terima Setoran) tahun berjalan.</li>
                        <li>Kartu Keluarga (KK) dan persetujuan tertulis pasangan (suami/istri).</li>
                        <li>Pengecekan keabsahan warkah di Kantor Pertanahan (BPN) dan survei lokasi lapangan.</li>
                        <li><strong>Penyimpanan:</strong> Sertifikat disimpan di khasanah tahan api. <em>Properti tetap Anda huni/kelola.</em></li>
                    </ul>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num" style="background: var(--kertas-2); color: var(--tinta);">04. SURAT BERHARGA</span>
                    </div>
                    <h3>Barang &amp; Surat Berharga Lainnya</h3>
                    <ul style="padding-left: 1.2rem; margin: .5rem 0 0; font-size: .95rem; line-height: 1.6; color: var(--tinta-2);">
                        <li>Sertifikat resmi keaslian/kadar untuk logam mulia atau perhiasan berharga.</li>
                        <li>Nota/faktur pembelian resmi dari toko atau lembaga penerbit sah.</li>
                        <li>Dokumen instrumen berharga legal yang dapat diverifikasi keabsahannya.</li>
                        <li>Uji karatase/keaslian di hadapan pemilik sebelum penetapan taksiran.</li>
                        <li><strong>Penyimpanan:</strong> Disimpan dalam safety deposit box / khasanah berkeamanan tinggi.</li>
                    </ul>
                </article>
            </div>

            <!-- Grid Ketentuan Umum Sistem Titip Gadai -->
            <div style="margin-bottom: 1.5rem;">
                <p class="nomor" style="margin-bottom: .4rem;">Ketentuan Pokok</p>
                <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .75rem;">Ketentuan Transaksi &amp; Pengelolaan Barang</h2>
                <p style="color: var(--muted); font-size: 1rem; max-width: 65ch; margin: 0 0 2rem;">
                    Standar aturan yang mengikat hak serta kewajiban antara pihak nasabah dan linygadai dalam setiap akad titip gadai.
                </p>
            </div>

            <div class="syarat-grid">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 01</span>
                    </div>
                    <h3>Pemeriksaan &amp; Penetapan Nilai Taksiran</h3>
                    <p>
                        Penetapan taksiran dilakukan secara objektif oleh analis bersertifikasi di hadapan calon nasabah berdasarkan kondisi fisik, fungsi operasional, kelengkapan, dan harga pasar wajar barang elektronik. Nasabah berhak membatalkan pengajuan tanpa biaya jika tidak menyetujui hasil taksiran.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 02</span>
                    </div>
                    <h3>Plafon Pinjaman &amp; Batas LTV</h3>
                    <p>
                        Besaran pinjaman dana tunai yang dapat dicairkan berkisar antara 70% hingga 80% dari total nilai taksiran barang (Loan to Value), dengan batas pinjaman minimum dan maksimum yang disesuaikan dengan jenis barang elektronik terkait.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 03</span>
                    </div>
                    <h3>Jangka Waktu Titip &amp; Jatuh Tempo</h3>
                    <p>
                        Masa titip standar adalah {tenor} hari kalender sejak tanggal penandatanganan akad nota gadai. Nasabah juga dapat memilih skema titip harian atau mingguan yang dihitung proporsional. Tanggal jatuh tempo tercantum secara mengikat pada fisik Nota Gadai dan akun digital nasabah.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 04</span>
                    </div>
                    <h3>Sewa Modal &amp; Biaya Administrasi</h3>
                    <p>
                        Biaya sewa modal dihitung proporsional harian sesuai tarif resmi tanpa bunga berbunga terselubung. Biaya administrasi dibayarkan pada saat penerbitan akad awal dan saat perpanjangan tenor. Seluruh nominal biaya tertulis transparan di muka tanpa pungutan tambahan lainnya.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 05</span>
                    </div>
                    <h3>Hak Perpanjangan &amp; Cicilan Pokok</h3>
                    <p>
                        Nasabah dapat memperpanjang masa titip maksimal {maks_perpanjangan} kali berturut-turut sebelum tanggal jatuh tempo dengan melunasi sewa modal dan biaya administrasi berjalan. Nasabah juga diperbolehkan mencicil pokok pinjaman untuk memperkecil beban biaya sewa pada periode berikutnya.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 06</span>
                    </div>
                    <h3>Pelunasan &amp; Pengambilan Barang</h3>
                    <p>
                        Penebusan barang dapat dilakukan kapan saja sebelum atau tepat pada tanggal jatuh tempo. Pengambilan barang jaminan wajib dilakukan langsung oleh pemilik sah dengan menunjukkan fisik Nota Gadai dan KTP asli, atau oleh penerima kuasa dengan surat kuasa resmi yang sah.
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 07</span>
                    </div>
                    <h3>Masa Tenggang &amp; Pemberitahuan Resmi</h3>
                    <p>
                        Apabila nasabah belum melakukan penebusan hingga jatuh tempo, linygadai memberikan masa tenggang selama {tenggang} hari kalender. Pemberitahuan resmi dikirimkan secara berkala (H-7 jatuh tempo, H-1, masa tenggang, dan pemberitahuan tertulis {lelang_hari} hari sebelum jadwal lelang).
                    </p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 08</span>
                    </div>
                    <h3>Prosedur Lelang &amp; Hak Sisa Hasil Lelang</h3>
                    <p>
                        Barang yang tidak ditebus atau tidak diperpanjang setelah melewati masa tenggang dan surat pemberitahuan akan diproses lelang terbuka. Apabila hasil lelang melebihi total kewajiban pokok, sewa modal, dan biaya lelang, selisih lebih tersebut mutlak merupakan hak nasabah dan dapat diambil di kasir cabang.
                    </p>
                </article>

                <article class="syarat-card" data-muncul style="grid-column: 1 / -1;">
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">PASAL 09</span>
                    </div>
                    <h3>Jaminan Keamanan &amp; Integritas Brankas Bersegel</h3>
                    <p>
                        Setiap unit barang elektronik ditempeli stiker segel resmi bernomor seri unik di hadapan nasabah sebelum dimasukkan ke dalam brankas khusus berpendingin (AC). linygadai menjamin barang tidak pernah dibuka, dipinjamkan, atau dioperasikan selama masa titip. Saat penebusan, stiker segel diverifikasi bersama nasabah untuk memastikan kondisi barang utuh sempurna.
                    </p>
                </article>
            </div>

            <!-- Ajakan Anggota -->
            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

