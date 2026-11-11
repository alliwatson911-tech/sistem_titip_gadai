@extends('layouts.panel')
@section('judul', 'Pengajuan baru')
@section('isi')
    @use('App\Support\Rupiah')
    <h1>Pengajuan titip gadai</h1>
    <p class="sub">Pengajuan ini membuat antrean Anda lebih cepat. Barang tetap harus dibawa ke cabang untuk diperiksa dan ditaksir.</p>

    @if ($member && $member->status !== 'terverifikasi')
        <p class="kabar kabar--galat">Keanggotaan Anda belum terverifikasi. Unggah foto KTP di <a href="{{ route('panel.profil') }}">Profil &amp; KTP</a>, lalu tunggu admin cabang memverifikasi.</p>
    @endif

    <form class="form" method="post" action="{{ route('panel.pengajuan.store') }}">
        @csrf
        @if ($anggota !== null)
            <div class="bidang">
                <label for="f-member_id">Anggota (terverifikasi)</label>
                <select id="f-member_id" name="member_id" required>
                    <option value="">Pilih anggota</option>
                    @foreach ($anggota as $m)<option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->nama_lengkap }}</option>@endforeach
                </select>
                @error('member_id')<p class="galat" role="alert">{{ $message }}</p>@enderror
            </div>
        @endif
        <div class="bidang">
            <label for="f-product_id">Jenis jaminan</label>
            <select id="f-product_id" name="product_id" required>
                <option value="">Pilih jenis jaminan</option>
                @foreach ($produk as $p)
                    <option value="{{ $p->id }}" data-kelompok="{{ $p->kelompok_agunan }}" data-dokumen='@json($p->dokumen_wajib)' data-atribut='@json($p->atribut_khusus)' @selected(old('product_id', $pilihan) == $p->id || $pilihan === $p->slug)>{{ $p->nama }} ({{ Rupiah::ringkas($p->pinjaman_min) }} – {{ Rupiah::ringkas($p->pinjaman_max) }})</option>
                @endforeach
            </select>
            @error('product_id')<p class="galat" role="alert">{{ $message }}</p>@enderror
        </div>

        <div id="kotak-info-produk" style="display:none; background:var(--kertas-2); border:1px solid var(--garis); padding:1.25rem; border-radius:var(--radius-sm); margin-bottom:1.5rem;">
            <h3 style="font-size:1.1rem; margin:0 0 .5rem; color:var(--tinta);">Kelengkapan Dokumen &amp; Data Agunan</h3>
            <div id="info-dokumen" style="font-size:.9rem; line-height:1.5; color:var(--tinta-2); margin-bottom:.75rem;"></div>
            <div id="info-atribut" style="font-size:.85rem; line-height:1.5; color:var(--tinta-2);"></div>
        </div>

        <x-bidang nama="pinjaman_diminta" label="Jumlah pinjaman yang diinginkan (Rp)" inputmode="numeric" autocomplete="off" :wajib="true" bantuan="Angka pasti ditetapkan analis setelah barang/dokumen ditaksir. Anda boleh meminta lebih rendah dari plafon." />
        <div class="bidang">
            <label for="f-catatan_member">Catatan / Rincian Agunan (opsional)</label>
            <textarea id="f-catatan_member" name="catatan_member" maxlength="500" placeholder="Contoh: Merek HP/Laptop, plat nomor kendaraan, atau nomor SHM">{{ old('catatan_member') }}</textarea>
            @error('catatan_member')<p class="galat" role="alert">{{ $message }}</p>@enderror
        </div>
        <button class="btn btn--utama" type="submit">Kirim pengajuan</button>
    </form>

    <script>
        (function() {
            var sel = document.getElementById('f-product_id');
            var box = document.getElementById('kotak-info-produk');
            var dokEl = document.getElementById('info-dokumen');
            var atrEl = document.getElementById('info-atribut');
            if (!sel || !box) return;

            function updateInfo() {
                var opt = sel.options[sel.selectedIndex];
                if (!opt || !opt.value) {
                    box.style.display = 'none';
                    return;
                }
                var doks = JSON.parse(opt.dataset.dokumen || '[]');
                var atrs = JSON.parse(opt.dataset.atribut || '[]');
                var htmlDok = '<strong>Dokumen yang wajib dibawa ke cabang:</strong><ul style="margin:.3rem 0 0; padding-left:1.2rem;">';
                if (doks.length) {
                    doks.forEach(function(d) { htmlDok += '<li>' + d + '</li>'; });
                } else {
                    htmlDok += '<li>KTP Asli dan kelengkapan unit jaminan.</li>';
                }
                htmlDok += '</ul>';

                var htmlAtr = '';
                if (atrs.length) {
                    htmlAtr = '<strong>Parameter data yang akan diperiksa petugas:</strong> ' + atrs.join(', ');
                }

                dokEl.innerHTML = htmlDok;
                atrEl.innerHTML = htmlAtr;
                box.style.display = 'block';
            }

            sel.addEventListener('change', updateInfo);
            updateInfo();
        })();
    </script>
@endsection
