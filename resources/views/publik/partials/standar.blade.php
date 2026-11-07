@php $standar = $konten['standar_layanan'] ?? []; @endphp
@if ($standar)
<section class="bagian" id="standar" data-spy="beranda" aria-labelledby="judul-standar">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Standar layanan</p>
            <h2 id="judul-standar">Enam hal yang kami janjikan di setiap transaksi</h2>
        </div>
        <ol class="standar">
            @foreach ($standar as $s)
                <li data-muncul>
                    <h3>{{ $s['judul'] }}</h3>
                    <p>{{ $isi($s['isi']) }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
@endif
