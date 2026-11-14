@extends('layouts.panel')
@section('judul', 'Penitip')
@section('isi')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>Daftar Penitip</h1>
            <p class="sub">Data penitip di {{ auth()->user()->branch?->nama ?? 'semua cabang' }}.</p>
        </div>
        <div style="display: flex; gap: .75rem; flex-wrap: wrap;">
            <a href="{{ route('panel.anggota.notifikasi') }}" class="btn btn--kecil">
                Antrean Notifikasi @if(!empty($notifAntreanCount) && $notifAntreanCount > 0) <span style="background: var(--merah-hp, #FA0001); color: #fff; padding: .1rem .45rem; border-radius: 9999px; font-size: .75rem;">{{ $notifAntreanCount }}</span> @endif
            </a>
            @can('anggota.tambah')
                <a href="{{ route('panel.anggota.create') }}" class="btn btn--kecil btn--utama">
                    + Input Penitip Baru
                </a>
            @endcan
        </div>
    </div>

    @if (session('akun_baru'))
        @php $ak = session('akun_baru'); @endphp
        <div class="panel-form" style="background: #eef6ff; border: 1.5px solid #b8daff; border-radius: var(--radius-m); padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <h2 style="font-size: 1.15rem; color: var(--navy-tua, #002665); margin: 0 0 .5rem;">
                        Akun Penitip Baru Berhasil Dibuat
                    </h2>
                    <p style="margin: 0 0 .75rem; font-size: .95rem; color: var(--tinta-2);">
                        Berikan rincian akun di bawah ini kepada penitip ({{ $ak['nama'] }}):
                    </p>
                    <div style="font-size: .9rem; line-height: 1.6; background: #fff; padding: .75rem 1rem; border-radius: 4px; border: 1px solid #cce5ff;">
                        <div><strong>Email Masuk:</strong> <code>{{ $ak['email'] }}</code></div>
                        <div><strong>Kata Sandi Sementara:</strong> <code style="font-weight: 700; color: #b8860b;">{{ $ak['password'] }}</code></div>
                        <div><strong>No. WhatsApp / HP:</strong> {{ $ak['telepon'] }}</div>
                        <div style="margin-top: .35rem; font-size: .8rem; color: var(--muted); font-style: italic;">
                            *Penitip wajib membuat kata sandi baru saat pertama kali login.
                        </div>
                    </div>
                </div>

                @if(!empty($ak['wa_url']))
                    <div style="align-self: center;">
                        <a href="{{ $ak['wa_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn--utama" style="padding: .65rem 1.25rem;">
                            Kirim WhatsApp Sekarang &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <form class="filter" method="get" action="{{ route('panel.anggota') }}">
        <div class="bidang" style="margin:0"><label for="f-status">Status</label>
            <select id="f-status" name="status"><option value="">Semua</option>
                @foreach (['calon', 'terverifikasi', 'ditolak', 'blokir'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select></div>
        <div class="bidang" style="margin:0"><label for="f-cari">Nama / NIK</label><input id="f-cari" name="cari" value="{{ request('cari') }}"></div>
        <button class="btn btn--kecil" type="submit">Terapkan</button>
    </form>

    <div class="tabel-bungkus"><table class="tabel">
        <thead><tr><th>Nama Penitip</th><th>NIK</th><th>Cabang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($anggota as $a)
            <tr>
                <td><a href="{{ route('panel.anggota.show', $a) }}"><b>{{ $a->nama_lengkap }}</b></a></td>
                <td class="mono">{{ $a->nikTersamar() }}</td>
                <td>{{ $a->branch->nama }}</td>
                <td><span class="lencana lencana--{{ ['terverifikasi' => 'sukses', 'calon' => 'proses', 'ditolak' => 'gagal', 'blokir' => 'gagal'][$a->status] ?? 'netral' }}">{{ $a->status }}</span></td>
                <td>
                    <a href="{{ route('panel.anggota.show', $a) }}" class="btn btn--kecil" style="padding: .2rem .5rem; font-size: .75rem;">Detail</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="kosong">Belum ada data penitip terdaftar.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:1rem">{{ $anggota->links() }}</div>
@endsection
