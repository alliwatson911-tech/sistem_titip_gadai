<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Services\LoanCalculator;
use App\Services\Situs;
use Illuminate\Support\Collection;

/** Satu halaman publik: beranda, produk, panduan, simulasi, tentang kami, masuk akun. */
class BerandaController extends Controller
{
    public function __invoke()
    {
        $konten = Situs::konten();
        $kebijakan = Situs::kebijakan();

        $produk = Product::where('is_active', true)->whereNull('branch_id')->orderBy('urutan')->get();

        // Token {tenor}, {tenggang}, {lelang_hari}, {maks_perpanjangan} pada teks mengikuti pengaturan kebijakan/produk.
        $token = [
            'tenor' => $produk->first()?->tenor_hari ?? 30,
            'tenggang' => $kebijakan['masa_tenggang_hari'] ?? 3,
            'lelang_hari' => $kebijakan['hari_pemberitahuan_lelang'] ?? 7,
            'maks_perpanjangan' => $kebijakan['maks_perpanjangan'] ?? 3,
        ];
        $isi = fn (?string $t) => Situs::isi($t, $token);

        // Halaman publik menampilkan konten semua cabang; global scope panel dimatikan di sini.
        $faqs = Faq::withoutGlobalScopes()->where('is_published', true)->orderBy('urutan')->orderBy('id')->get()
            ->each(function ($f) use ($isi) {
                $f->pertanyaan = $isi($f->pertanyaan);
                $f->jawaban = $isi($f->jawaban);
            });

        $cabang = Branch::where('is_active', true)->orderByRaw("FIELD(kode,'PUSAT','A','B')")->get()->keyBy('kode');

        return view('publik.beranda', [
            'konten' => $konten,
            'token' => $token,
            'isi' => $isi,
            'produk' => $produk,
            'pengumuman' => Announcement::withoutGlobalScopes()->with('branch')->tayang()->limit(8)->get(),
            'faq' => $faqs->where('kelompok', 'faq')->values(),
            'informasi' => $faqs->where('kelompok', 'informasi')->values(),
            'cabang' => $cabang,
            'pusat' => $cabang->get('PUSAT'),
            'dataProduk' => $this->dataKalkulator($produk),
            'maksPeriode' => LoanCalculator::MAKS_PERIODE,
        ]);
    }

    /** Data minimal yang dibutuhkan kalkulator di browser (rumus tetap di server). */
    private function dataKalkulator(Collection $produk): array
    {
        return $produk->map(fn (Product $p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'nama' => $p->nama,
            'ltvMaxBp' => $p->ltv_max_bp,
            'min' => $p->pinjaman_min,
            'max' => $p->pinjaman_max,
            'tenor' => $p->tenor_hari,
        ])->values()->all();
    }
}
