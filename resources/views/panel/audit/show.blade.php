@extends('layouts.panel')
@section('judul', 'Audit #'.$a->id)
@section('isi')
    <p class="sub"><a href="{{ route('panel.audit.index') }}">&larr; Jejak audit</a></p>
    <h1>Audit #{{ $a->id }} <span class="lencana lencana--netral mono">{{ $a->aksi }}</span></h1>
    <dl class="ringkasan">
        <div><dt>Waktu</dt><dd>{{ $a->created_at->translatedFormat('j F Y H:i:s') }}</dd></div>
        <div><dt>Pelaku</dt><dd>{{ $a->user?->name ?? 'Sistem/tamu' }}@if ($a->user) <br><small>{{ $a->user->email }}</small>@endif</dd></div>
        <div><dt>Subjek</dt><dd class="mono">{{ $a->subject_type ? class_basename($a->subject_type).' #'.$a->subject_id : '—' }}</dd></div>
        <div><dt>IP</dt><dd class="mono">{{ $a->ip ?: '—' }}</dd></div>
        <div style="grid-column:span 2"><dt>Perangkat</dt><dd style="font-weight:400;font-size:.85rem">{{ $a->user_agent ?: '—' }}</dd></div>
    </dl>
    <p class="catatan-audit">Kolom sensitif (kata sandi, NIK, IMEI) tidak pernah ditulis ke audit.</p>
    <h2>Sebelum</h2>
    <pre class="mono" style="background:var(--putih);border:1px solid var(--garis);padding:1rem;overflow:auto;font-size:.85rem">{{ $a->sebelum ? json_encode($a->sebelum, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '—' }}</pre>
    <h2>Sesudah</h2>
    <pre class="mono" style="background:var(--putih);border:1px solid var(--garis);padding:1rem;overflow:auto;font-size:.85rem">{{ $a->sesudah ? json_encode($a->sesudah, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '—' }}</pre>
@endsection
