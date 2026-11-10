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
        Situs::pastikanDataAwal();

        $konten = Situs::konten();
        $kebijakan = Situs::kebijakan();

        $produk = Product::where('is_active', true)->whereNull('branch_id')->orderBy('urutan')->get();
        if ($produk->isEmpty() && ! app()->runningUnitTests()) {
            $produk = self::defaultProduk();
        }

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
        if ($cabang->isEmpty() && ! app()->runningUnitTests()) {
            $cabang = self::defaultCabang();
        }

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

    /** Produk bawaan sebagai fallback jika basis data belum terisi. */
    public static function defaultProduk(): Collection
    {
        $items = [
            ['nama' => 'Gadai Smartphone', 'kategori_barang' => 'smartphone', 'ltv_max_bp' => 8000, 'deskripsi' => 'Ponsel pintar berbagai merek dalam kondisi menyala dan terbebas dari kunci akun.'],
            ['nama' => 'Gadai Laptop', 'kategori_barang' => 'laptop', 'ltv_max_bp' => 8000, 'deskripsi' => 'Laptop dan notebook lengkap dengan adaptor pengisi daya.'],
            ['nama' => 'Gadai Kamera', 'kategori_barang' => 'kamera', 'ltv_max_bp' => 7500, 'deskripsi' => 'Kamera mirrorless dan DSLR beserta lensa dan baterai.'],
            ['nama' => 'Gadai Konsol Game', 'kategori_barang' => 'konsol', 'ltv_max_bp' => 7500, 'deskripsi' => 'Konsol game dengan kontroler dan kabel bawaan.'],
            ['nama' => 'Gadai Televisi', 'kategori_barang' => 'televisi', 'ltv_max_bp' => 7000, 'deskripsi' => 'Televisi layar datar dengan remote asli.'],
            ['nama' => 'Gadai Smartwatch', 'kategori_barang' => 'smartwatch', 'ltv_max_bp' => 7000, 'deskripsi' => 'Jam tangan pintar dengan pengisi daya asli.'],
        ];

        return collect($items)->map(function ($item, $i) {
            $p = new Product($item + [
                'branch_id' => null,
                'pinjaman_min' => 100_000,
                'pinjaman_max' => 25_000_000,
                'tenor_hari' => 30,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 15,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 125_000,
                'urutan' => $i + 1,
                'is_active' => true,
            ]);
            $p->id = $i + 1;
            $p->slug = str($p->nama)->slug()->toString();

            return $p;
        });
    }

    /** Cabang operasional bawaan sebagai fallback jika basis data belum terisi. */
    public static function defaultCabang(): Collection
    {
        $items = [
            'PUSAT' => new Branch(['kode' => 'PUSAT', 'nama' => 'Kantor Pusat', 'is_active' => true, 'alamat' => 'Makassar', 'telepon' => '', 'jam_buka' => 'Senin - Sabtu: 08:30 - 17:00 WITA']),
            'A' => new Branch(['kode' => 'A', 'nama' => 'Cabang A', 'is_active' => true, 'alamat' => 'Jl. Veteran Selatan, Makassar', 'telepon' => '', 'jam_buka' => '08:30 - 17:00 WITA']),
            'B' => new Branch(['kode' => 'B', 'nama' => 'Cabang B', 'is_active' => true, 'alamat' => 'Jl. Perintis Kemerdekaan, Makassar', 'telepon' => '', 'jam_buka' => '08:30 - 17:00 WITA']),
        ];

        return collect($items);
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
