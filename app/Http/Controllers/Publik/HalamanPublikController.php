<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Services\LoanCalculator;
use App\Services\Situs;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HalamanPublikController extends Controller
{
    /** Ambil data umum yang dipakai di seluruh layout publik (header, footer, kontak WA, dll.) */
    private function dataUmum(): array
    {
        Situs::pastikanDataAwal();

        $konten = Situs::konten();
        $kebijakan = Situs::kebijakan();
        $produk = Product::where('is_active', true)->whereNull('branch_id')->orderBy('urutan')->get();
        if ($produk->isEmpty() && ! app()->runningUnitTests()) {
            $produk = BerandaController::defaultProduk();
        }

        $token = [
            'tenor' => $produk->first()?->tenor_hari ?? 30,
            'tenggang' => $kebijakan['masa_tenggang_hari'] ?? 3,
            'lelang_hari' => $kebijakan['hari_pemberitahuan_lelang'] ?? 7,
            'maks_perpanjangan' => $kebijakan['maks_perpanjangan'] ?? 3,
        ];
        $isi = fn (?string $t) => Situs::isi($t, $token);

        $cabang = Branch::where('is_active', true)->orderByRaw("FIELD(kode,'PUSAT','A','B')")->get()->keyBy('kode');
        if ($cabang->isEmpty() && ! app()->runningUnitTests()) {
            $cabang = BerandaController::defaultCabang();
        }

        return [
            'konten' => $konten,
            'kebijakan' => $kebijakan,
            'produk' => $produk,
            'token' => $token,
            'isi' => $isi,
            'cabang' => $cabang,
            'pusat' => $cabang->get('PUSAT'),
        ];
    }

    /** Data minimal yang dibutuhkan kalkulator di browser */
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

    /** Halaman Khusus: Layanan Gadai */
    public function layanan(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.layanan', $data);
    }

    /** Halaman Detail Khusus per Jenis Jaminan */
    public function layananDetail(string $slug): View
    {
        $data = $this->dataUmum();
        $detailProduk = Product::where('is_active', true)->where('slug', $slug)->firstOrFail();
        $data['p'] = $detailProduk;
        $data['dataProduk'] = $this->dataKalkulator($data['produk']);
        $data['maksPeriode'] = LoanCalculator::MAKS_PERIODE;

        return view('publik.pages.layanan-detail', $data);
    }

    /** Halaman Khusus: Standar Layanan */
    public function standar(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.standar', $data);
    }

    /** Halaman Khusus: Panduan Alur Titip Gadai */
    public function panduan(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.panduan', $data);
    }

    /** Halaman Khusus: Syarat & Ketentuan Titip Gadai */
    public function syarat(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.syarat', $data);
    }

    /** Halaman Khusus: Simulasi & Kalkulator Gadai */
    public function simulasi(): View
    {
        $data = $this->dataUmum();
        $data['dataProduk'] = $this->dataKalkulator($data['produk']);
        $data['maksPeriode'] = LoanCalculator::MAKS_PERIODE;

        return view('publik.pages.simulasi', $data);
    }

    /** Halaman Khusus: FAQ & Informasi */
    public function faq(): View
    {
        $data = $this->dataUmum();
        $isi = $data['isi'];

        $faqs = Faq::withoutGlobalScopes()->where('is_published', true)->orderBy('urutan')->orderBy('id')->get()
            ->each(function ($f) use ($isi) {
                $f->pertanyaan = $isi($f->pertanyaan);
                $f->jawaban = $isi($f->jawaban);
            });

        $data['faq'] = $faqs->where('kelompok', 'faq')->values();
        $data['informasi'] = $faqs->where('kelompok', 'informasi')->values();

        return view('publik.pages.faq', $data);
    }

    /** Halaman Khusus: Tentang Kami & Kontak Cabang */
    public function tentang(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.tentang', $data);
    }
}
