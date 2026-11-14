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

        $produk = Product::clientFeatured()->whereNull('branch_id')->orderBy('urutan')->get();
        if ($produk->isEmpty() && ! app()->runningUnitTests()) {
            $produk = self::defaultProduk();
        }

        // Token {tenor}, {tenggang}, {lelang_hari}, {maks_perpanjangan} pada teks mengikuti pengaturan kebijakan/produk.
        $token = [
            'tenor' => $produk->first()?->tenor_hari ?? 14,
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

        $cabang = Branch::where('is_active', true)->orderBy('nama')->get()->keyBy('kode');
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
    /** 8 Kategori Produk Resmi PERSIDEN HP Titip Gadai */
    public static function defaultProduk(): Collection
    {
        $items = [
            ['nama' => 'Titip Gadai Laptop', 'kategori_barang' => 'laptop', 'kelompok_agunan' => 'elektronik', 'ltv_max_bp' => 8000, 'deskripsi' => 'Laptop dan notebook lengkap dengan adaptor pengisi daya.'],
            ['nama' => 'Titip Gadai HP (Smartphone)', 'kategori_barang' => 'smartphone', 'kelompok_agunan' => 'elektronik', 'ltv_max_bp' => 8000, 'deskripsi' => 'Ponsel pintar berbagai merek dalam kondisi menyala dan terbebas dari kunci akun.'],
            ['nama' => 'Titip Gadai Tab / Tablet', 'kategori_barang' => 'tablet', 'kelompok_agunan' => 'elektronik', 'ltv_max_bp' => 8000, 'deskripsi' => 'iPad, Samsung Galaxy Tab, dan tablet berbagai merek dengan fungsi normal.'],
            ['nama' => 'Titip Gadai Kamera', 'kategori_barang' => 'kamera', 'kelompok_agunan' => 'elektronik', 'ltv_max_bp' => 7500, 'deskripsi' => 'Kamera mirrorless dan DSLR beserta lensa dan baterai.'],
            ['nama' => 'Titip Gadai BPKB Mobil', 'kategori_barang' => 'bpkb-mobil', 'kelompok_agunan' => 'kendaraan', 'ltv_max_bp' => 8500, 'deskripsi' => 'BPKB mobil pribadi atau niaga. Dokumen tersimpan aman.'],
            ['nama' => 'Titip Gadai BPKB Motor', 'kategori_barang' => 'bpkb-motor', 'kelompok_agunan' => 'kendaraan', 'ltv_max_bp' => 8500, 'deskripsi' => 'BPKB motor seluruh merek Jepang dan Eropa.'],
            ['nama' => 'Titip Gadai Sertifikat Properti & Tanah', 'kategori_barang' => 'sertifikat-rumah', 'kelompok_agunan' => 'properti', 'ltv_max_bp' => 7000, 'deskripsi' => 'Sertifikat Hak Milik (SHM) dan SHGB rumah tinggal atau tanah.'],
            ['nama' => 'Titip Gadai Barang & Surat Berharga Lainnya', 'kategori_barang' => 'lainnya', 'kelompok_agunan' => 'berharga', 'ltv_max_bp' => 8000, 'deskripsi' => 'Surat berharga legal dan agunan bernilai tinggi lainnya.'],
        ];

        return collect($items)->map(function ($item, $i) {
            $p = new Product($item + [
                'branch_id' => null,
                'pinjaman_min' => 100_000,
                'pinjaman_max' => 25_000_000,
                'tenor_hari' => 14,
                'durasi_opsi_minggu' => 1,
                'sewa_modal_per_hari_bp' => 15,
                'blok_hari' => 7,
                'admin_bp' => 100,
                'admin_min' => 5_000,
                'admin_max' => 125_000,
                'urutan' => $i + 1,
                'is_active' => true,
                'is_client_featured' => true,
            ]);
            $p->id = $i + 1;
            $p->slug = str($p->nama)->slug()->toString();

            return $p;
        });
    }

    /** 7 Cabang PERSIDEN HP di Makassar & Gowa (08.00 - 22.00 WITA) */
    public static function defaultCabang(): Collection
    {
        $items = [
            'ANTANG' => new Branch(['kode' => 'ANTANG', 'nama' => 'Persiden HP Antang Raya', 'is_active' => true, 'alamat' => 'Jl. Antang Raya No.116, Antang, Makassar', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
            'BTP' => new Branch(['kode' => 'BTP', 'nama' => 'Persiden HP BTP', 'is_active' => true, 'alamat' => 'Ruko BTP, blok M No.3, Jl. Bumi Tamalanrea Permai No.17, Makassar', 'telepon' => '0853 4245 3005', 'whatsapp' => '0853 4245 3005', 'instagram' => 'persiden_betepe', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => true]),
            'TODDOPULI' => new Branch(['kode' => 'TODDOPULI', 'nama' => 'Persiden HP Toddopuli', 'is_active' => true, 'alamat' => 'Jl. Pasar Hobby samping halte Damri No.50, Paropo, Makassar', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
            'GOWA' => new Branch(['kode' => 'GOWA', 'nama' => 'President HP Gowa', 'is_active' => true, 'alamat' => 'Jl. Sultan Hasanuddin No.164 B, Pandang Pandang, Somba Opu, Gowa', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
            'DAYA' => new Branch(['kode' => 'DAYA', 'nama' => 'Persiden HP Daya', 'is_active' => true, 'alamat' => 'Jl. Paccerakkang No.82, Paccerakkang, Biringkanaya, Makassar', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
            'RAPPOCINI' => new Branch(['kode' => 'RAPPOCINI', 'nama' => 'Persiden HP Rappocini', 'is_active' => true, 'alamat' => 'Jl. Rappocini Raya No.187b, Bua Kana, Rappocini, Makassar', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
            'UJUNGBORI' => new Branch(['kode' => 'UJUNGBORI', 'nama' => 'Persiden HP Ujung Bori', 'is_active' => true, 'alamat' => 'Jl. Ujung Bori No.22, Antang, Manggala, Makassar', 'telepon' => '', 'jam_buka' => '08.00 - 22.00 WITA', 'is_verified' => false]),
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
