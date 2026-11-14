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
use Illuminate\View\View;

class HalamanPublikController extends Controller
{
    /** Ambil data umum yang dipakai di seluruh layout publik (header, footer, kontak WA, dll.) */
    private function dataUmum(): array
    {
        Situs::pastikanDataAwal();

        $konten = Situs::konten();
        $kebijakan = Situs::kebijakan();
        try {
            $produk = Product::clientFeatured()->whereNull('branch_id')->orderBy('urutan')->get();
        } catch (\Throwable) {
            try {
                $produk = Product::where('is_active', true)->whereNull('branch_id')->orderBy('urutan')->get();
            } catch (\Throwable) {
                $produk = collect();
            }
        }

        if ($produk->isEmpty() && ! app()->runningUnitTests()) {
            $produk = BerandaController::defaultProduk();
        }

        $token = [
            'tenor' => $produk->first()?->tenor_hari ?? 14,
            'tenggang' => $kebijakan['masa_tenggang_hari'] ?? 3,
            'lelang_hari' => $kebijakan['hari_pemberitahuan_lelang'] ?? 7,
            'maks_perpanjangan' => $kebijakan['maks_perpanjangan'] ?? 3,
        ];
        $isi = fn (?string $t) => Situs::isi($t, $token);

        /** @var Collection<string, Branch> $cabang */
        $cabang = Branch::where('is_active', true)->whereNotIn('kode', ['A', 'B'])->orderBy('nama')->get()->keyBy('kode');
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
            'pusat' => $cabang->firstWhere('kode', 'PUSAT') ?? $cabang->first(),
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
        $data['kategoriAktif'] = request('kategori', 'semua');

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

    /** Halaman Khusus: Standar Layanan (Diarahkan ke Cara Penitipan) */
    public function standar(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.cara-penitipan', $data);
    }

    /** Halaman Khusus: Panduan Alur Titip Gadai (Diarahkan ke Cara Penitipan) */
    public function panduan(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.cara-penitipan', $data);
    }

    /** Halaman Khusus: Cara Penitipan */
    public function caraPenitipan(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.cara-penitipan', $data);
    }

    /** Halaman Khusus: Syarat & Ketentuan Titip Gadai (Termasuk FAQ di Bawahnya) */
    public function syarat(): View
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

    /** Halaman Khusus: Promo (Pengganti Pengumuman) */
    public function promo(): View
    {
        $data = $this->dataUmum();
        $data['promo'] = Announcement::where('is_published', true)
            ->where(fn ($q) => $q->whereNull('mulai')->orWhere('mulai', '<=', now()->toDateString()))
            ->where(fn ($q) => $q->whereNull('selesai')->orWhere('selesai', '>=', now()->toDateString()))
            ->latest('id')
            ->get();

        return view('publik.pages.promo', $data);
    }

    /** Halaman Khusus: Kontak & Lokasi Cabang */
    public function kontak(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.kontak', $data);
    }

    /** Halaman Khusus: Tentang Kami (Ringkasan & Profil) */
    public function tentang(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.tentang', $data);
    }

    /** Sub-halaman: Sambutan Pimpinan */
    public function sambutan(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.sambutan', $data);
    }

    /** Sub-halaman: Visi & Misi */
    public function visiMisi(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.visi-misi', $data);
    }

    /** Sub-halaman: Dokumen Legalitas */
    public function legalitas(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.legalitas', $data);
    }

    /** Sub-halaman: Daftar Cabang */
    public function cabang(): View
    {
        $data = $this->dataUmum();

        return view('publik.pages.cabang', $data);
    }
}
