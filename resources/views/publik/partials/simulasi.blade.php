@php $tataCara = $konten['tata_cara'] ?? []; @endphp
<section class="bagian" id="simulasi" data-spy="simulasi" aria-labelledby="judul-simulasi">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">Simulasi Jasa</p>
            <div>
                <h2 id="judul-simulasi">Simulasi Jasa Penitipan Barang</h2>
                <p class="pengantar" style="margin-top:1rem">Pilih jenis barang, tentukan nilai yang diterima, dan pilih durasi 1 minggu atau 2 minggu. Kami tampilkan rincian jasa penitipan per periode dan perkiraan total bila diperpanjang.</p>
            </div>
        </div>

        @if ($produk->isEmpty())
            <p>Simulasi akan tersedia setelah data layanan diisi.</p>
        @else
        <div class="sim">
            <form id="sim-form" class="panel-form" style="padding: 2rem;" data-url="{{ route('simulasi') }}" novalidate>
                <div class="bidang">
                    <label for="sim-produk">Jenis Barang Titipan</label>
                    <select id="sim-produk" name="produk">
                        @foreach ($produk as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bidang">
                    <label for="sim-taksiran">Perkiraan Nilai Taksiran Barang</label>
                    <div class="awalan"><span aria-hidden="true">Rp</span><input id="sim-taksiran" type="text" inputmode="numeric" autocomplete="off" value="3.000.000"></div>
                    <p class="catatan-kecil" style="margin:.4rem 0 0">Perkiraan harga wajar pasar barang Anda. Nilai pasti disepakati di loket cabang setelah pemeriksaan fisik.</p>
                </div>

                <div class="bidang">
                    <label for="sim-ltv">Rasio Nilai yang Diterima: <output id="sim-ltv-nilai" for="sim-ltv">80%</output></label>
                    <input id="sim-ltv" type="range" min="1000" max="8000" step="500" value="8000">
                    <div class="rentang-info"><span id="sim-batas"></span></div>
                </div>

                <fieldset class="bidang">
                    <legend>Durasi Penitipan</legend>

                    <!-- Pilihan Satuan Hitungan: Mingguan (Utama Klien: 1 & 2 Minggu) -->
                    <div class="satuan-titip-tabs" role="tablist" aria-label="Pilihan durasi penitipan">
                        <button type="button" class="tab-satuan aktif" data-satuan="mingguan" role="tab" aria-selected="true">Opsi Mingguan (1 &ndash; 2 Minggu)</button>
                        <button type="button" class="tab-satuan" data-satuan="harian" role="tab" aria-selected="false">Harian</button>
                        <button type="button" class="tab-satuan" data-satuan="bulanan" role="tab" aria-selected="false">Dengan Perpanjangan</button>
                    </div>

                    <!-- Durasi Mingguan (Standar Klien PERSIDEN HP) -->
                    <div class="pilihan-durasi" id="durasi-mingguan">
                        <div class="segmen" role="radiogroup" aria-label="Pilihan minggu titip">
                            <input type="radio" name="durasi_minggu" id="minggu-1" value="7" checked>
                            <label for="minggu-1"><span>1 Minggu<br><small>7 hari kalender</small></span></label>

                            <input type="radio" name="durasi_minggu" id="minggu-2" value="14">
                            <label for="minggu-2"><span>2 Minggu<br><small>14 hari kalender</small></span></label>

                            <input type="radio" name="durasi_minggu" id="minggu-3" value="21">
                            <label for="minggu-3"><span>3 Minggu<br><small>perpanjangan</small></span></label>

                            <input type="radio" name="durasi_minggu" id="minggu-4" value="28">
                            <label for="minggu-4"><span>4 Minggu<br><small>perpanjangan</small></span></label>
                        </div>
                    </div>

                    <!-- Durasi Harian -->
                    <div class="pilihan-durasi" id="durasi-harian" hidden>
                        <div class="segmen" role="radiogroup" aria-label="Pilihan hari titip">
                            <input type="radio" name="durasi_hari" id="hari-1" value="1">
                            <label for="hari-1"><span>1 Hari<br><small>titip kilat</small></span></label>

                            <input type="radio" name="durasi_hari" id="hari-3" value="3">
                            <label for="hari-3"><span>3 Hari<br><small>titip singkat</small></span></label>

                            <input type="radio" name="durasi_hari" id="hari-7" value="7">
                            <label for="hari-7"><span>7 Hari<br><small>1 pekan</small></span></label>

                            <input type="radio" name="durasi_hari" id="hari-14" value="14">
                            <label for="hari-14"><span>14 Hari<br><small>2 pekan</small></span></label>
                        </div>
                        <div class="slider-hari-wrap" style="margin-top: .85rem;">
                            <label for="sim-hari-custom" style="display:flex; justify-content:space-between; font-size:.82rem; margin:0 0 .3rem;">
                                <span>Atur durasi hari kustom:</span>
                                <b id="sim-hari-custom-nilai" style="color:var(--kuningan-tua);">7 hari</b>
                            </label>
                            <input id="sim-hari-custom" type="range" min="1" max="28" step="1" value="7">
                        </div>
                    </div>

                    <!-- Durasi dengan Perpanjangan Bertahap -->
                    <div class="pilihan-durasi" id="durasi-bulanan" hidden>
                        <div class="segmen" role="radiogroup" aria-label="Lama titip dengan perpanjangan">
                            @for ($n = 1; $n <= $maksPeriode; $n++)
                                <input type="radio" name="periode" id="periode-{{ $n }}" value="{{ $n }}" @checked($n === 1)>
                                <label for="periode-{{ $n }}"><span>{{ $n * 14 }} hari<br><small>{{ $n === 1 ? '1 periode' : ($n - 1).'x diperpanjang' }}</small></span></label>
                            @endfor
                        </div>
                    </div>
                </fieldset>
                <noscript><p class="galat">Simulasi memerlukan JavaScript aktif di browser Anda.</p></noscript>
            </form>

            <div class="nota-bungkus">
                <div class="nota" id="nota" aria-live="polite">
                    <h3><span>Nota Simulasi</span><span>PERSIDEN HP</span></h3>
                    <div class="nota-baris"><span>Jenis barang</span><b id="n-produk">&mdash;</b></div>
                    <div class="nota-baris"><span>Perkiraan taksiran</span><b id="n-taksiran">&mdash;</b></div>
                    <div class="nota-baris"><span>Rasio taksiran</span><b id="n-ltv">&mdash;</b></div>
                    <div class="nota-baris"><span>Nilai yang diterima</span><b id="n-pokok">&mdash;</b></div>
                    <div class="nota-baris"><span>Durasi penitipan</span><b id="n-hari">&mdash;</b></div>
                    <div class="nota-baris"><span>Jasa penitipan</span><b id="n-sewa">&mdash;</b></div>
                    <div class="nota-baris"><span>Biaya administrasi</span><b id="n-admin">&mdash;</b></div>
                    <div class="nota-baris"><span>Perkiraan jatuh tempo*</span><b id="n-jatuh">&mdash;</b></div>
                    <div class="nota-total"><span>Total pelunasan</span><b id="n-total">&mdash;</b></div>
                    <div id="n-rincian-bungkus" hidden>
                        <table class="nota-rincian">
                            <thead><tr><th>Periode</th><th>Jasa Penitipan</th><th>Administrasi</th></tr></thead>
                            <tbody id="n-rincian"></tbody>
                        </table>
                    </div>
                    <p class="nota-pesan" id="n-pesan" role="status"></p>
                </div>
                <p class="catatan-kecil">*Dihitung dari hari ini jika transaksi dibuat hari ini. Saat diperpanjang, jasa penitipan periode berjalan dilunasi terlebih dahulu. Angka pasti tercantum di Nota Penitipan fisik di loket cabang.</p>
            </div>
        </div>
        @endif

        @if ($tataCara)
            <h3 class="sub-judul">Tata cara penitipan, dari loket sampai pelunasan</h3>
            <ol class="lini">
                @foreach ($tataCara as $t)
                    <li data-muncul><b>{{ $t['judul'] }}</b><p>{{ $isi($t['isi']) }}</p></li>
                @endforeach
            </ol>
        @endif
    </div>
</section>

<script type="application/json" id="data-produk">{!! json_encode($dataProduk, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
