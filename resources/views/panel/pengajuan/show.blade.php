@extends('layouts.panel')
@section('judul', $a->kode)
@section('isi')
    @use('App\Support\Rupiah')
    @use('App\Enums\ApplicationStatus', 'S')
    @php
        $user = auth()->user();
        $staf = $user->role->isStaff();
        $milikSaya = $a->created_by === $user->id;
    @endphp
    <p class="sub"><a href="{{ route('panel.pengajuan.index') }}">&larr; Semua pengajuan</a></p>
    <h1><span class="mono">{{ $a->kode }}</span> <span class="lencana lencana--{{ $a->status->nada() }}">{{ $a->status->label() }}</span></h1>

    <dl class="ringkasan">
        @if ($staf)<div><dt>Anggota</dt><dd>{{ $a->member->nama_lengkap }}</dd></div>@endif
        <div><dt>Jenis barang</dt><dd>{{ $a->product->nama }}</dd></div>
        <div><dt>Tenor</dt><dd>{{ $a->tenor_hari }} hari</dd></div>
        <div><dt>Pinjaman diminta</dt><dd>{{ Rupiah::format($a->pinjaman_diminta) }}</dd></div>
        <div><dt>Pinjaman disetujui</dt><dd>{{ $a->pinjaman_disetujui ? Rupiah::format($a->pinjaman_disetujui) : '—' }}</dd></div>
        <div><dt>Batas bawa barang</dt><dd>{{ $a->batas_bawa_barang?->translatedFormat('j F Y') ?? '—' }}</dd></div>
        @if ($staf)<div><dt>Dibuat oleh</dt><dd>{{ $a->pembuat?->name }}</dd></div>@endif
    </dl>
    @if ($a->catatan_member)<p><b>Catatan anggota:</b> {{ $a->catatan_member }}</p>@endif

    @if ($a->status === S::MenungguBarang && ! $staf)
        <p class="kabar">Bawa barang beserta kelengkapannya dan KTP asli ke cabang {{ $a->branch->nama }} sebelum {{ $a->batas_bawa_barang->translatedFormat('j F Y') }}. Setelah lewat, pengajuan kedaluwarsa otomatis.</p>
    @endif
    @if ($a->status === S::Disetujui)
        <p class="kabar">Pengajuan disetujui ({{ Rupiah::format((int) $a->pinjaman_disetujui) }}) dan siap dicairkan oleh teller di cabang.</p>
        @can('pencairan.catat')
            <form class="form" method="post" action="{{ route('panel.gadai.cairkan', $a) }}">
                @csrf
                <h2>Cairkan dana</h2>
                <p class="bantuan">Pastikan penggadai hadir dan identitasnya sudah dicocokkan. Dana diserahkan sebesar pokok; sewa modal dan administrasi dibayar saat perpanjang, cicil, atau tebus.</p>
                <div class="baris-2">
                    <div class="bidang"><label for="f-metode">Metode</label><select id="f-metode" name="metode"><option value="tunai">Tunai</option><option value="transfer">Transfer</option></select></div>
                    <x-bidang nama="referensi" label="No. referensi (transfer)" maxlength="100" />
                </div>
                <button class="btn btn--utama" type="submit">Cairkan {{ Rupiah::format((int) $a->pinjaman_disetujui) }}</button>
            </form>
        @endcan
    @endif
    @if ($a->status === S::Dicairkan)
        @php $pinjam = \App\Models\Loan::where('application_id', $a->id)->first(); @endphp
        @if ($pinjam)<p class="kabar">Dana sudah dicairkan. <a href="{{ route('panel.gadai.show', $pinjam) }}">Lihat gadai {{ $pinjam->no_sbg }}</a></p>@endif
    @endif

    {{-- ===== Barang jaminan (staf) ===== --}}
    @if ($staf && $a->collaterals->isNotEmpty())
        <h2>Barang jaminan</h2>
        @foreach ($a->collaterals as $c)
            <dl class="ringkasan">
                <div><dt>Barang</dt><dd>{{ $c->merek }} {{ $c->model }}</dd></div>
                <div><dt>No. segel</dt><dd class="mono">{{ $c->no_segel }}</dd></div>
                <div><dt>Lokasi simpan</dt><dd>{{ $c->lokasi_simpan ?: '—' }}</dd></div>
                <div><dt>IMEI / serial</dt><dd class="mono">{{ $c->imei_serial_enc ? '••••'.substr($c->imei_serial_enc, -4) : '—' }}</dd></div>
                <div><dt>Kelengkapan</dt><dd>{{ $c->kelengkapan ?: '—' }}</dd></div>
                <div><dt>Status barang</dt><dd>{{ $c->status }}</dd></div>
                <div style="grid-column:1/-1"><dt>Kondisi</dt><dd>{{ $c->kondisi ?: '—' }}</dd></div>
            </dl>
            @if ($c->photos->isNotEmpty())
                <div class="foto-baris">
                    @foreach ($c->photos as $f)
                        <a href="{{ route('panel.berkas.foto', $f) }}" target="_blank" rel="noopener"><img src="{{ route('panel.berkas.foto', $f) }}" alt="Foto {{ $f->jenis }} barang" loading="lazy"><span>{{ $f->jenis }}</span></a>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

    {{-- ===== Taksiran ===== --}}
    @if ($taksiran)
        <h2>Hasil taksiran</h2>
        @if ($staf)
            <dl class="ringkasan">
                <div><dt>Nilai pasar</dt><dd>{{ Rupiah::format($taksiran->nilai_pasar) }}</dd></div>
                <div><dt>Nilai taksiran</dt><dd>{{ Rupiah::format($taksiran->nilai_taksiran) }}</dd></div>
                <div><dt>Plafon ({{ $taksiran->ltv_diterapkan_bp / 100 }}%)</dt><dd>{{ Rupiah::format($taksiran->plafon_maks) }}</dd></div>
                <div><dt>Skor kelayakan</dt><dd>{{ $taksiran->skor_kelayakan }} / 100</dd></div>
                <div><dt>Rekomendasi</dt><dd>{{ ucfirst($taksiran->rekomendasi) }}</dd></div>
                <div><dt>Analis</dt><dd>{{ $taksiran->analis?->name }}</dd></div>
                @if ($taksiran->catatan)<div style="grid-column:1/-1"><dt>Catatan</dt><dd>{{ $taksiran->catatan }}</dd></div>@endif
            </dl>
        @else
            <dl class="ringkasan"><div><dt>Plafon hasil taksiran</dt><dd>{{ Rupiah::format($taksiran->plafon_maks) }}</dd></div></dl>
        @endif
    @endif

    @if ($staf && $a->approvals->isNotEmpty())
        <h2>Riwayat keputusan</h2>
        <div class="tabel-bungkus"><table class="tabel">
            <thead><tr><th>Tingkat</th><th>Keputusan</th><th>Oleh</th><th>Waktu</th><th>Catatan</th></tr></thead>
            <tbody>@foreach ($a->approvals as $p)
                <tr><td>{{ ucfirst($p->level) }}</td><td>{{ ucfirst($p->keputusan) }}</td><td>{{ $p->pengambilKeputusan?->name }}</td><td>{{ $p->decided_at->translatedFormat('j M Y H:i') }}</td><td>{{ $p->catatan ?: '—' }}</td></tr>
            @endforeach</tbody>
        </table></div>
    @endif

    {{-- ===== Tindakan ===== --}}
    @if ($a->status === S::MenungguBarang)
        @can('jaminan.terima')
            <form class="form" method="post" action="{{ route('panel.pengajuan.terima', $a) }}" enctype="multipart/form-data" style="margin-top:1.5rem">
                @csrf
                <h2>Terima barang jaminan</h2>
                <label class="centang"><input type="checkbox" name="identitas_dicocokkan" value="1" @checked(old('identitas_dicocokkan'))> Identitas penggadai sudah dicocokkan dengan KTP asli.</label>
                @error('identitas_dicocokkan')<p class="galat" role="alert">{{ $message }}</p>@enderror
                <div class="baris-2">
                    <x-bidang nama="merek" label="Merek" :wajib="true" />
                    <x-bidang nama="model" label="Model" :wajib="true" />
                    <x-bidang nama="imei_serial" label="IMEI / nomor seri" autocomplete="off" bantuan="Dipakai untuk mendeteksi barang yang sama dititipkan dua kali." />
                    <x-bidang nama="no_segel" label="Nomor segel" :wajib="true" />
                    <x-bidang nama="kelengkapan" label="Kelengkapan" bantuan="Dus, pengisi daya, kabel, aksesori." />
                    <x-bidang nama="lokasi_simpan" label="Lokasi simpan" bantuan="Mis. brankas 2, rak C." />
                </div>
                <div class="bidang"><label for="f-kondisi">Kondisi fisik</label><textarea id="f-kondisi" name="kondisi" maxlength="1000">{{ old('kondisi') }}</textarea>@error('kondisi')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                <fieldset class="bidang" style="border:0;padding:0"><legend>Foto barang (JPG/PNG/WebP, maks. 4 MB)</legend>
                    <div class="baris-2">
                        @foreach (['depan' => 'Depan', 'belakang' => 'Belakang', 'serial' => 'Nomor seri', 'segel' => 'Segel terpasang'] as $k => $l)
                            <div><label for="foto-{{ $k }}">{{ $l }}</label><input id="foto-{{ $k }}" type="file" name="foto[{{ $k }}]" accept="image/jpeg,image/png,image/webp">@error("foto.$k")<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                        @endforeach
                    </div>
                </fieldset>
                <button class="btn btn--utama" type="submit">Terima dan segel barang</button>
            </form>
        @endcan
        @can('pengajuan.buat')
            <form method="post" action="{{ route('panel.pengajuan.batal', $a) }}" style="margin-top:1.2rem" data-konfirmasi="Batalkan pengajuan ini?">@csrf
                <button class="btn btn--kecil btn--bahaya" type="submit">Batalkan pengajuan</button></form>
        @endcan
    @endif

    @if ($a->status === S::BarangDiterima)
        @can('taksiran.buat')
            <form class="form" method="post" action="{{ route('panel.pengajuan.taksir', $a) }}" style="margin-top:1.5rem">
                @csrf
                <h2>Taksir dan beri rekomendasi</h2>
                <p class="bantuan">Plafon dihitung otomatis: taksiran x batas pinjaman produk ({{ $a->product->ltv_max_bp / 100 }}%), maksimal {{ Rupiah::format($a->product->pinjaman_max) }}. Pinjaman di atas batas persetujuan Manager otomatis diteruskan ke Manager.</p>
                <div class="baris-2">
                    <x-bidang nama="nilai_pasar" label="Nilai pasar barang (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" />
                    <x-bidang nama="nilai_taksiran" label="Nilai taksiran (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" />
                </div>
                <fieldset class="bidang" style="border:0;padding:0"><legend>Daftar periksa kelayakan (tiap butir 20 poin)</legend>
                    @foreach ($periksa as $k => $l)
                        <label class="centang"><input type="checkbox" name="periksa[{{ $k }}]" value="1" @checked(old("periksa.$k"))> {{ $l }}</label>
                    @endforeach
                </fieldset>
                <div class="bidang"><label for="f-rekomendasi">Rekomendasi</label>
                    <select id="f-rekomendasi" name="rekomendasi" required><option value="">Pilih</option>
                        <option value="setuju" @selected(old('rekomendasi') === 'setuju')>Setuju</option><option value="tolak" @selected(old('rekomendasi') === 'tolak')>Tolak</option></select>
                    @error('rekomendasi')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                <div class="bidang"><label for="f-catatan">Catatan (wajib bila menolak)</label><textarea id="f-catatan" name="catatan" maxlength="1000">{{ old('catatan') }}</textarea>@error('catatan')<p class="galat" role="alert">{{ $message }}</p>@enderror</div>
                <button class="btn btn--utama" type="submit">Simpan taksiran</button>
            </form>
        @endcan
    @endif

    @if ($a->status === S::MenungguManager)
        @can('persetujuan.putuskan')
            @if ($milikSaya)
                <p class="kabar kabar--galat">Anda tidak dapat memutuskan pengajuan yang Anda buat sendiri.</p>
            @else
                <form class="form" method="post" action="{{ route('panel.pengajuan.putuskan', $a) }}" style="margin-top:1.5rem">
                    @csrf
                    <h2>Keputusan persetujuan</h2>
                    <p class="bantuan">Pinjaman {{ Rupiah::format($a->pinjaman_disetujui) }} melebihi batas persetujuan cabang.</p>
                    <div class="bidang"><label for="f-keputusan">Keputusan</label>
                        <select id="f-keputusan" name="keputusan" required><option value="">Pilih</option><option value="setuju">Setujui</option><option value="tolak">Tolak</option></select></div>
                    <div class="bidang"><label for="f-catatan2">Catatan (wajib bila menolak)</label><textarea id="f-catatan2" name="catatan" maxlength="1000">{{ old('catatan') }}</textarea></div>
                    <button class="btn btn--utama" type="submit">Simpan keputusan</button>
                </form>
            @endif
        @endcan
    @endif

    @if ($a->status === S::Ditolak && $a->collaterals->where('status', 'diterima')->isNotEmpty())
        @can('jaminan.keluarkan')
            <form method="post" action="{{ route('panel.pengajuan.kembalikan', $a) }}" style="margin-top:1.5rem">@csrf
                <p class="kabar kabar--galat">Pengajuan ditolak. Barang masih tersimpan di cabang dan harus dikembalikan ke penggadai.</p>
                <button class="btn btn--utama" type="submit">Catat barang sudah dikembalikan</button></form>
        @endcan
    @endif
@endsection
