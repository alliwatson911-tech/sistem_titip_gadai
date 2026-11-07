@if ($pengumuman->isNotEmpty())
<section class="bagian bagian--gelap" id="pengumuman" data-spy="beranda" aria-labelledby="judul-pengumuman">
    <div class="wrap">
        <div class="billboard-kepala">
            <div>
                <p class="nomor">Papan pengumuman</p>
                <h2 id="judul-pengumuman" style="margin-top:.9rem">Kabar dari cabang kami</h2>
            </div>
            @if ($pengumuman->count() > 1)
                <div class="panah">
                    <button type="button" data-geser="-1" aria-label="Pengumuman sebelumnya">&larr;</button>
                    <button type="button" data-geser="1" aria-label="Pengumuman berikutnya">&rarr;</button>
                </div>
            @endif
        </div>
        <div class="billboard" id="billboard" role="region" aria-label="Daftar pengumuman" tabindex="0">
            @foreach ($pengumuman as $p)
                <article class="papan">
                    @if ($p->mulai)<time datetime="{{ $p->mulai->toDateString() }}">{{ $p->mulai->translatedFormat('j F Y') }}{{ $p->branch ? ' · '.$p->branch->nama : '' }}</time>@else<span class="tanda">Pengumuman{{ $p->branch ? ' · '.$p->branch->nama : '' }}</span>@endif
                    <h3>{{ $p->judul }}</h3>
                    <p>{{ $p->isi }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
