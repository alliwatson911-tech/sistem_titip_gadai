@if ($faq->isNotEmpty() || $informasi->isNotEmpty())
<section class="bagian bagian--kertas2" id="faq" data-spy="beranda" aria-labelledby="judul-faq">
    <div class="wrap">
        <div class="kepala-bagian">
            <p class="nomor">FAQ dan informasi</p>
            <h2 id="judul-faq">Pertanyaan yang paling sering kami terima</h2>
        </div>
        <div class="dua-kolom">
            @if ($faq->isNotEmpty())
            <div class="akordeon">
                <h3>Tanya jawab</h3>
                @foreach ($faq as $f)
                    <details name="faq">
                        <summary>{{ $f->pertanyaan }}</summary>
                        <div class="jawab"><p>{{ $f->jawaban }}</p></div>
                    </details>
                @endforeach
            </div>
            @endif
            @if ($informasi->isNotEmpty())
            <div class="akordeon">
                <h3>Informasi</h3>
                @foreach ($informasi as $f)
                    <details name="informasi">
                        <summary>{{ $f->pertanyaan }}</summary>
                        <div class="jawab"><p>{{ $f->jawaban }}</p></div>
                    </details>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
@endif
