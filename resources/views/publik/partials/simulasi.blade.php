@php $tataCara = $konten['tata_cara'] ?? []; @endphp
<section class="bagian" id="simulasi" data-spy="simulasi" aria-labelledby="judul-simulasi">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Simulasi</p>
            <div>
                <h2 id="judul-simulasi">Hitung dulu, putuskan kemudian</h2>
                <p class="pengantar" style="margin-top:1rem">Masukkan perkiraan nilai barang Anda. Kami tampilkan pinjaman, biaya, dan total yang harus ditebus sebelum Anda datang ke cabang.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Simulasi akan tersedia setelah produk diisi.</p>
        @else
        <div class="sim">
            <form id="sim-form" class="panel-form" style="padding: 2rem;" data-url="{{ route('simulasi') }}" novalidate>
                <div class="bidang">
                    <label for="sim-produk">Jenis barang</label>
                    <select id="sim-produk" name="produk">
                        @foreach ($produk as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bidang">
                    <label for="sim-taksiran">Perkiraan taksiran barang</label>
                    <div class="awalan"><span aria-hidden="true">Rp</span><input id="sim-taksiran" type="text" inputmode="numeric" autocomplete="off" value="3.000.000"></div>
                    <p class="catatan-kecil" style="margin:.4rem 0 0">Perkiraan nilai barang Anda saat ini. Nilai pasti ditetapkan analis setelah barang diperiksa.</p>
                </div>
                <div class="bidang">
                    <label for="sim-ltv">Persentase pinjaman dari taksiran: <output id="sim-ltv-nilai" for="sim-ltv">80%</output></label>
                    <input id="sim-ltv" type="range" min="1000" max="8000" step="500" value="8000">
                    <div class="rentang-info"><span id="sim-batas"></span></div>
                </div>
                <fieldset class="bidang">
                    <legend>Lama titip</legend>
                    <div class="segmen" role="radiogroup" aria-label="Lama titip">
                        @for ($n = 1; $n <= $maksPeriode; $n++)
                            <input type="radio" name="periode" id="periode-{{ $n }}" value="{{ $n }}" @checked($n === 1)>
                            <label for="periode-{{ $n }}"><span>{{ $n * $token['tenor'] }} hari<br><small>{{ $n === 1 ? 'tanpa perpanjang' : ($n - 1).'x perpanjang' }}</small></span></label>
                        @endfor
                    </div>
                </fieldset>
                <noscript><p class="galat">Simulasi memerlukan JavaScript aktif di browser Anda.</p></noscript>
            </form>

            <div class="nota-bungkus">
                <div class="nota" id="nota" aria-live="polite">
                    <h3><span>Nota simulasi</span><span>bukan penawaran</span></h3>
                    <div class="nota-baris"><span>Jenis barang</span><b id="n-produk">&mdash;</b></div>
                    <div class="nota-baris"><span>Perkiraan taksiran</span><b id="n-taksiran">&mdash;</b></div>
                    <div class="nota-baris"><span>Persentase pinjaman</span><b id="n-ltv">&mdash;</b></div>
                    <div class="nota-baris"><span>Pokok pinjaman</span><b id="n-pokok">&mdash;</b></div>
                    <div class="nota-baris"><span>Lama titip</span><b id="n-hari">&mdash;</b></div>
                    <div class="nota-baris"><span>Sewa modal</span><b id="n-sewa">&mdash;</b></div>
                    <div class="nota-baris"><span>Biaya administrasi</span><b id="n-admin">&mdash;</b></div>
                    <div class="nota-baris"><span>Jatuh tempo terakhir*</span><b id="n-jatuh">&mdash;</b></div>
                    <div class="nota-total"><span>Total tebus</span><b id="n-total">&mdash;</b></div>
                    <div id="n-rincian-bungkus" hidden>
                        <table class="nota-rincian">
                            <thead><tr><th>Periode</th><th>Sewa modal</th><th>Administrasi</th></tr></thead>
                            <tbody id="n-rincian"></tbody>
                        </table>
                    </div>
                    <p class="nota-pesan" id="n-pesan" role="status"></p>
                </div>
                <p class="catatan-kecil">*Dihitung dari hari ini bila akad dibuat hari ini. Total tebus mengasumsikan pokok tidak dicicil. Angka final tercantum di nota gadai setelah taksiran analis.</p>
            </div>
        </div>
        @endif

        @if ($tataCara)
            <h3 class="sub-judul">Tata cara praktik, dari pengajuan sampai pelunasan</h3>
            <ol class="lini">
                @foreach ($tataCara as $t)
                    <li data-muncul><b>{{ $t['judul'] }}</b><p>{{ $isi($t['isi']) }}</p></li>
                @endforeach
            </ol>
        @endif
    </div>
</section>

<script type="application/json" id="data-produk">{!! json_encode($dataProduk, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
