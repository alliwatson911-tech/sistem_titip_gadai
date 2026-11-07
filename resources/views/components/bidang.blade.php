@props(['nama', 'label', 'tipe' => 'text', 'nilai' => null, 'wajib' => false, 'bantuan' => null])
<div class="bidang">
    <label for="f-{{ $nama }}">{{ $label }}@if ($wajib) <span aria-hidden="true">*</span>@endif</label>
    <input id="f-{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" value="{{ old($nama, $nilai) }}" @required($wajib) {{ $attributes }}>
    @if ($bantuan)<p class="bantuan">{{ $bantuan }}</p>@endif
    @error($nama)<p class="galat" role="alert">{{ $message }}</p>@enderror
</div>
