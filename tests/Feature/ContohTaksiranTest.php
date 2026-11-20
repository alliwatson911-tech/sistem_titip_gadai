<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PersidenHpBranchSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContohTaksiranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([BranchSeeder::class, PolicySeeder::class, ProductSeeder::class, ContentSeeder::class, PersidenHpBranchSeeder::class]);
    }

    private function dataProduk(): array
    {
        preg_match('#<script type="application/json" id="data-produk">(.*?)</script>#s', $this->get('/')->assertOk()->getContent(), $m);

        return collect(json_decode($m[1], true))->keyBy('slug')->all();
    }

    public function test_contoh_elektronik_dan_kendaraan_tersedia_dengan_kisaran_valid(): void
    {
        $data = $this->dataProduk();

        foreach (['gadai-smartphone', 'gadai-laptop', 'gadai-bpkb-motor', 'gadai-bpkb-mobil'] as $slug) {
            $this->assertNotEmpty($data[$slug]['contoh'], $slug);
            foreach ($data[$slug]['contoh'] as $c) {
                $this->assertGreaterThan(0, $c['min']);
                $this->assertGreaterThanOrEqual($c['min'], $c['max']);
            }
        }
        $this->assertSame([], $data['gadai-sertifikat-rumah']['contoh']);
    }

    public function test_contoh_simulasi_mengabaikan_baris_tak_lengkap(): void
    {
        $p = new Product(['contoh_taksiran' => [['label' => 'x'], ['label' => 'ok', 'min' => 1, 'max' => 2]]]);

        $this->assertSame([['label' => 'ok', 'min' => 1, 'max' => 2]], $p->contohSimulasi());
    }
}
