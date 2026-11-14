@extends('layouts.publik')

@section('judul', 'Cara Penitipan & Standar Layanan — PERSIDEN HP Titip Gadai')
@section('deskripsi', 'Panduan alur penitipan barang di loket cabang PERSIDEN HP Titip Gadai: pendaftaran oleh staf cabang, pemeriksaan kondisi, nilai yang diterima, durasi 1 atau 2 minggu, hingga tebus.')

@section('badan')
    @include('publik.partials.kepala')
    <main id="isi" class="halaman-khusus">
        <div class="wrap" style="padding-top: clamp(1.5rem, 3vw, 2.5rem);">
            <x-breadcrumb backText="Kembali ke Beranda" :backUrl="route('beranda')" :crumbs="['Cara Penitipan' => '']" />
            
            <div class="halaman-header">
                <p class="kicker">Alur Layanan Resmi</p>
                <h1>Cara Penitipan Barang di PERSIDEN HP</h1>
                <p class="pengantar">Alur penitipan barang berharga dan surat berharga yang praktis, aman, dan transparan. Pendaftaran dan pencatatan transaksi dilakukan langsung oleh staf petugas di loket cabang kami di seluruh Makassar &amp; Gowa.</p>
            </div>

            <!-- Tahapan Alur Penitipan -->
            <div class="lembar-grid" style="margin-bottom: 3.5rem;">
                <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--kuningan-tua);">Langkah 01</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Kunjungi Loket Cabang</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1rem;">
                        Datang langsung ke cabang PERSIDEN HP terdekat di Makassar atau Gowa (buka setiap hari pukul <strong>08.00 – 22.00 WITA</strong>).
                    </p>
                    <ul style="padding-left: 1.2rem; font-size: .9rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        <li>Bawa barang jaminan (Laptop, HP, Tab, Kamera, BPKB, Sertifikat, atau Surat Berharga).</li>
                        <li>Sertakan kelengkapan asli (charger, dus, faktur, atau dokumen kepemilikan).</li>
                        <li>Bawa fisik e-KTP asli yang masih berlaku untuk verifikasi identitas.</li>
                    </ul>
                </div>

                <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--kuningan-tua);">Langkah 02</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Pendaftaran oleh Petugas</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1rem;">
                        Staf petugas loket menginput data diri Anda ke dalam sistem (Nama Lengkap, NIK 16 digit terenkripsi, nomor telepon/WhatsApp, dan email).
                    </p>
                    <ul style="padding-left: 1.2rem; font-size: .9rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        <li>Sistem secara otomatis menerbitkan akun digital khusus penitip.</li>
                        <li>Konfirmasi akses dan kata sandi sementara dikirimkan via email / WhatsApp.</li>
                        <li>Anda dapat masuk ke portal untuk memantau detail barang dan tanggal jatuh tempo.</li>
                    </ul>
                </div>

                <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--kuningan-tua);">Langkah 03</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Pemeriksaan &amp; Nilai</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1rem;">
                        Barang diperiksa secara terbuka di hadapan Anda untuk memastikan kondisi fisik dan fungsinya, lalu ditetapkan nilai yang diterima.
                    </p>
                    <ul style="padding-left: 1.2rem; font-size: .9rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        <li>Petugas memotret barang jaminan dan mencatat spesifikasinya ke sistem.</li>
                        <li>Anda memilih durasi penitipan: <strong>1 minggu</strong> atau <strong>2 minggu</strong>.</li>
                        <li>Jasa penitipan dan tanggal jatuh tempo dihitung transparan tanpa biaya terselubung.</li>
                    </ul>
                </div>

                <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--kuningan-tua);">Langkah 04</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Penerimaan Dana &amp; Nota</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1rem;">
                        Setelah Anda menyetujui rincian nilai dan durasi, bukti nota resmi diterbitkan dan dana langsung diserahkan secara tunai atau transfer.
                    </p>
                    <ul style="padding-left: 1.2rem; font-size: .9rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        <li>Barang fisik disimpan aman di loker penyimpanan cabang dan tidak digunakan sama sekali.</li>
                        <li>Fisik nota penitipan diserahkan sebagai tanda bukti sah untuk penebusan.</li>
                    </ul>
                </div>

                <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--kuningan-tua);">Langkah 05</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Perpanjangan atau Tebus</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1rem;">
                        Sebelum atau pada saat jatuh tempo, Anda memiliki 3 opsi fleksibel yang dicatat langsung oleh staf cabang:
                    </p>
                    <ul style="padding-left: 1.2rem; font-size: .9rem; line-height: 1.6; color: var(--muted); margin: 0;">
                        <li><strong>Perpanjang Durasi:</strong> Bayar jasa penitipan berjalan terlebih dahulu untuk memperpanjang masa titip periode berikutnya.</li>
                        <li><strong>Bayar Sebagian Pokok:</strong> Mengurangi nilai pokok agar jasa penitipan pada periode berikutnya menjadi lebih ringan.</li>
                        <li><strong>Pelunasan &amp; Ambil Barang:</strong> Lunasi sisa nilai yang diterima beserta jasa penitipan berjalan, lalu ambil barang Anda dalam kondisi utuh.</li>
                    </ul>
                </div>

                <div class="lembar panel-form" style="padding: 1.8rem; background: var(--kertas-2);" data-muncul>
                    <div class="nomor" style="margin-bottom: .8rem; color: var(--tinta);">Pilihan Cepat</div>
                    <h2 style="font-size: 1.25rem; border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem; color: var(--tinta);">Layanan Lainnya</h2>
                    <p style="font-size: .95rem; line-height: 1.6; color: var(--tinta-2); margin-bottom: 1.5rem;">
                        Hitung perkiraan jasa penitipan atau temukan alamat 7 cabang kami di area Makassar &amp; Gowa:
                    </p>
                    <div style="display: flex; flex-direction: column; gap: .75rem;">
                        <a href="{{ route('publik.simulasi') }}" class="tombol tombol--emas" style="text-align: center; text-decoration: none;">Simulasi Jasa Penitipan</a>
                        <a href="{{ route('publik.cabang') }}" class="tombol tombol--garis" style="text-align: center; text-decoration: none;">Lihat Daftar 7 Cabang</a>
                    </div>
                </div>
            </div>

            <!-- Standar Layanan PERSIDEN HP -->
            <div style="margin: 3.5rem 0 1.5rem;">
                <p class="kicker">Komitmen Kami</p>
                <h2 style="font: 700 clamp(1.5rem, 3vw, 2rem)/1.2 var(--f-judul); color: var(--tinta); margin: 0 0 .75rem;">Standar Layanan PERSIDEN HP Titip Gadai</h2>
                <p style="color: var(--muted); font-size: 1rem; max-width: 65ch; margin: 0 0 2rem;">
                    Prinsip pelayanan yang kami terapkan di seluruh loket cabang untuk kenyamanan dan ketenangan setiap penitip.
                </p>
            </div>

            <div class="syarat-grid" style="margin-bottom: 3.5rem;">
                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">01. PEMERIKSAAN</span>
                    </div>
                    <h3>Pemeriksaan Terbuka di Loket</h3>
                    <p>Pemeriksaan kondisi fisik, aksesoris, dan kelengkapan barang dilakukan langsung di depan Anda oleh petugas cabang kami sebelum penetapan nilai.</p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">02. TRANSPARANSI</span>
                    </div>
                    <h3>Biaya Jelas Tanpa Biaya Tersembunyi</h3>
                    <p>Nilai yang diterima, tarif jasa penitipan per minggu, dan tanggal jatuh tempo tercantum dengan jelas di nota transaksi fisik dan akun digital Anda.</p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">03. KEAMANAN</span>
                    </div>
                    <h3>Barang Disimpan Aman &amp; Tidak Digunakan</h3>
                    <p>Barang fisik yang dititipkan disimpan di tempat penyimpanan khusus cabang dan tidak pernah digunakan, dioperasikan, atau dipinjamkan kepada siapa pun.</p>
                </article>

                <article class="syarat-card" data-muncul>
                    <div class="syarat-card-top">
                        <span class="syarat-card-num">04. JAM LAYANAN</span>
                    </div>
                    <h3>Buka Setiap Hari Pukul 08.00 – 22.00 WITA</h3>
                    <p>Seluruh cabang kami di Makassar dan Gowa siap melayani kebutuhan transaksi penitipan dan penebusan Anda dari pagi hingga malam hari.</p>
                </article>
            </div>

            @include('publik.partials.banner-member')
        </div>
    </main>
    @include('publik.partials.kaki')
@endsection

