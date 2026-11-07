@extends('layouts.panel')
@section('judul', 'Ringkasan')
@section('isi')
@php
    use App\Enums\Role;
    use App\Services\RingkasanPanel;
    $r = $ringkasan;
    $mode = $r['mode'] ?? null;
    $lokasi = $user->branch?->nama ?? ($user->role === Role::Owner ? 'Semua cabang' : 'linygadai');
    $cariAnggota = $mode === 'admin';
@endphp

<section class="dash-hero" aria-labelledby="judul-dash">
    <div class="dash-hero-atas">
        @if ($cariAnggota ? Gate::allows('anggota.lihat') : Gate::allows('gadai.lihat'))
            <form class="dash-cari" role="search" method="get" action="{{ $cariAnggota ? route('panel.anggota') : route('panel.gadai.index') }}">
                @include('partials.ikon', ['n' => 'cari'])
                <label class="sr-only" for="dash-cari">{{ $cariAnggota ? 'Cari anggota' : 'Cari nomor SBG' }}</label>
                <input id="dash-cari" type="search" name="cari" maxlength="60" placeholder="{{ $cariAnggota ? 'Cari nama atau data anggota…' : 'Cari nomor SBG gadai…' }}" autocomplete="off">
                <button type="submit">Cari</button>
            </form>
        @endif
        <div class="dash-label">
            <span>{{ $user->role->label() }}</span>
            <span>{{ $lokasi }}</span>
            <span>Diperbarui {{ now()->format('H:i') }}</span>
        </div>
    </div>
    <div class="dash-judul">
        <div>
            @if ($mode)
                <h1 id="judul-dash">Panel Operasional <em>{{ $mode === 'admin' ? 'Admin' : 'Eksekutif' }}</em></h1>
                <p>Halo, {{ $user->name }}. {{ $mode === 'admin' ? 'Ringkasan keanggotaan, pengajuan, dan antrean verifikasi cabang Anda.' : 'Ringkasan portofolio gadai, arus dana, dan hal yang menunggu keputusan.' }}</p>
            @else
                <h1 id="judul-dash">Halo, {{ $user->name }}</h1>
                <p>{{ $user->role->label() }} · {{ $lokasi }}. Pekerjaan Anda hari ini ada di bawah.</p>
            @endif
        </div>
        <div class="dash-aksi">
            @can('persetujuan.putuskan')<a class="btn-pil btn-pil--emas" href="{{ route('panel.pengajuan.index', ['status' => 'menunggu_manager']) }}">@include('partials.ikon', ['n' => 'verifikasi'])Antrean persetujuan</a>@endcan
            @can('pengajuan.buat')<a class="btn-pil btn-pil--emas" href="{{ route('panel.pengajuan.create') }}">@include('partials.ikon', ['n' => 'pengajuan'])Buat pengajuan</a>@endcan
            @can('anggota.kelola')<a class="btn-pil btn-pil--emas" href="{{ route('panel.anggota', ['status' => 'calon']) }}">@include('partials.ikon', ['n' => 'anggota'])Verifikasi anggota</a>@endcan
            @canany(['lelang.kelola'])<a class="btn-pil btn-pil--lumut" href="{{ route('panel.lelang.index') }}">@include('partials.ikon', ['n' => 'lelang'])Lelang</a>@endcanany
            @can('laporan.cabang')<a class="btn-pil btn-pil--bening" href="{{ route('panel.laporan.cabang') }}">@include('partials.ikon', ['n' => 'laporan'])Laporan</a>@endcan
            @can('konten.kelola')<a class="btn-pil btn-pil--bening" href="{{ route('panel.pengumuman.index') }}">@include('partials.ikon', ['n' => 'pengumuman'])Pengumuman</a>@endcan
            @if (! $mode)<a class="btn-pil btn-pil--bening" href="{{ route('panel.gadai.index') }}">@include('partials.ikon', ['n' => 'gadai'])Gadai berjalan</a>@endif
        </div>
    </div>
</section>

