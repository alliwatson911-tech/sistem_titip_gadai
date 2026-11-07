@extends('layouts.panel')
@section('judul', 'Lelang')
@section('isi')
    @use('App\Support\Rupiah')
    @use('App\Enums\LoanStatus', 'LS')
    <h1>Lelang</h1>
    <p class="sub">Gadai yang melewati masa tenggang. Anggota diberi pemberitahuan dan masih dapat menebus atau memperpanjang sampai lelang dilaksanakan. Hasil lelang: pokok, sewa modal, dan biaya lelang dilunasi dulu; <b>kelebihan menjadi hak anggota</b>; kekurangan tidak ditagihkan.</p>

    @forelse ($daftar as $g)
        @php $au = $g->auction; $t = $g->status === LS::SiapLelang ? $tagihan($g) : null; @endphp
        <section class="form" style="max-width:none;margin-bottom:1.2rem">
            <h2 style="margin-bottom:.3rem"><a class="mono" href="{{ route('panel.gadai.show', $g) }}">{{ $g->no_sbg }}</a> <span class="lencana lencana--{{ $g->status->nada() }}">{{ $g->status->label() }}</span></h2>
            <p class="sub" style="margin-bottom:.8rem">{{ $g->member->nama_lengkap }} · {{ $g->product->nama }} · saldo pokok {{ Rupiah::format((int) $g->pokok_saldo) }} · jatuh tempo {{ $g->jatuh_tempo->translatedFormat('j M Y') }}</p>
            @if ($au)
                <dl class="ringkasan">
                    <div><dt>Diberitahukan</dt><dd>{{ $au->diberitahukan_at?->translatedFormat('j M Y') }}</dd></div>
                    <div><dt>Lelang paling cepat</dt><dd>{{ $au->diberitahukan_at?->copy()->addDays($g->snapshot_syarat['hari_pemberitahuan_lelang'] ?? 7)->translatedFormat('j M Y') }}</dd></div>
                    <div><dt>Tanggal lelang</dt><dd>{{ $au->tgl_lelang?->translatedFormat('j M Y') ?? '—' }}</dd></div>
                    <div><dt>Harga limit</dt><dd>{{ $au->harga_limit ? Rupiah::format((int) $au->harga_limit) : '—' }}</dd></div>
                    @if ($t)<div><dt>Kewajiban bila ditebus hari ini</dt><dd>{{ Rupiah::format($t['tebus']['total']) }}</dd></div>@endif
                    @if ($au->status === 'laku')
                        <div><dt>Harga laku</dt><dd>{{ Rupiah::format((int) $au->harga_laku) }}</dd></div>
                        <div><dt>{{ $au->kelebihan > 0 ? 'Kelebihan' : 'Kekurangan' }}</dt><dd>{{ Rupiah::format(abs((int) $au->kelebihan)) }} ({{ $au->kelebihan_status }})</dd></div>
                    @endif
                </dl>
            @endif

            @if ($g->status === LS::SiapLelang)
                @can('lelang.kelola')
                    <form method="post" action="{{ route('panel.lelang.jadwal', $g) }}" class="baris-2" style="align-items:end">@csrf
                        <x-bidang nama="tgl_lelang" label="Tanggal lelang" tipe="date" :nilai="$au?->tgl_lelang?->toDateString()" />
                        <x-bidang nama="harga_limit" label="Harga limit (Rp)" inputmode="numeric" :nilai="$au?->harga_limit" autocomplete="off" />
                        <div><button class="btn btn--kecil btn--utama" type="submit">Jadwalkan lelang</button></div>
                    </form>
                @endcan
                @can('lelang.catat')
                    @if ($au?->tgl_lelang)
                        <form method="post" action="{{ route('panel.lelang.hasil', $g) }}" class="baris-2" style="align-items:end;margin-top:.8rem">@csrf
                            <div class="bidang"><label for="h-{{ $g->id }}">Hasil</label><select id="h-{{ $g->id }}" name="hasil"><option value="laku">Laku</option><option value="tidak_laku">Tidak laku</option></select></div>
                            <x-bidang nama="harga_laku" label="Harga laku (Rp)" inputmode="numeric" autocomplete="off" />
                            <x-bidang nama="biaya_lelang" label="Biaya lelang (Rp)" inputmode="numeric" autocomplete="off" nilai="0" />
                            <div><button class="btn btn--kecil" type="submit">Catat hasil</button></div>
                        </form>
                    @endif
                @endcan
            @elseif ($g->status === LS::Dilelang)
                @can('lelang.catat')
                    <form method="post" action="{{ route('panel.lelang.kelebihan', $g) }}">@csrf
                        <button class="btn btn--utama btn--kecil" type="submit">Catat kelebihan sudah dibayarkan ke anggota</button></form>
                @endcan
            @endif
        </section>
    @empty
        <p class="kosong">Tidak ada gadai dalam proses lelang.</p>
    @endforelse
@endsection
