@php
    $pusatModel = isset($pusat) ? $pusat : null;
    $rawHp = $pusatModel ? ($pusatModel->whatsapp ?: $pusatModel->telepon) : null;
    $waNumber = null;
    if ($rawHp) {
        $cleanHp = preg_replace('/[^0-9]/', '', (string) $rawHp);
        if ($cleanHp !== '') {
            if (str_starts_with($cleanHp, '0')) {
                $waNumber = '62' . substr($cleanHp, 1);
            } elseif (str_starts_with($cleanHp, '62')) {
                $waNumber = $cleanHp;
            } else {
                $waNumber = '62' . $cleanHp;
            }
        }
    }
    $waText = rawurlencode('Halo linygadai, saya ingin konsultasi atau tanya syarat titip gadai elektronik.');
@endphp

@if ($waNumber)
<!-- Tombol WhatsApp Melayang -->
<a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}"
   target="_blank"
   rel="noopener noreferrer"
   class="floating-whatsapp-pill"
   aria-label="Hubungi WhatsApp Customer Service linygadai">
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.23 8.23 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1s.9 2.43 1.03 2.6c.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.3"/>
    </svg>
    <span>Konsultasi CS</span>
</a>
@endif

<!-- Tombol Kembali ke Atas (Back to Top) -->
<button type="button" class="back-to-top" id="back-to-top" aria-label="Kembali ke atas halaman">
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>