@if ($r)
    <div class="kpi-grid">
        @foreach ($r['kpi'] as $k)
            <article class="kpi">
                <div class="kpi-atas">
                    <span class="kpi-ikon">@include('partials.ikon', ['n' => $k['ikon']])</span>
                    <span class="pil pil--{{ $k['nada'] }}">{{ $k['badge'] }}</span>
                </div>
                <div class="kpi-angka">{{ $k['angka'] }}@if ($k['satuan'] !== '')<small>{{ $k['satuan'] }}</small>@endif</div>
                <div class="kpi-label">{{ $k['label'] }}</div>
                <span class="kpi-chip">{{ $k['chip'] }}</span>
                <p class="kpi-catatan">{{ $k['catatan'] }}</p>
                <div class="kpi-kaki"><span>{{ $k['kiri'] }}</span><a href="{{ $k['url'] }}">{{ $k['tautan'] }} →</a></div>
            </article>
        @endforeach
    </div>

    @php
        $t = $r['tren'];
        $semua = collect($t['seri'])->pluck('nilai')->flatten();
        $maks = max(1, (int) $semua->max());
        $besar = 10 ** (strlen((string) $maks) - 1);
        $ymaks = (int) (ceil($maks / $besar) * $besar);
        $x0 = 46; $lebar = 500; $atas = 14; $tinggi = 176; $n = count($t['bulan']); $grup = $lebar / $n;
        $fmt = fn (int $v) => $t['uang'] ? implode('', RingkasanPanel::pecah($v)) : (string) $v;
        $d = $r['donat'];
        $totalIris = max(0, (int) collect($d['irisan'])->sum('nilai'));
    @endphp
    <div class="dash-baris dash-baris--tren">
        <section class="pkartu" aria-labelledby="judul-tren">
            <div class="kartu-kepala">
                <div><h2 id="judul-tren">{{ $t['judul'] }}</h2><p class="kartu-sub">{{ $t['sub'] }}</p></div>
                <span class="pil pil--sukses">6 bulan terakhir</span>
            </div>
            <ul class="legenda">
                @foreach ($t['seri'] as $s)<li><i class="w-{{ $s['warna'] }}"></i>{{ $s['nama'] }}</li>@endforeach
            </ul>
            <svg class="grafik" viewBox="0 0 560 226" role="img" aria-label="{{ $t['judul'] }}">
                <title>{{ $t['judul'] }}</title>
                @for ($g = 0; $g <= 4; $g++)
                    @php $y = $atas + $tinggi - ($tinggi * $g / 4); @endphp
                    <line class="gl" x1="{{ $x0 }}" x2="{{ $x0 + $lebar }}" y1="{{ $y }}" y2="{{ $y }}"/>
                    <text class="gt" x="{{ $x0 - 6 }}" y="{{ $y + 4 }}" text-anchor="end">{{ $fmt((int) ($ymaks * $g / 4)) }}</text>
                @endfor
                @foreach ($t['bulan'] as $i => $nama)
                    @foreach ($t['seri'] as $j => $s)
                        @php
                            $nilai = $s['nilai'][$i] ?? 0;
                            $tb = $nilai > 0 ? max(3, $tinggi * $nilai / $ymaks) : 0;
                            $bx = $x0 + $i * $grup + ($grup - 2 * 24 - 4) / 2 + $j * 28;
                        @endphp
                        <rect class="b-{{ $s['warna'] }}" x="{{ round($bx, 1) }}" y="{{ round($atas + $tinggi - $tb, 1) }}" width="24" height="{{ round($tb, 1) }}" rx="4"><title>{{ $nama }}: {{ $s['nama'] }} {{ $t['uang'] ? \App\Support\Rupiah::format($nilai) : $nilai }}</title></rect>
                    @endforeach
                    <text class="gt" x="{{ round($x0 + $i * $grup + $grup / 2, 1) }}" y="{{ $atas + $tinggi + 22 }}" text-anchor="middle">{{ $nama }}</text>
                @endforeach
            </svg>
            @if ($semua->sum() === 0)<p class="kosong-kecil">Belum ada data pada 6 bulan terakhir. Grafik terisi otomatis seiring transaksi.</p>@endif
        </section>

        <section class="pkartu" aria-labelledby="judul-donat">
            <div class="kartu-kepala">
                <div><h2 id="judul-donat">{{ $d['judul'] }}</h2><p class="kartu-sub">{{ $d['sub'] }}</p></div>
            </div>
            <div class="donat-bungkus">
                <svg class="donat" viewBox="0 0 42 42" role="img" aria-label="{{ $d['judul'] }}: {{ $d['pusat'] }}% {{ strtolower($d['pusatLabel']) }}">
                    <circle class="dasar" cx="21" cy="21" r="15.9155"/>
                    @php $kum = 0; @endphp
                    @if ($totalIris > 0)
                        @foreach ($d['irisan'] as $ir)
                            @if ($ir['nilai'] > 0)
                                @php $pj = $ir['nilai'] * 100 / $totalIris; @endphp
                                <circle class="s-{{ $ir['warna'] }}" cx="21" cy="21" r="15.9155" stroke-dasharray="{{ round($pj, 2) }} {{ round(100 - $pj, 2) }}" stroke-dashoffset="{{ round(-$kum, 2) }}"/>
                                @php $kum += $pj; @endphp
                            @endif
                        @endforeach
                    @endif
                </svg>
                <div class="donat-tengah"><b>{{ $d['pusat'] }}%</b><small>{{ $d['pusatLabel'] }}</small></div>
            </div>
            <div class="donat-status"><span class="pil pil--{{ $d['status'][1] }}">{{ $d['status'][0] }}</span></div>
            <ul class="irisan">
                @foreach ($d['irisan'] as $ir)<li><span><i class="w-{{ $ir['warna'] }}"></i>{{ $ir['label'] }}</span><b>{{ $ir['nilai'] }}</b></li>@endforeach
            </ul>
        </section>
    </div>
