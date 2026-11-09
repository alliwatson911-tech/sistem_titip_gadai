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
                <p class="pengantar" style="margin-top:1rem">Alur titip gadai elektronik yang jelas, praktis, dan tanpa prosedur berbelit-belit di seluruh cabang kami.</p>
            </div>
        </div>

        <div class="lembar-grid">
            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 01</div>
                <h3 style="border-bottom: 2px solid var(--kuningan); padding-bottom: .6rem; margin-bottom: 1.25rem;">Menjadi Anggota</h3>
                <ol class="langkah">
                    @foreach ($anggota as $s)
                        <li><div><b>{{ $s['judul'] }}</b><span>{{ $isi($s['isi']) }}</span></div></li>
                    @endforeach
                </ol>
            </div>

            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 02</div>
                <h3 style="border-bottom: 2px solid var(--tinta); padding-bottom: .6rem; margin-bottom: 1.25rem;">Titip Gadai Barang</h3>
                <ol class="langkah">
                    @foreach ($gadai as $s)
                        <li><div><b>{{ $s['judul'] }}</b><span>{{ $isi($s['isi']) }}</span></div></li>
                    @endforeach
                </ol>
            </div>

            <div class="lembar panel-form" style="padding: 1.8rem;" data-muncul>
                <div class="nomor" style="margin-bottom: .8rem;">Tahap 03</div>
                <h3 style="border-bottom: 2px solid var(--lumut); padding-bottom: .6rem; margin-bottom: 1.25rem;">Pelunasan &amp; Tebus</h3>
                <ol class="langkah">
                    <li><div><b>Perpanjang Masa Titip</b><span>Sebelum jatuh tempo, Anda dapat memperpanjang masa simpan dengan melunasi sewa modal dan administrasi berjalan.</span></div></li>
                    <li><div><b>Cicil Pokok Pinjaman</b><span>Kurangi beban kewajiban dengan mencicil pokok pinjaman bertahap sesuai kesiapan dana Anda.</span></div></li>
                    <li><div><b>Pelunasan &amp; Pengambilan</b><span>Lunasi sisa pokok dan biaya untuk mengambil kembali barang elektronik Anda yang tersimpan aman bersegel brankas.</span></div></li>
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
