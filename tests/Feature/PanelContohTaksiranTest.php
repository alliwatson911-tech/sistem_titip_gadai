<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use Tests\PanelTestCase;

class PanelContohTaksiranTest extends PanelTestCase
{
    private function laptop(): Product
    {
        return Product::where('kategori_barang', 'laptop')->firstOrFail();
    }

    public function test_owner_menyimpan_contoh_dan_angka_bertitik_diurai(): void
    {
        $this->actingAs($this->staf(Role::Owner))
            ->put(route('panel.contoh.update', $this->laptop()), ['contoh' => "Asus Vivobook | 3.500.000 | 5.000.000\n\nHP 14 | 4500000 | 6500000"])
            ->assertSessionHasNoErrors();

        $this->assertSame([
            ['label' => 'Asus Vivobook', 'min' => 3_500_000, 'max' => 5_000_000],
            ['label' => 'HP 14', 'min' => 4_500_000, 'max' => 6_500_000],
        ], $this->laptop()->refresh()->contoh_taksiran);
    }

    public function test_baris_kosong_menghapus_contoh_dan_input_tak_valid_ditolak(): void
    {
        $owner = $this->staf(Role::Owner);
        $url = route('panel.contoh.update', $this->laptop());

        $this->actingAs($owner)->put($url, ['contoh' => 'tanpa pemisah'])->assertSessionHasErrorsIn('p'.$this->laptop()->id, 'contoh');
        $this->actingAs($owner)->put($url, ['contoh' => 'Terbalik | 9.000.000 | 1.000.000'])->assertSessionHasErrorsIn('p'.$this->laptop()->id, 'contoh');
        $this->actingAs($owner)->put($url, ['contoh' => str_repeat("A | 1 | 2\n", 13)])->assertSessionHasErrorsIn('p'.$this->laptop()->id, 'contoh');

        $this->actingAs($owner)->put($url, ['contoh' => ''])->assertSessionHasNoErrors();
        $this->assertNull($this->laptop()->refresh()->contoh_taksiran);
    }

    public function test_hanya_owner_yang_boleh_mengakses(): void
    {
        $url = route('panel.contoh.update', $this->laptop());

        $this->actingAs($this->staf(Role::Admin))->get(route('panel.contoh.index'))->assertForbidden();
        $this->actingAs($this->staf(Role::Admin))->put($url, ['contoh' => 'X | 1 | 2'])->assertForbidden();
        $this->actingAs($this->staf(Role::Owner))->get(route('panel.contoh.index'))->assertOk()->assertSee($this->laptop()->nama);
    }
}
