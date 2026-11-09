@props([
    'backUrl' => url()->previous() !== url()->current() ? url()->previous() : route('beranda'),
    'backText' => 'Kembali ke Halaman Sebelumnya',
    'crumbs' => [],
])

<nav class="breadcrumb-nav" aria-label="Navigasi Halaman">
    <div class="breadcrumb-back-wrap">
        <a href="{{ $backUrl }}" class="btn-kembali" onclick="if(window.history.length > 1 && document.referrer.indexOf(window.location.host) !== -1){ window.history.back(); return false; }">
            <svg class="icon-kembali" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
            </svg>
            <span>{{ $backText }}</span>
        </a>
    </div>

    @if (!empty($crumbs))
        <ol class="breadcrumb-trail">
            <li>
                <a href="{{ route('beranda') }}">Beranda</a>
            </li>
            @foreach ($crumbs as $label => $url)
                <li class="breadcrumb-sep" aria-hidden="true">/</li>
                <li>
                    @if (is_string($label) && $url)
                        <a href="{{ $url }}">{{ $label }}</a>
                    @else
                        <span class="breadcrumb-aktif" aria-current="page">{{ is_string($label) ? $label : $url }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</nav>

