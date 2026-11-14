@php
    $anggota = $konten['panduan_anggota'] ?? [];
    $gadai = $konten['panduan_gadai'] ?? [];
    $syarat = $konten['syarat_online'] ?? [];
@endphp
<section class="bagian bagian--kertas2" id="panduan" data-spy="panduan" aria-labelledby="judul-panduan">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Alur Layanan</p>
            <div>
                <h2 id="judul-panduan">Panduan Ringkas: Dari Mendaftar Sampai Tebus</h2>
                <p class="pengantar" style="margin-top:1rem">Alur titip gadai resmi yang jelas, transparan, dan terstandarisasi untuk barang bergerak, dokumen kendaraan, sertifikat, dan surat berharga di seluruh cabang kami.</p>
            </div>
        </div>

        <div class="lembar-grid">
            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 01</div>
                <h3 style="border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem;">Pendaftaran di Loket</h3>
                <ol class="langkah">
                    <li><div><b>Datang ke Loket Cabang</b><span>Kunjungi salah satu dari 7 cabang PERSIDEN HP dengan membawa barang jaminan dan KTP asli.</span></div></li>
                    <li><div><b>Pencatatan oleh Staf</b><span>Petugas loket mencatat data identitas (nama, NIK, kontak) dan membuatkan akun digital.</span></div></li>
                    <li><div><b>Akses Masuk Terbit</b><span>Notifikasi tautan masuk dan kata sandi sementara dikirimkan via email / WhatsApp.</span></div></li>
                </ol>
            </div>

            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 02</div>
                <h3 style="border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem;">Pemeriksaan &amp; Nilai</h3>
                <ol class="langkah">
                    <li><div><b>Pemeriksaan Terbuka</b><span>Kondisi fisik barang dan kelengkapan diperiksa langsung di hadapan Anda.</span></div></li>
                    <li><div><b>Pilihan Durasi</b><span>Tentukan durasi penitipan yang Anda inginkan: 1 minggu atau 2 minggu.</span></div></li>
                    <li><div><b>Penerimaan Nilai</b><span>Nota diterbitkan dan dana langsung diserahkan tunai atau transfer.</span></div></li>
                </ol>
            </div>

            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 03</div>
                <h3 style="border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem;">Pelunasan &amp; Tebus</h3>
                <ol class="langkah">
                    <li><div><b>Perpanjang Masa Titip</b><span>Sebelum jatuh tempo, Anda dapat memperpanjang dengan melunasi jasa penitipan periode berjalan.</span></div></li>
                    <li><div><b>Bayar Sebagian Pokok</b><span>Kurangi beban jasa berikutnya dengan membayar sebagian dari nilai pokok.</span></div></li>
                    <li><div><b>Pelunasan &amp; Pengambilan</b><span>Lunasi sisa nilai yang diterima dan jasa penitipan untuk mengambil kembali barang Anda secara utuh.</span></div></li>
                </ol>
                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--garis);">
                    <a href="{{ route('publik.syarat') }}" style="display:inline-flex; align-items:center; gap:.4rem; font:600 .85rem/1.3 var(--f-isi); color:var(--kuningan-tua); text-decoration:none;">
                        <span>Lihat Syarat &amp; Ketentuan Lengkap</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
