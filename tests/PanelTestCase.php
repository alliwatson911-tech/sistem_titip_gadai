<?php

namespace Tests;

use App\Enums\Role;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Member;
use App\Models\Product;
use App\Models\User;
use App\Services\ApplicationFlow;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

/** Dasar uji panel: 2 cabang + pusat, kebijakan, produk, konten; helper pembuat staf/anggota/pengajuan. */
abstract class PanelTestCase extends TestCase
{
    use RefreshDatabase;

    protected Branch $a;
    protected Branch $b;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([BranchSeeder::class, PolicySeeder::class, ProductSeeder::class, ContentSeeder::class]);
        $this->a = Branch::where('kode', 'A')->firstOrFail();
        $this->b = Branch::where('kode', 'B')->firstOrFail();
        Storage::fake('local');
    }

    protected function staf(Role $role, ?Branch $cabang = null): User
    {
        return User::factory()->peran($role, $role->isBranchBound() ? ($cabang ?? $this->a)->id : null)->create();
    }

    protected function anggota(?Branch $cabang = null, string $status = 'terverifikasi'): Member
    {
        $m = Member::factory()->create(['branch_id' => ($cabang ?? $this->a)->id]);
        $m->forceFill(['status' => $status])->save();

        return $m;
    }

    protected function produk(string $slug = 'gadai-smartphone'): Product
    {
        return Product::where('slug', $slug)->firstOrFail();
    }

    protected function pengajuan(Member $m, int $pinjaman = 2_000_000, string $slug = 'gadai-smartphone', ?User $oleh = null): Application
    {
        return app(ApplicationFlow::class)->ajukan($m, $this->produk($slug), $pinjaman, null, $oleh ?? $m->user);
    }

    /** Pengajuan sampai dicairkan (tunai) lewat service. Mengembalikan Loan aktif. */
    protected function buatGadai(?Member $m = null, int $pinjam = 2_000_000, ?User $teller = null): \App\Models\Loan
    {
        $m ??= $this->anggota();
        $cabang = $m->branch_id === $this->b->id ? $this->b : $this->a;
        $teller ??= $this->staf(Role::Teller, $cabang);
        $a = $this->pengajuan($m, $pinjam);
        $alur = app(ApplicationFlow::class);
        $alur->terimaBarang($a, $teller, ['identitas_dicocokkan' => true, 'merek' => 'Samsung', 'model' => 'A54', 'imei_serial' => (string) random_int(100000000000000, 999999999999999), 'no_segel' => 'SG-'.random_int(1000, 9999)]);
        $alur->taksir($a, $this->staf(Role::Analis, $cabang), ['nilai_pasar' => 3_500_000, 'nilai_taksiran' => 3_000_000, 'periksa' => [], 'rekomendasi' => 'setuju']);

        return app(\App\Services\LoanService::class)->cairkan($a->refresh(), $teller, 'tunai', null);
    }

    /** Data form terima barang yang valid. */
    protected function dataTerima(array $ubah = []): array
    {
        return array_merge([
            'identitas_dicocokkan' => '1', 'merek' => 'Samsung', 'model' => 'Galaxy A54', 'imei_serial' => '356938035643809',
            'kelengkapan' => 'Dus, charger', 'kondisi' => 'Mulus', 'no_segel' => 'SG-0001', 'lokasi_simpan' => 'Brankas 1',
        ], $ubah);
    }

    protected function dataTaksir(array $ubah = []): array
    {
        return array_merge([
            'nilai_pasar' => '3.500.000', 'nilai_taksiran' => '3.000.000',
            'periksa' => ['identitas_cocok' => '1', 'barang_menyala' => '1', 'bebas_kunci' => '1', 'kelengkapan_sesuai' => '1', 'riwayat_baik' => '1'],
            'rekomendasi' => 'setuju', 'catatan' => 'Kondisi baik.',
        ], $ubah);
    }

    /** Bawa pengajuan sampai barang diterima (sebagai teller cabang). */
    protected function sampaiBarangDiterima(Application $a, array $ubah = []): void
    {
        $this->actingAs($this->staf(Role::Teller, $a->branch_id === $this->b->id ? $this->b : $this->a))
            ->post(route('panel.pengajuan.terima', $a), $this->dataTerima($ubah))->assertSessionHasNoErrors();
    }
}
