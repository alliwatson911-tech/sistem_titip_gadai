@extends('layouts.panel')
@section('judul', 'Antrean Notifikasi Penitip')
@section('isi')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>Antrean Notifikasi Penitip</h1>
            <p class="sub">Log dan antrean pesan keluar (WhatsApp &amp; Email) pendaftaran akun dan jatuh tempo.</p>
        </div>
        <a href="{{ route('panel.anggota') }}" class="btn btn--kecil">&larr; Kembali ke Daftar Penitip</a>
    </div>

    <div class="tabel-bungkus">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Penitip</th>
                    <th>Saluran</th>
                    <th>Tujuan</th>
                    <th>Pesan</th>
                    <th>Status</th>
                    <th>Aksi Manual</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifikasi as $n)
                    @php
                        $waUrl = $n->channel === 'whatsapp' ? \App\Services\PenitipNotificationService::tautanWhatsApp($n) : null;
                    @endphp
                    <tr>
                        <td class="mono" style="font-size: .8rem;">{{ $n->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <b>{{ $n->member?->nama_lengkap ?? $n->user?->name ?? 'Penitip' }}</b>
                            <div style="font-size: .75rem; color: var(--muted);">{{ $n->member?->branch?->nama ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="lencana lencana--{{ $n->channel === 'whatsapp' ? 'sukses' : 'netral' }}">
                                {{ strtoupper($n->channel) }}
                            </span>
                        </td>
                        <td class="mono" style="font-size: .85rem;">{{ $n->tujuan }}</td>
                        <td style="max-width: 320px; font-size: .85rem; line-height: 1.4;">
                            <details>
                                <summary style="cursor: pointer; color: var(--kuningan-tua);">Lihat teks pesan...</summary>
                                <pre style="white-space: pre-wrap; font-family: inherit; font-size: .8rem; background: var(--kertas-2); padding: .5rem; border-radius: 4px; margin-top: .5rem;">{{ $n->pesan }}</pre>
                            </details>
                        </td>
                        <td>
                            <span class="lencana lencana--{{ ['terkirim' => 'sukses', 'antrean' => 'proses', 'gagal' => 'gagal'][$n->status] ?? 'netral' }}">
                                {{ ucfirst($n->status) }}
                            </span>
                        </td>
                        <td>
                            @if ($n->channel === 'whatsapp' && $waUrl)
                                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn--kecil btn--utama" style="font-size: .75rem; padding: .3rem .6rem;">
                                    Kirim WA &rarr;
                                </a>
                            @else
                                <span style="font-size: .75rem; color: var(--muted);">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="kosong">Belum ada riwayat antrean notifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $notifikasi->links() }}
    </div>
@endsection

