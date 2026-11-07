@php
    $anggota = $konten['panduan_anggota'] ?? [];
    $gadai = $konten['panduan_gadai'] ?? [];
    $syarat = $konten['syarat_online'] ?? [];
@endphp
<section class="bagian bagian--kertas2" id="panduan" data-spy="panduan" aria-labelledby="judul-panduan">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Panduan</p>
            <h2 id="judul-panduan">Dari mendaftar sampai barang kembali ke tangan Anda</h2>
        </div>
        <div class="lembar-grid">
            <div class="lembar" data-muncul>
                <h3>Menjadi anggota</h3>
                <ol class="langkah">
                    @foreach ($anggota as $s)
                        <li><div><b>{{ $s['judul'] }}</b><span>{{ $isi($s['isi']) }}</span></div></li>
                    @endforeach
                </ol>
            </div>
            <div class="lembar" data-muncul>
                <h3>Titip gadai, langkah demi langkah</h3>
                <ol class="langkah">
                    @foreach ($gadai as $s)
                        <li><div><b>{{ $s['judul'] }}</b><span>{{ $isi($s['isi']) }}</span></div></li>
                    @endforeach
                </ol>
            </div>
            <div class="lembar" data-muncul>
                <h3>Syarat umum online</h3>
                <ul class="syarat">
                    @foreach ($syarat as $s)
                        <li>{{ $isi($s) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