@endif

@if (! empty($tugas))
    <div class="dash-judul-bagian"><h2>Menunggu Anda</h2><p>Klik kartu untuk langsung ke daftarnya.</p></div>
    <div class="tugas">
        @foreach ($tugas as [$label, $jumlah, $url])
            <a href="{{ $url }}"><b>{{ $jumlah }}</b>{{ $label }}</a>
        @endforeach
    </div>
@endif

@if ($r)
    <div class="dash-baris dash-baris--dua">
        <section class="pkartu" aria-labelledby="judul-segera">
            <div class="kartu-kepala"><div><h2 id="judul-segera">Jatuh tempo terdekat</h2><p class="kartu-sub">Gadai yang belum lunas, diurutkan dari tanggal terdekat.</p></div></div>
            @if (count($r['segera']))
                <ul class="daftar-dash">
                    @foreach ($r['segera'] as $s)
                        <li>
                            <div><a href="{{ $s['url'] }}">{{ $s['nama'] }}</a><small class="mono">{{ $s['sbg'] }}@if ($s['pokok']) · {{ $s['pokok'] }}@endif</small></div>
                            <div class="kanan">
                                <span class="pil pil--{{ $s['hari'] < 0 ? 'gagal' : ($s['hari'] <= 7 ? 'proses' : 'netral') }}">{{ $s['hari'] < 0 ? 'Lewat '.abs($s['hari']).' hari' : ($s['hari'] === 0 ? 'Hari ini' : 'H-'.$s['hari']) }}</span>
                                <small>{{ $s['tanggal'] }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="kosong-kecil">Belum ada gadai berjalan. Gadai baru akan muncul di sini setelah dicairkan.</p>
            @endif
            <a class="tautan-kartu" href="{{ route('panel.gadai.index', ['segera' => 1]) }}">Lihat semua yang segera jatuh tempo →</a>
        </section>

        <section class="pkartu" aria-labelledby="judul-antrean">
            <div class="kartu-kepala"><div><h2 id="judul-antrean">{{ $r['antreanJudul'] }}</h2><p class="kartu-sub">{{ $mode === 'admin' ? 'Periksa KTP asli lalu setujui atau tolak dari halaman anggota.' : 'Pengajuan di atas limit cabang menunggu keputusan Manager.' }}</p></div></div>
            @if (count($r['antrean']))
                <ul class="daftar-dash">
                    @foreach ($r['antrean'] as $a)
                        <li><div><a href="{{ $a['url'] }}">{{ $a['judul'] }}</a><small>{{ $a['sub'] }}</small></div><span class="pil pil--proses">Menunggu</span></li>
                    @endforeach
                </ul>
            @else
                <p class="kosong-kecil">Antrean bersih. Tidak ada yang menunggu saat ini.</p>
            @endif
        </section>
    </div>

    @if ($r['terbaru'] !== null)
        <div class="dash-baris dash-baris--dua">
            <section class="pkartu" aria-labelledby="judul-terbaru">
                <div class="kartu-kepala"><div><h2 id="judul-terbaru">Pembayaran terbaru</h2><p class="kartu-sub">Perpanjangan, cicilan, dan penebusan yang sudah sah.</p></div></div>
                @if (count($r['terbaru']))
                    <ul class="daftar-dash">
                        @foreach ($r['terbaru'] as $p)
                            <li>
                                <div>@if ($p['url'])<a href="{{ $p['url'] }}">{{ $p['nama'] }}</a>@else{{ $p['nama'] }}@endif<small class="mono">{{ $p['kwitansi'] }} · {{ $p['tipe'] }}</small></div>
                                <div class="kanan"><b class="mono">{{ $p['total'] }}</b><small>{{ $p['waktu'] }}</small></div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="kosong-kecil">Belum ada pembayaran tercatat.</p>
                @endif
            </section>

            <section class="pkartu" aria-labelledby="judul-panduan">
                <div class="kartu-kepala"><div><h2 id="judul-panduan">Catatan operasional</h2><p class="kartu-sub">Aturan yang sedang berlaku di sistem ini.</p></div></div>
                <ul class="daftar-dash">
                    <li><div><b>Masa tenggang</b><small>Gadai yang lewat jatuh tempo masuk masa tenggang; lewat tenggang otomatis menjadi “siap lelang”.</small></div></li>
                    <li><div><b>Lelang</b><small>Anggota diberi pemberitahuan lebih dulu. Kelebihan hasil lelang menjadi hak anggota; kekurangan tidak ditagihkan.</small></div></li>
                    <li><div><b>Taksir ulang</b><small>Bila taksiran turun, anggota menyetor selisih lewat cicil sebelum boleh memperpanjang.</small></div></li>
                    <li><div><b>Jejak audit</b><small>Setiap persetujuan, pembayaran, dan akses data sensitif tercatat dan tidak dapat diubah.</small></div></li>
                </ul>
            </section>
        </div>
    @else
        <section class="pkartu" style="margin-top:1rem" aria-labelledby="judul-panduan">
            <div class="kartu-kepala"><div><h2 id="judul-panduan">Praktik baik verifikasi anggota</h2><p class="kartu-sub">Pegangan singkat sebelum menyetujui calon anggota.</p></div></div>
            <ul class="daftar-dash">
                <li><div><b>Cocokkan identitas</b><small>Nama, NIK, dan foto pada KTP harus sama dengan data pendaftaran. Minta KTP asli bila foto kurang jelas.</small></div></li>
                <li><div><b>Pastikan email aktif</b><small>Pengajuan hanya dapat dibuat setelah email terverifikasi. Ingatkan anggota memeriksa folder spam.</small></div></li>
                <li><div><b>Data pribadi dilindungi</b><small>NIK terenkripsi dan setiap pembukaan NIK atau KTP oleh petugas tercatat di jejak audit.</small></div></li>
            </ul>
        </section>
    @endif

    @if (! empty($r['cabang']))
        <div class="dash-judul-bagian"><h2>Perbandingan cabang</h2><p>Posisi portofolio saat ini dan pencairan bulan berjalan.</p></div>
        <div class="tabel-bungkus">
            <table class="tabel">
                <thead><tr><th>Cabang</th><th class="angka">Gadai berjalan</th><th class="angka">Pokok berjalan</th><th class="angka">Jatuh tempo ≤ 7 hari</th><th class="angka">Berisiko</th><th class="angka">Pencairan bulan ini</th></tr></thead>
                <tbody>
                    @foreach ($r['cabang'] as $c)
                        <tr><td><b>{{ $c['nama'] }}</b></td><td class="angka">{{ $c['berjalan'] }}</td><td class="angka">{{ \App\Support\Rupiah::format($c['pokok']) }}</td><td class="angka">{{ $c['segera'] }}</td><td class="angka">{{ $c['risiko'] }}</td><td class="angka">{{ \App\Support\Rupiah::format($c['cair']) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif

@if ($user->role === Role::Member && $user->email_verified_at === null)
    <p class="kabar kabar--galat" style="margin-top:1rem">Email Anda belum diverifikasi, sehingga belum dapat mengajukan titip gadai. Buka email verifikasi yang kami kirim ke {{ $user->email }}.
        <form method="post" action="{{ route('panel.akun.verifikasi.kirim') }}" style="display:inline">@csrf <button class="btn btn--kecil" type="submit">Kirim ulang email verifikasi</button></form></p>
@endif
@if ($user->role === Role::Member && $user->member && $user->member->status !== 'terverifikasi')
    <p class="kabar kabar--galat" style="margin-top:1rem">Keanggotaan Anda belum terverifikasi (status: {{ $user->member->status }}).
        @if (! $user->member->ktp_path) Unggah foto KTP di <a href="{{ route('panel.profil') }}">Profil &amp; KTP</a> agar admin cabang dapat memverifikasi Anda.
        @else Admin cabang sedang memeriksa data Anda.
        @endif
    </p>
@endif
@endsection
