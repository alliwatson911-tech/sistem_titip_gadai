@use('App\Support\Rupiah')
<section class="bagian" id="produk" data-spy="produk" aria-labelledby="judul-produk">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Produk</p>
            <div>
                <h2 id="judul-produk">Barang elektronik apa yang bisa Anda titipkan</h2>
                <p class="pengantar" style="margin-top:1rem">Pilih jenis barang Anda. Batas pinjaman, tenor, dan tarif tertulis jelas di setiap kartu, dan bisa Anda hitung dulu di bagian simulasi.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Daftar produk sedang disiapkan.</p>
        @else
        <div class="produk">
            @foreach ($produk as $i => $p)
                <article class="kartu {{ $i === 0 ? 'kartu--tinta' : '' }}" data-muncul>
                    <div class="kartu-atas">
                        <span class="kategori">{{ $p->kategori_barang }}</span>
                        @include('publik.partials.ikon', ['k' => $p->kategori_barang])
                    </div>
                    <h3>{{ $p->nama }}</h3>
                    <p>{{ $p->deskripsi }}</p>
                    <dl>
                        <div><dt>Kisaran pinjaman</dt><dd>{{ Rupiah::ringkas($p->pinjaman_min) }} &ndash; {{ Rupiah::ringkas($p->pinjaman_max) }}</dd></div>
                        <div><dt>Tenor</dt><dd>{{ $p->tenor_hari }} hari</dd></div>
                        <div><dt>Pinjaman hingga</dt><dd>{{ rtrim(rtrim(number_format($p->ltv_max_bp / 100, 1, ',', ''), '0'), ',') }}% taksiran</dd></div>
                        <div><dt>Sewa modal</dt><dd>{{ rtrim(rtrim(number_format($p->sewa_modal_per_hari_bp / 100, 2, ',', ''), '0'), ',') }}% / hari</dd></div>
                    </dl>
                    <div class="aksi">
                        <a class="btn {{ $i === 0 ? '' : 'btn--utama' }} btn--kecil" href="#simulasi" data-pilih="{{ $p->slug }}">Hitung simulasi</a>
                        <a class="btn btn--kecil" href="{{ auth()->check() ? route('panel') : '#akun' }}">Ajukan</a>
                    </div>
                </article>
            @endforeach
        </div>
        @endif
    </div>
</section>
