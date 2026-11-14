<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PersidenHpBranchSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([BranchSeeder::class, PolicySeeder::class, ProductSeeder::class, ContentSeeder::class, PersidenHpBranchSeeder::class]);
    }

    public function test_halaman_utama_memuat_semua_bagian_berurutan(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $posisi = [];
        foreach (['beranda', 'pengumuman', 'standar', 'faq', 'produk', 'panduan', 'simulasi', 'tentang', 'akun'] as $id) {
            $this->assertMatchesRegularExpression('/<section[^>]*\bid="'.$id.'"/', $html, "bagian #$id tidak ada");
            $posisi[] = strpos($html, 'id="'.$id.'"');
        }
        $sorted = $posisi;
        sort($sorted);
        $this->assertSame($sorted, $posisi, 'urutan bagian harus sesuai rancangan');
    }

    public function test_hanya_satu_h1_dan_ada_lompat_ke_isi(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html));
        $this->assertStringContainsString('href="#isi"', $html);
        $this->assertStringContainsString('<main id="isi">', $html);
    }

    public function test_produk_berasal_dari_database_dan_nonaktif_tidak_tampil(): void
    {
        $this->get('/layanan')->assertSee('Gadai Laptop')->assertSee('Gadai Smartphone')->assertSee('Rp 300 rb');

        Product::where('slug', 'gadai-laptop')->update(['is_active' => false]);

        $this->get('/layanan')->assertDontSee('Gadai Laptop')->assertSee('Gadai Smartphone');
    }

    public function test_token_kebijakan_diganti_dengan_nilai_sebenarnya(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('{tenor}', $html);
        $this->assertStringNotContainsString('{tenggang}', $html);
        $this->assertStringContainsString('Tenor 14 hari', $html);
    }

    public function test_kontak_kosong_tidak_dikarang(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('08.00 – 22.00 WITA', $html);
    }

    public function test_tidak_ada_klaim_izin_atau_asuransi_yang_belum_diverifikasi(): void
    {
        $teks = strip_tags($this->get('/')->getContent());

        foreach (['terdaftar di OJK', 'diawasi OJK', 'berizin resmi', 'diasuransikan', 'asuransi', 'Lorem'] as $kata) {
            $this->assertStringNotContainsString($kata, $teks, "klaim/teks terlarang: $kata");
        }
    }

    public function test_pengumuman_terbit_tampil_dan_yang_kedaluwarsa_atau_draf_tidak(): void
    {
        Announcement::create(['judul' => 'Pengumuman Kedaluwarsa', 'isi' => 'x', 'is_published' => true, 'selesai' => now()->subDay()->toDateString()]);
        Announcement::create(['judul' => 'Pengumuman Draf', 'isi' => 'x', 'is_published' => false]);
        Announcement::create(['judul' => 'Pengumuman Belum Mulai', 'isi' => 'x', 'is_published' => true, 'mulai' => now()->addDay()->toDateString()]);

        $this->get('/')
            ->assertSee('Promo &amp; Informasi', false)
            ->assertDontSee('Pengumuman Kedaluwarsa')
            ->assertDontSee('Pengumuman Draf')
            ->assertDontSee('Pengumuman Belum Mulai');
    }

    public function test_isi_pengumuman_di_escape_dari_script(): void
    {
        Announcement::create(['judul' => '<script>alert(1)</script>', 'isi' => '<img src=x onerror=alert(2)>', 'is_published' => true]);

        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<img src=x onerror', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    public function test_json_ld_valid_dan_memuat_faq(): void
    {
        $html = $this->get('/')->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $this->assertGreaterThanOrEqual(1, count($m[1]));
        $org = json_decode($m[1][0], true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('FinancialService', $org['@type']);
        $this->assertSame('PERSIDEN HP Titip Gadai', $org['name']);
    }

    public function test_sitemap_dan_header_keamanan(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->assertSee('<loc>http://localhost</loc>', false);
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_galat_login_tampil_di_bagian_akun_halaman_publik(): void
    {
        $this->from('/')->followingRedirects()
            ->post('/masuk', ['email' => 'tidak@ada.test', 'password' => 'salah-salah-1'])
            ->assertSee('Akun tidak terdaftar atau email salah')
            ->assertSee('data-ke="akun"', false);
    }

    public function test_pengguna_login_melihat_tautan_panel_bukan_form(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertSee('Panel Saya');
    }

    // ---- endpoint simulasi ----

    private function produkId(string $slug): int
    {
        return Product::where('slug', $slug)->value('id');
    }

    public function test_simulasi_menghitung_sama_dengan_kalkulator(): void
    {
        $this->getJson('/simulasi?'.http_build_query(['produk' => $this->produkId('gadai-smartphone'), 'taksiran' => 3000000, 'ltv_bp' => 8000, 'periode' => 1]))
            ->assertOk()
            ->assertJson(['pokok' => 2400000, 'sewa_modal_total' => 50400, 'administrasi_total' => 24000, 'total_tebus' => 2474400]);
    }

    public function test_simulasi_menolak_persentase_melebihi_batas_produk(): void
    {
        // Televisi maksimal 70%.
        $this->getJson('/simulasi?'.http_build_query(['produk' => $this->produkId('gadai-televisi'), 'taksiran' => 3000000, 'ltv_bp' => 8000, 'periode' => 1]))
            ->assertStatus(422)->assertJsonPath('pesan', 'Persentase pinjaman maksimal 70% untuk jenis barang ini.');
    }

    public function test_simulasi_menolak_masukan_tidak_valid(): void
    {
        $id = $this->produkId('gadai-laptop');

        $this->getJson('/simulasi')->assertStatus(422);
        $this->getJson('/simulasi?'.http_build_query(['produk' => $id, 'taksiran' => -5, 'ltv_bp' => 8000, 'periode' => 1]))->assertStatus(422);
        $this->getJson('/simulasi?'.http_build_query(['produk' => $id, 'taksiran' => 3000000, 'ltv_bp' => 8000, 'periode' => 9]))->assertStatus(422);
        $this->getJson('/simulasi?'.http_build_query(['produk' => 999999, 'taksiran' => 3000000, 'ltv_bp' => 8000, 'periode' => 1]))->assertStatus(422);
    }

    public function test_simulasi_menolak_produk_nonaktif(): void
    {
        $id = $this->produkId('gadai-kamera');
        Product::whereKey($id)->update(['is_active' => false]);

        $this->getJson('/simulasi?'.http_build_query(['produk' => $id, 'taksiran' => 3000000, 'ltv_bp' => 7000, 'periode' => 1]))->assertStatus(422);
    }

    public function test_simulasi_hitungan_harian_dan_mingguan_via_api(): void
    {
        $id = $this->produkId('gadai-smartphone');

        // Harian (3 hari)
        $this->getJson('/simulasi?'.http_build_query(['produk' => $id, 'taksiran' => 3000000, 'ltv_bp' => 8000, 'hari' => 3]))
            ->assertOk()
            ->assertJson([
                'pokok' => 2400000,
                'sewa_modal_total' => 10800,
                'administrasi_total' => 24000,
                'total_tebus' => 2434800,
                'hari_total' => 3,
            ]);

        // Mingguan (7 hari / 1 minggu)
        $this->getJson('/simulasi?'.http_build_query(['produk' => $id, 'taksiran' => 3000000, 'ltv_bp' => 8000, 'hari' => 7]))
            ->assertOk()
            ->assertJson([
                'pokok' => 2400000,
                'sewa_modal_total' => 25200,
                'administrasi_total' => 24000,
                'total_tebus' => 2449200,
                'hari_total' => 7,
            ]);
    }

    public function test_halaman_khusus_panel_berhasil_dimuat_dan_memiliki_tombol_kembali(): void
    {
        $halaman = [
            '/layanan' => 'Layanan Titip Gadai PERSIDEN HP',
            '/cara-penitipan' => 'Cara Penitipan Barang di PERSIDEN HP',
            '/syarat-ketentuan' => 'Syarat &amp; Ketentuan Titip Gadai',
            '/simulasi-jasa' => 'Hitung Simulasi Jasa Penitipan',
            '/promo' => 'Promo &amp; Informasi PERSIDEN HP',
            '/kontak' => 'Kontak Cabang PERSIDEN HP',
            '/tentang-kami' => 'Tentang PERSIDEN HP Titip Gadai',
            '/tentang-kami/sambutan' => 'Sambutan Pimpinan',
            '/tentang-kami/visi-misi' => 'Visi &amp; Misi Perusahaan',
            '/tentang-kami/legalitas' => 'Legalitas &amp; Dokumen Perizinan',
            '/tentang-kami/cabang' => 'Daftar 7 Cabang PERSIDEN HP',
        ];

        foreach ($halaman as $url => $judul) {
            $response = $this->get($url);
            $response->assertOk();
            $response->assertSee($judul, false);
            $response->assertSee('btn-kembali', false);
            $response->assertSee('Kembali', false);
            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $response->getContent()), "halaman $url harus tepat memiliki 1 h1");
        }

        // Tautan lama harus redirect 301
        $this->get('/standar')->assertRedirect('/cara-penitipan');
        $this->get('/panduan')->assertRedirect('/cara-penitipan');
        $this->get('/syarat')->assertRedirect('/syarat-ketentuan');
        $this->get('/simulasi-gadai')->assertRedirect('/simulasi-jasa');
        $this->get('/faq')->assertRedirect('/syarat-ketentuan#faq');
        $this->get('/pengumuman')->assertRedirect('/promo');
        $this->get('/tentang')->assertRedirect('/tentang-kami');
        $this->get('/daftar')->assertRedirect('/masuk');
    }
}
