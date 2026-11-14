<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Member;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class BranchIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Branch $a;

    private Branch $b;

    private Member $anggotaA;

    private Member $anggotaB;

    private Product $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->a = Branch::factory()->create(['kode' => 'A', 'nama' => 'Cabang A']);
        $this->b = Branch::factory()->create(['kode' => 'B', 'nama' => 'Cabang B']);
        $this->anggotaA = Member::factory()->create(['branch_id' => $this->a->id, 'nama_lengkap' => 'Anggota Alfa']);
        $this->anggotaB = Member::factory()->create(['branch_id' => $this->b->id, 'nama_lengkap' => 'Anggota Bravo']);
        $this->produk = Product::create([
            'nama' => 'Gadai Uji', 'slug' => 'gadai-uji', 'kategori_barang' => 'smartphone',
            'pinjaman_min' => 100000, 'pinjaman_max' => 25000000, 'ltv_max_bp' => 8000, 'tenor_hari' => 30,
            'sewa_modal_per_hari_bp' => 15, 'admin_bp' => 100, 'admin_min' => 5000, 'admin_max' => 125000,
        ]);
    }

    private function staf(Role $role, ?Branch $cabang): User
    {
        return User::factory()->peran($role, $cabang?->id)->create();
    }

    private function pengajuan(Member $m, string $kode): Application
    {
        return Application::create([
            'kode' => $kode, 'member_id' => $m->id, 'branch_id' => $m->branch_id, 'product_id' => $this->produk->id,
            'pinjaman_diminta' => 1000000, 'tenor_hari' => 30, 'created_by' => $m->user_id,
        ]);
    }

    public function test_staf_cabang_hanya_melihat_anggota_cabangnya(): void
    {
        $this->actingAs($this->staf(Role::Admin, $this->a));

        $this->assertSame([$this->anggotaA->id], Member::pluck('id')->all());
        $this->assertNull(Member::find($this->anggotaB->id), 'akses langsung via id lintas cabang harus gagal');
    }

    public function test_owner_melihat_semua_cabang(): void
    {
        $this->actingAs($this->staf(Role::Owner, null));

        $this->assertEqualsCanonicalizing([$this->anggotaA->id, $this->anggotaB->id], Member::pluck('id')->all());
    }

    public function test_staf_tanpa_cabang_tidak_melihat_apa_pun(): void
    {
        $this->actingAs($this->staf(Role::Teller, null));

        $this->assertSame(0, Member::count());
    }

    public function test_member_hanya_melihat_data_miliknya_sendiri(): void
    {
        $this->pengajuan($this->anggotaA, 'P-A1');
        $this->pengajuan($this->anggotaB, 'P-B1');

        $this->actingAs($this->anggotaA->user);

        $this->assertSame([$this->anggotaA->id], Member::pluck('id')->all());
        $this->assertSame(['P-A1'], Application::pluck('kode')->all());
    }

    public function test_pengajuan_dipisah_per_cabang_untuk_staf(): void
    {
        $this->pengajuan($this->anggotaA, 'P-A1');
        $this->pengajuan($this->anggotaB, 'P-B1');

        $this->actingAs($this->staf(Role::Analis, $this->b));

        $this->assertSame(['P-B1'], Application::pluck('kode')->all());
    }

    public function test_baris_baru_mewarisi_cabang_staf(): void
    {
        $teller = $this->staf(Role::Teller, $this->a);
        $this->actingAs($teller);

        $app = Application::create([
            'kode' => 'P-X', 'member_id' => $this->anggotaA->id, 'product_id' => $this->produk->id,
            'pinjaman_diminta' => 500000, 'tenor_hari' => 30, 'created_by' => $teller->id,
        ]);

        $this->assertSame($this->a->id, $app->branch_id);
    }

    public function test_halaman_anggota_tidak_membocorkan_cabang_lain(): void
    {
        $this->actingAs($this->staf(Role::Admin, $this->a))
            ->get('/panel/anggota')
            ->assertOk()
            ->assertSee('Anggota Alfa')
            ->assertDontSee('Anggota Bravo');
    }

    public function test_halaman_anggota_menyamarkan_nik(): void
    {
        $nik = $this->anggotaA->nik_enc;

        $this->actingAs($this->staf(Role::Admin, $this->a))
            ->get('/panel/anggota')
            ->assertDontSee($nik);
    }

    public function test_audit_log_tidak_bisa_diubah_atau_dihapus(): void
    {
        $log = AuditLog::first();

        try {
            $log->update(['aksi' => 'dimanipulasi']);
            $this->fail('update audit harus ditolak');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        $this->expectException(LogicException::class);
        $log->delete();
    }
}
