<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccessMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function pengguna(Role $role): User
    {
        $cabang = $role->isBranchBound() ? Branch::factory()->create()->id : null;

        return User::factory()->peran($role, $cabang)->create();
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        foreach (['/panel', '/panel/anggota', '/panel/pengguna'] as $url) {
            $this->get($url)->assertRedirect('/masuk');
        }
    }

    public static function peranSemua(): array
    {
        return array_map(fn (Role $r) => [$r], Role::cases());
    }

    #[DataProvider('peranSemua')]
    public function test_halaman_pengguna_hanya_untuk_owner(Role $role): void
    {
        $r = $this->actingAs($this->pengguna($role))->get('/panel/pengguna');

        $role === Role::Owner ? $r->assertOk() : $r->assertForbidden();
    }

    #[DataProvider('peranSemua')]
    public function test_halaman_anggota_hanya_untuk_staf(Role $role): void
    {
        $r = $this->actingAs($this->pengguna($role))->get('/panel/anggota');

        $role === Role::Member ? $r->assertForbidden() : $r->assertOk();
    }

    #[DataProvider('peranSemua')]
    public function test_semua_peran_bisa_membuka_dashboard(Role $role): void
    {
        $this->actingAs($this->pengguna($role))->get('/panel')->assertOk()->assertSee($role->label());
    }

    /** Titik-titik kritis pemisahan tugas dari matriks (docs §4). */
    public static function ekspektasiIzin(): array
    {
        return [
            'owner tidak mencairkan'              => [Role::Owner, 'pencairan.catat', false],
            'owner tidak mencatat bayar'          => [Role::Owner, 'pembayaran.catat', false],
            'owner mengubah kebijakan'            => [Role::Owner, 'kebijakan.ubah', true],
            'owner laporan konsolidasi'           => [Role::Owner, 'laporan.konsolidasi', true],
            'manager tidak ubah kebijakan'        => [Role::Manager, 'kebijakan.ubah', false],
            'manager memutuskan persetujuan'      => [Role::Manager, 'persetujuan.putuskan', true],
            'manager membatalkan pembayaran'      => [Role::Manager, 'pembayaran.batalkan', true],
            'manager tidak menaksir'              => [Role::Manager, 'taksiran.buat', false],
            'manager tanpa laporan konsolidasi'   => [Role::Manager, 'laporan.konsolidasi', false],
            'admin kelola anggota'                => [Role::Admin, 'anggota.kelola', true],
            'admin kelola konten'                 => [Role::Admin, 'konten.kelola', true],
            'admin tidak mencatat bayar'          => [Role::Admin, 'pembayaran.catat', false],
            'admin tidak mengelola pengguna'      => [Role::Admin, 'pengguna.kelola', false],
            'teller mencairkan'                   => [Role::Teller, 'pencairan.catat', true],
            'teller mencatat bayar'               => [Role::Teller, 'pembayaran.catat', true],
            'teller tidak membatalkan bayar'      => [Role::Teller, 'pembayaran.batalkan', false],
            'teller tidak menyetujui'             => [Role::Teller, 'persetujuan.putuskan', false],
            'teller menerima jaminan'             => [Role::Teller, 'jaminan.terima', true],
            'analis menaksir'                     => [Role::Analis, 'taksiran.buat', true],
            'analis tidak menyetujui'             => [Role::Analis, 'persetujuan.putuskan', false],
            'analis tidak mencairkan'             => [Role::Analis, 'pencairan.catat', false],
            'member mengajukan'                   => [Role::Member, 'pengajuan.buat', true],
            'member tidak melihat anggota'        => [Role::Member, 'anggota.lihat', false],
            'member tidak melihat audit'          => [Role::Member, 'audit.lihat', false],
            'member tidak menyentuh pembayaran'   => [Role::Member, 'pembayaran.catat', false],
        ];
    }

    #[DataProvider('ekspektasiIzin')]
    public function test_izin_sesuai_matriks(Role $role, string $izin, bool $diharapkan): void
    {
        $this->assertSame($diharapkan, $this->pengguna($role)->hasPermission($izin));
    }

    public function test_pengguna_nonaktif_tidak_punya_izin_apa_pun(): void
    {
        $owner = User::factory()->peran(Role::Owner)->nonaktif()->create();

        $this->assertSame([], array_values(array_filter(array_keys(config('permissions')), fn ($i) => $owner->hasPermission($i))));
    }

    public function test_konfigurasi_izin_hanya_berisi_peran_valid(): void
    {
        foreach (config('permissions') as $izin => $peran) {
            $this->assertNotEmpty($peran, "$izin tanpa peran");
            foreach ($peran as $p) {
                $this->assertInstanceOf(Role::class, $p, "$izin memuat nilai non-Role");
            }
        }
    }
}
