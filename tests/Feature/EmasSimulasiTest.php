<?php

namespace Tests\Feature;

use App\Enums\Role;
use Database\Seeders\PersidenHpBranchSeeder;
use Illuminate\Support\Facades\DB;
use Tests\PanelTestCase;

class EmasSimulasiTest extends PanelTestCase
{
    private function produkPublik(): array
    {
        preg_match('#<script type="application/json" id="data-produk">(.*?)</script>#s', $this->get('/')->assertOk()->getContent(), $m);

        return collect(json_decode($m[1], true))->keyBy('slug')->all();
    }

    private function isiHarga(int $harga, string $sumber = 'Antam, 20 Nov 2026'): void
    {
        $this->actingAs($this->staf(Role::Owner))->put(route('panel.emas.update'), ['harga_emas_gram' => (string) $harga, 'sumber' => $sumber])->assertSessionHasNoErrors();
        auth()->logout();
    }

    public function test_emas_tetap_tampil_di_simulator_setelah_seeder_rebrand_dan_bukan_pilihan_bawaan(): void
    {
        $this->seed(PersidenHpBranchSeeder::class);

        $produk = $this->produkPublik();

        $this->assertArrayHasKey('gadai-emas-perhiasan', $produk);
        $this->assertNotSame('gadai-emas-perhiasan', array_key_first($produk), 'pilihan bawaan tetap barang elektronik');
        $this->assertArrayNotHasKey('gadai-televisi', $produk);
    }

    public function test_data_emas_kosong_sebelum_admin_mengisi_harga_dan_tidak_ada_pada_produk_lain(): void
    {
        $produk = $this->produkPublik();

        $this->assertSame(0, $produk['gadai-emas-perhiasan']['emas']['harga']);
        $this->assertArrayNotHasKey('emas', $produk['gadai-laptop']);
    }

    public function test_owner_menyimpan_harga_emas_dan_sumber_dan_tampil_di_data_publik(): void
    {
        $this->isiHarga(1_250_000);

        $emas = $this->produkPublik()['gadai-emas-perhiasan']['emas'];

        $this->assertSame(1_250_000, $emas['harga']);
        $this->assertSame('Antam, 20 Nov 2026', $emas['sumber']);
        $this->assertFalse($emas['basi']);
        $this->assertContains(22, $emas['karat']);
    }

    public function test_harga_emas_yang_lebih_dari_tujuh_hari_ditandai_basi(): void
    {
        $this->isiHarga(1_250_000);
        DB::table('site_contents')->where('kunci', 'harga_emas_gram')->update(['updated_at' => now()->subDays(10)]);

        $this->assertTrue($this->produkPublik()['gadai-emas-perhiasan']['emas']['basi']);
    }

    public function test_harga_emas_wajib_valid_dan_bersumber(): void
    {
        $owner = $this->staf(Role::Owner);
        $url = route('panel.emas.update');

        $this->actingAs($owner)->put($url, ['harga_emas_gram' => '0', 'sumber' => 'Antam'])->assertSessionHasErrors('harga_emas_gram');
        $this->actingAs($owner)->put($url, ['harga_emas_gram' => 'abc', 'sumber' => 'Antam'])->assertSessionHasErrors('harga_emas_gram');
        $this->actingAs($owner)->put($url, ['harga_emas_gram' => '999999999', 'sumber' => 'Antam'])->assertSessionHasErrors('harga_emas_gram');
        $this->actingAs($owner)->put($url, ['harga_emas_gram' => '1.250.000', 'sumber' => ''])->assertSessionHasErrors('sumber');
    }

    public function test_hanya_owner_yang_boleh_mengubah_harga_emas(): void
    {
        $this->actingAs($this->staf(Role::Admin, $this->a))->put(route('panel.emas.update'), ['harga_emas_gram' => '1250000', 'sumber' => 'Antam'])->assertForbidden();
        $this->assertSame(0, DB::table('site_contents')->where('kunci', 'harga_emas_gram')->count());
    }
}
