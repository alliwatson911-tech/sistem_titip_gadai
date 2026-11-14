@extends('layouts.panel')
@section('judul', $g->no_sbg)
@section('isi')
    @use('App\Support\Rupiah')
    @use('App\Enums\LoanStatus', 'LS')
    @php
        $user = auth()->user();
        $staf = $user->role->isStaff();
        $sisa = (int) today()->diffInDays($g->jatuh_tempo, false);
        $menunggu = $pembayaran->firstWhere('status', 'menunggu_verifikasi');
        $terakhirSah = $pembayaran->firstWhere('status', 'sah');
    @endphp
    <p class="sub"><a href="{{ route('panel.gadai.index') }}">&larr; Semua gadai</a></p>
    <h1><span class="mono">{{ $g->no_sbg }}</span> <span class="lencana lencana--{{ $g->status->nada() }}">{{ $g->status->label() }}</span></h1>
    <div class="bar-aksi"><a class="btn btn--kecil" href="{{ route('panel.gadai.nota', $g) }}" target="_blank" rel="noopener">Cetak nota gadai</a></div>

    <dl class="ringkasan">
        @if ($staf)<div><dt>Penitip</dt><dd>{{ $g->member->nama_lengkap }}</dd></div>@endif
        <div><dt>Barang</dt><dd>{{ $g->product->nama }}</dd></div>
        <div><dt>Tanggal titip</dt><dd>{{ $g->tgl_akad->translatedFormat('j F Y') }}</dd></div>
        <div><dt>Nilai yang diterima</dt><dd>{{ Rupiah::format((int) $g->pokok_awal) }}</dd></div>
        <div><dt>Sisa nilai pokok</dt><dd>{{ Rupiah::format((int) $g->pokok_saldo) }}</dd></div>
        <div><dt>Jatuh tempo</dt><dd>{{ $g->jatuh_tempo->translatedFormat('j F Y') }}
            @if ($tagihan)<br><small style="color:{{ $sisa < 0 ? 'var(--terakota)' : 'inherit' }}">{{ $sisa < 0 ? 'terlambat '.abs($sisa).' hari' : ($sisa === 0 ? 'hari ini' : $sisa.' hari lagi') }}</small>@endif</dd></div>
        <div><dt>Perpanjangan ke</dt><dd>{{ $g->perpanjangan_ke }}</dd></div>
        @if ($g->disbursement)<div><dt>Dana dicairkan</dt><dd>{{ Rupiah::format((int) $g->disbursement->jumlah) }} ({{ $g->disbursement->metode }})</dd></div>@endif
    </dl>

    {{-- Foto Barang Jaminan --}}
    @php $barangJaminan = $g->collaterals()->with('photos')->get(); @endphp
    @if ($barangJaminan->isNotEmpty())
        <h2>Foto &amp; Rincian Barang Jaminan</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            @foreach ($barangJaminan as $bj)
                @foreach ($bj->photos as $foto)
                    <div style="background: #fff; border: 1.5px solid var(--garis); border-radius: var(--radius-m); overflow: hidden;">
                        <a href="{{ route('panel.berkas.foto', $foto) }}" target="_blank" rel="noopener">
                            <img src="{{ route('panel.berkas.foto', $foto) }}" alt="Foto Barang Titipan" style="width: 100%; height: 150px; object-fit: cover; display: block;">
                        </a>
                        <div style="padding: .5rem .75rem; font-size: .8rem; color: var(--muted);">
                            {{ $bj->kategori ?? 'Barang Titipan' }}
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif

    @if ($g->status === LS::SiapLelang)
        <p class="kabar kabar--galat"><b>Barang masuk proses lelang.</b> Anggota masih dapat menebus, memperpanjang, atau mencicil sebelum lelang dilaksanakan.@if ($g->auction?->tgl_lelang) Lelang dijadwalkan pada {{ $g->auction->tgl_lelang->translatedFormat('j F Y') }}.@endif</p>
    @elseif ($g->status === LS::Tenggang)
        <p class="kabar kabar--galat">Gadai lewat jatuh tempo dan berada dalam masa tenggang. Sewa modal tetap berjalan. Segera bayar sebelum {{ $g->tenggang_sampai?->translatedFormat('j F Y') }}.</p>
    @endif

    {{-- ===== Tagihan hari ini ===== --}}
    @if ($tagihan)
        <h2>Tagihan hari ini ({{ \Illuminate\Support\Carbon::parse($tagihan['tanggal'])->translatedFormat('j F Y') }})</h2>
        <div class="tabel-bungkus"><table class="tabel" style="min-width:30rem">
            <thead><tr><th></th><th class="angka">Perpanjang</th><th class="angka">Cicil (minimum)</th><th class="angka">Tebus</th></tr></thead>
            <tbody>
                <tr><td>Setoran pokok</td><td class="angka">—</td><td class="angka">{{ Rupiah::format($tagihan['cicil']['minimum']) }}+</td><td class="angka">{{ Rupiah::format($tagihan['saldo']) }}</td></tr>
                <tr><td>Jasa penitipan</td><td class="angka">{{ Rupiah::format($tagihan['perpanjang']['sewa_modal']) }}</td><td class="angka">{{ Rupiah::format($tagihan['cicil']['sewa_modal']) }}</td><td class="angka">{{ Rupiah::format($tagihan['tebus']['sewa_modal']) }}</td></tr>
                <tr><td>Administrasi</td><td class="angka">{{ Rupiah::format($tagihan['administrasi']) }}</td><td class="angka">{{ Rupiah::format($tagihan['administrasi']) }}</td><td class="angka">{{ Rupiah::format($tagihan['administrasi']) }}</td></tr>
                @if ($tagihan['denda'] > 0)<tr><td>Denda</td><td class="angka">{{ Rupiah::format($tagihan['denda']) }}</td><td class="angka">{{ Rupiah::format($tagihan['denda']) }}</td><td class="angka">{{ Rupiah::format($tagihan['denda']) }}</td></tr>@endif
                <tr><td><b>Total</b></td><td class="angka"><b>{{ $tagihan['perpanjang']['boleh'] ? Rupiah::format($tagihan['perpanjang']['total']) : 'tidak tersedia' }}</b></td><td class="angka"><b>{{ $tagihan['cicil']['boleh'] ? Rupiah::format($tagihan['cicil']['biaya_periode'] + $tagihan['cicil']['minimum']).'+' : 'tidak tersedia' }}</b></td><td class="angka"><b>{{ Rupiah::format($tagihan['tebus']['total']) }}</b></td></tr>
            </tbody>
        </table></div>
        <p class="catatan-audit">Perpanjang atau cicil memajukan jatuh tempo ke {{ \Illuminate\Support\Carbon::parse($tagihan['jatuh_tempo_baru'])->translatedFormat('j F Y') }}. @if ($tagihan['terlambat_hari'] > 0) Terlambat {{ $tagihan['terlambat_hari'] }} hari; sewa modal harian ikut dihitung. @endif
            @unless ($tagihan['perpanjang']['boleh']) <br>Perpanjang: {{ $tagihan['perpanjang']['alasan'] }} @endunless</p>
    @endif

    @if ($tagihan && $tagihan['selisih_taksir'] > 0)
        <p class="kabar kabar--galat">Taksiran ulang menurun. Plafon baru {{ Rupiah::format($tagihan['plafon_ulang']) }}; <b>selisih {{ Rupiah::format($tagihan['selisih_taksir']) }}</b> wajib disetor lewat cicil sebelum perpanjangan. Tebus tetap dapat dilakukan.</p>
    @endif

    {{-- ===== Hubungi anggota lewat WhatsApp (tautan manual, tanpa penyedia pihak ketiga) ===== --}}
    @if ($tagihan && $staf && ($user->hasPermission('pembayaran.catat') || $user->hasPermission('lelang.kelola')) && filled($g->member->telepon))
        @php
            $wa = preg_replace('/\D/', '', (string) $g->member->telepon);
            $wa = str_starts_with($wa, '0') ? '62'.substr($wa, 1) : $wa;
            $pesan = "Halo {$g->member->nama_lengkap}, ini petugas loket PERSIDEN HP Titip Gadai. Penitipan {$g->product->nama} (nota {$g->no_sbg}) jatuh tempo ".$g->jatuh_tempo->translatedFormat('j F Y').'. '
                .($g->status === LS::SiapLelang ? 'Barang Anda masuk proses lelang; Anda masih dapat menebus atau memperpanjang sebelum lelang. ' : '')
                .'Silakan datang ke loket cabang untuk memperpanjang, mencicil, atau menebus. Kami tidak pernah meminta kata sandi atau kode.';
        @endphp
        <p class="bar-aksi"><a class="btn btn--kecil" href="https://wa.me/{{ $wa }}?text={{ rawurlencode($pesan) }}" target="_blank" rel="noopener">Hubungi penitip via WhatsApp</a> <span class="catatan-audit">Membuka WhatsApp dengan pesan siap kirim; Anda yang menekan kirim.</span></p>
    @endif

    {{-- ===== Taksir ulang (analis) ===== --}}
    @if ($tagihan && in_array($g->status, [LS::Aktif, LS::Tenggang], true))
        @can('taksiran.buat')
            <form class="form" method="post" action="{{ route('panel.gadai.taksir-ulang', $g) }}" style="margin-top:1.5rem">
                @csrf
                <h2>Taksir ulang barang</h2>
                <p class="bantuan">Dipakai saat perpanjang atau bila nilai barang berubah. Plafon dihitung dari taksiran x batas pinjaman produk. Bila saldo pokok melebihi plafon, anggota wajib menyetor selisihnya lewat cicil sebelum memperpanjang.@if ($g->plafon_ulang !== null) Plafon taksir ulang terakhir: {{ Rupiah::format((int) $g->plafon_ulang) }}.@endif</p>
                <div class="baris-2">
                    <x-bidang nama="nilai_pasar" label="Nilai pasar saat ini (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" />
                    <x-bidang nama="nilai_taksiran" label="Nilai taksiran ulang (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" />
                </div>
                <div class="bidang"><label for="f-catatan-tu">Catatan</label><textarea id="f-catatan-tu" name="catatan" maxlength="1000">{{ old('catatan') }}</textarea></div>
                <button class="btn btn--utama" type="submit">Simpan taksir ulang</button>
            </form>
        @endcan
    @endif

    {{-- ===== Catat pembayaran (teller) ===== --}}
    @if ($tagihan)
        @can('pembayaran.catat')
            @if ($menunggu)
                <p class="kabar kabar--galat">Ada pembayaran transfer yang menunggu verifikasi ({{ $menunggu->no_kwitansi }}). Selesaikan dulu sebelum mencatat pembayaran baru.</p>
            @else
                <form class="form" method="post" action="{{ route('panel.pembayaran.store', $g) }}" enctype="multipart/form-data" style="margin-top:1.5rem">
                    @csrf
                    <h2>Catat pembayaran</h2>
                    <div class="baris-2">
                        <div class="bidang"><label for="f-tipe">Jenis</label>
                            <select id="f-tipe" name="tipe" required><option value="">Pilih</option>
                                @foreach (['perpanjangan' => 'Perpanjang', 'cicil' => 'Cicil', 'tebus' => 'Tebus'] as $k => $l)<option value="{{ $k }}" @selected(old('tipe') === $k)>{{ $l }}</option>@endforeach
                            </select>@error('tipe')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                        <div class="bidang"><label for="f-metode">Metode</label>
                            <select id="f-metode" name="metode" required><option value="tunai" @selected(old('metode', 'tunai') === 'tunai')>Tunai di teller</option><option value="transfer" @selected(old('metode') === 'transfer')>Transfer manual</option></select></div>
                    </div>
                    <x-bidang nama="pokok" label="Setoran pokok (hanya untuk cicil, Rp)" inputmode="numeric" autocomplete="off" />
                    <div class="bidang"><label for="f-bukti">Bukti transfer (wajib untuk transfer)</label><input id="f-bukti" type="file" name="bukti" accept="image/jpeg,image/png,image/webp">@error('bukti')<p class="galat" role="alert">{{ $message }}</p>@enderror
                        <p class="bantuan">Jumlah tagihan dihitung sistem. Transfer baru berlaku setelah diverifikasi petugas lain.</p></div>
                    <button class="btn btn--utama" type="submit">Catat pembayaran</button>
                </form>
            @endif
        @endcan
    @endif

    {{-- ===== Pengeluaran barang ===== --}}
    @if ($g->status === LS::Ditebus)
        @if ($g->redemption)
            <h2>Barang dikeluarkan</h2>
            <dl class="ringkasan">
                <div><dt>Penebus</dt><dd>{{ $g->redemption->nama_penebus }} ({{ $g->redemption->penebus_tipe }})</dd></div>
                <div><dt>Waktu</dt><dd>{{ $g->redemption->dikeluarkan_at->translatedFormat('j F Y H:i') }}</dd></div>
                <div><dt>Surat kuasa</dt><dd>@if ($g->redemption->surat_kuasa_path && $staf)<a href="{{ route('panel.berkas.kuasa', $g) }}" target="_blank" rel="noopener">Buka berkas</a>@else{{ $g->redemption->surat_kuasa_path ? 'Terlampir' : '—' }}@endif</dd></div>
            </dl>
        @else
            @can('jaminan.keluarkan')
                <form class="form" method="post" action="{{ route('panel.penebusan.store', $g) }}" enctype="multipart/form-data" style="margin-top:1.5rem">
                    @csrf
                    <h2>Keluarkan barang</h2>
                    <div class="bidang"><label for="f-penebus_tipe">Penebus</label>
                        <select id="f-penebus_tipe" name="penebus_tipe"><option value="penggadai" @selected(old('penebus_tipe') === 'penggadai')>Penggadai sendiri</option><option value="kuasa" @selected(old('penebus_tipe') === 'kuasa')>Orang lain (surat kuasa bermaterai)</option></select></div>
                    <div class="baris-2">
                        <x-bidang nama="nama_penebus" label="Nama penebus" :nilai="$g->member->nama_lengkap" :wajib="true" />
                        <x-bidang nama="nik_penebus" label="NIK penebus (wajib bila kuasa)" inputmode="numeric" maxlength="16" autocomplete="off" />
                    </div>
                    <div class="bidang"><label for="f-surat_kuasa">Surat kuasa (JPG/PNG/WebP/PDF, maks. 4 MB)</label><input id="f-surat_kuasa" type="file" name="surat_kuasa" accept="image/jpeg,image/png,image/webp,application/pdf">@error('surat_kuasa')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                    <label class="centang"><input type="checkbox" name="nota_asli_diperiksa" value="1" @checked(old('nota_asli_diperiksa'))> Nota gadai asli dan KTP asli penggadai (dan penebus) sudah diperiksa.</label>
                    @error('nota_asli_diperiksa')<p class="galat" role="alert">{{ $message }}</p>@enderror
                    <button class="btn btn--utama" type="submit">Serahkan barang</button>
                </form>
            @endcan
        @endif
    @endif

    {{-- ===== Lelang ===== --}}
    @if ($g->auction && $staf && in_array($g->auction->status, ['laku', 'dijadwalkan', 'batal'], true) && $g->status !== LS::Aktif)
        <h2>Lelang</h2>
        <dl class="ringkasan">
            <div><dt>Status lelang</dt><dd>{{ $g->auction->status }}</dd></div>
            <div><dt>Diberitahukan</dt><dd>{{ $g->auction->diberitahukan_at?->translatedFormat('j M Y') }}</dd></div>
            <div><dt>Tanggal lelang</dt><dd>{{ $g->auction->tgl_lelang?->translatedFormat('j F Y') ?? 'belum dijadwalkan' }}</dd></div>
            @if ($g->auction->status === 'laku')
                <div><dt>Harga laku</dt><dd>{{ Rupiah::format((int) $g->auction->harga_laku) }}</dd></div>
                <div><dt>Biaya lelang</dt><dd>{{ Rupiah::format((int) $g->auction->biaya_lelang) }}</dd></div>
                <div><dt>{{ $g->auction->kelebihan > 0 ? 'Kelebihan (hak anggota)' : 'Kekurangan (tidak ditagihkan)' }}</dt><dd>{{ Rupiah::format(abs((int) $g->auction->kelebihan)) }} @if ($g->auction->kelebihan > 0) — {{ $g->auction->kelebihan_status }}@endif</dd></div>
            @endif
        </dl>
        @can('lelang.catat')<p class="catatan-audit"><a href="{{ route('panel.lelang.index') }}">Kelola di halaman Lelang</a></p>@endcan
    @endif

    {{-- ===== Riwayat pembayaran ===== --}}
    <h2>Riwayat pembayaran</h2>
    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Kwitansi</th><th>Jenis</th><th class="angka">Total</th><th>Metode</th><th>Status</th><th>Tanggal</th>@if ($staf)<th>Tindakan</th>@endif</tr></thead>
        <tbody>
        @forelse ($pembayaran as $p)
            <tr>
                <td class="mono">{{ $p->no_kwitansi }}</td>
                <td>{{ ucfirst($p->tipe) }}@if ($p->tipe === 'cicil') <small>(pokok {{ Rupiah::format((int) $p->pokok_dibayar) }})</small>@endif</td>
                <td class="angka">{{ Rupiah::format((int) $p->total) }}</td>
                <td>{{ $p->metode }}</td>
                <td><span class="lencana lencana--{{ ['sah' => 'sukses', 'menunggu_verifikasi' => 'proses', 'batal' => 'netral'][$p->status] }}">{{ str_replace('_', ' ', $p->status) }}</span>@if ($p->alasan)<br><small>{{ $p->alasan }}</small>@endif</td>
                <td>{{ $p->dibayar_at->translatedFormat('j M Y H:i') }}</td>
                @if ($staf)
                    <td>
                        @can('pembayaran.catat')
                            @if (in_array($p->status, ['sah', 'batal'])) <a href="{{ route('panel.pembayaran.kwitansi', $p) }}" target="_blank" rel="noopener">Kwitansi</a> @endif
                            @if ($p->bukti_path) <a href="{{ route('panel.pembayaran.bukti', $p) }}" target="_blank" rel="noopener">Bukti</a> @endif
                        @endcan
                        @if ($p->status === 'menunggu_verifikasi')
                            @can('pembayaran.verifikasi')
                                @if ($p->teller_id === $user->id)
                                    <small>Menunggu petugas lain</small>
                                @else
                                    <form method="post" action="{{ route('panel.pembayaran.verifikasi', $p) }}" style="margin-top:.4rem">@csrf
                                        <input name="alasan" placeholder="Alasan bila ditolak" maxlength="255" style="min-height:36px;width:100%;margin-bottom:.3rem">
                                        <button class="btn btn--kecil btn--utama" name="keputusan" value="setuju" type="submit">Setujui</button>
                                        <button class="btn btn--kecil btn--bahaya" name="keputusan" value="tolak" type="submit">Tolak</button></form>
                                @endif
                            @endcan
                        @endif
                        @if ($p->status === 'sah' && $terakhirSah && $terakhirSah->id === $p->id)
                            @can('pembayaran.batalkan')
                                <form method="post" action="{{ route('panel.pembayaran.batalkan', $p) }}" style="margin-top:.4rem" data-konfirmasi="Balikkan pembayaran ini?">@csrf
                                    <input name="alasan" placeholder="Alasan pembalikan" maxlength="255" required style="min-height:36px;width:100%;margin-bottom:.3rem">
                                    <button class="btn btn--kecil btn--bahaya" type="submit">Balikkan</button></form>
                            @endcan
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="7" class="kosong">Belum ada pembayaran.</td></tr>
        @endforelse
        </tbody>
    </table></div>

    @if ($staf)
        @php $pemberitahuan = $g->notices()->latest('id')->get(); @endphp
        <h2>Pemberitahuan ke anggota (email)</h2>
        <div class="tabel-bungkus"><table class="tabel">
            <thead><tr><th>Jenis</th><th>Dasar tanggal</th><th>Status</th><th>Waktu kirim</th></tr></thead>
            <tbody>
            @forelse ($pemberitahuan as $n)
                <tr><td>{{ \App\Models\LoanNotice::JENIS[$n->jenis] ?? $n->jenis }}</td><td>{{ $n->kunci }}</td>
                    <td><span class="lencana lencana--{{ ['terkirim' => 'sukses', 'gagal' => 'gagal', 'dilewati' => 'netral'][$n->status] }}">{{ $n->status }}</span>@if ($n->catatan)<br><small>{{ $n->catatan }}</small>@endif</td>
                    <td>{{ $n->sent_at?->translatedFormat('j M Y H:i') ?? '—' }}</td></tr>
            @empty
                <tr><td colspan="4" class="kosong">Belum ada pemberitahuan yang dikirim.</td></tr>
            @endforelse
            </tbody>
        </table></div>

        <h2>Riwayat status</h2>
        <div class="tabel-bungkus"><table class="tabel">
            <thead><tr><th>Waktu</th><th>Dari</th><th>Ke</th><th>Oleh</th><th>Keterangan</th></tr></thead>
            <tbody>@foreach ($g->histories->sortByDesc('id') as $h)
                <tr><td>{{ $h->created_at->translatedFormat('j M Y H:i') }}</td><td>{{ $h->dari ?: '—' }}</td><td>{{ $h->ke }}</td><td>{{ $h->pelaku?->name ?? 'Sistem' }}</td><td>{{ $h->alasan }}</td></tr>
            @endforeach</tbody>
        </table></div>
    @endif
@endsection
