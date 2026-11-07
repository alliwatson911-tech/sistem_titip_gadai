<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Services\LoanService;
use App\Services\RingkasanPanel;
use Tests\PanelTestCase;

/** Dashboard Owner/Manager/Admin: isi sesuai peran, isolasi cabang, dan tidak membocorkan uang ke Admin. */
class DashboardEksekutifTest extends PanelTestCase
{
    public function test_pecah_angka_ringkas(): void
    {
        $this->assertSame(['8,4', 'jt'], RingkasanPanel::pecah(8_350_000));
        $this->assertSame(['2', 'jt'], RingkasanPanel::pecah(2_000_000));
        $this->assertSame(['1,5', 'M'], RingkasanPanel::pecah(1_500_000_000));
        $this->assertSame(['500', 'rb'], RingkasanPanel::pecah(500_000));
        $this->assertSame(['950', ''], RingkasanPanel::pecah(950));
    }

    public function test_manager_melihat_kpi_uang_dan_grafik_cabangnya(): void
    {
        $this->buatGadai(null, 2_000_000);
        $r = $this->actingAs($this->staf(Role::Manager))->get(route('panel'))->assertOk();
        $r->assertSee('Panel Operasional', false)->assertSee('Pokok pinjaman berjalan')->assertSee('Pencairan bulan ini')
            ->assertSee('Pendapatan bulan ini')->assertSee('Tren arus dana bulanan')->assertSee('Kesehatan portofolio')
            ->assertSee('Jatuh tempo terdekat')->assertSee('Rp 2.000.000')->assertSee('Menunggu Anda');
        $r->assertDontSee('Perbandingan cabang'); // hanya Owner
    }

    public function test_manager_tidak_melihat_data_cabang_lain(): void
    {
        $milikB = $this->anggota($this->b);
        $this->buatGadai($milikB, 3_000_000);

        // Pinjaman dibatasi plafon 80% dari taksiran 3.000.000 = 2.400.000.
        $this->actingAs($this->staf(Role::Manager, $this->a))->get(route('panel'))->assertOk()
            ->assertDontSee('Rp 2.400.000')->assertDontSee($milikB->nama_lengkap);
        $this->actingAs($this->staf(Role::Manager, $this->b))->get(route('panel'))->assertOk()
            ->assertSee('Rp 2.400.000')->assertSee($milikB->nama_lengkap);
    }

    public function test_owner_melihat_perbandingan_kedua_cabang(): void
    {
        $this->buatGadai(null, 2_000_000);
        $this->buatGadai($this->anggota($this->b), 1_500_000);

        $this->actingAs($this->staf(Role::Owner))->get(route('panel'))->assertOk()
            ->assertSee('Perbandingan cabang')->assertSee($this->a->nama)->assertSee($this->b->nama)->assertSee('Rp 3.500.000');
    }

    public function test_admin_hanya_melihat_angka_keanggotaan_tanpa_uang(): void
    {
        $this->buatGadai(null, 2_000_000);
        $calon = $this->anggota($this->a, 'calon');

        $r = $this->actingAs($this->staf(Role::Admin))->get(route('panel'))->assertOk();
        $r->assertSee('Anggota terverifikasi')->assertSee('Menunggu verifikasi')->assertSee('Pertumbuhan anggota dan pengajuan')
            ->assertSee($calon->nama_lengkap)->assertSee('Status keanggotaan');
        $r->assertDontSee('Rp 2.000.000')->assertDontSee('Pokok pinjaman berjalan')->assertDontSee('Pembayaran terbaru')
            ->assertDontSee('Perbandingan cabang')->assertDontSee('Pendapatan bulan ini');
    }

    public function test_dashboard_kosong_tidak_error_dan_memberi_petunjuk(): void
    {
        foreach ([Role::Owner, Role::Manager, Role::Admin] as $peran) {
            $this->actingAs($this->staf($peran))->get(route('panel'))->assertOk()->assertSee('Belum ada gadai berjalan', false);
        }
    }

    public function test_pembayaran_sah_muncul_di_daftar_terbaru(): void
    {
        $teller = $this->staf(Role::Teller);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        app(LoanService::class)->bayar($l->refresh(), $teller, 'perpanjangan', 'tunai');

        $this->actingAs($this->staf(Role::Manager))->get(route('panel'))->assertOk()
            ->assertSee('Pembayaran terbaru')->assertSee('Perpanjangan')->assertDontSee('Belum ada pembayaran tercatat');
    }

    public function test_peran_lain_tetap_mendapat_ringkasan_lama_dengan_hero_ringan(): void
    {
        $this->actingAs($this->staf(Role::Teller))->get(route('panel'))->assertOk()
            ->assertSee('Menunggu barang dibawa')->assertDontSee('Pokok pinjaman berjalan')->assertDontSee('Panel Operasional');
        $this->actingAs($this->anggota()->user)->get(route('panel'))->assertOk()->assertDontSee('Pokok pinjaman berjalan');
    }

    public function test_pencarian_hero_mengarah_ke_daftar_yang_ada(): void
    {
        $l = $this->buatGadai();
        $this->actingAs($this->staf(Role::Manager))->get(route('panel.gadai.index', ['cari' => $l->no_sbg]))->assertOk()->assertSee($l->no_sbg);
        $this->actingAs($this->staf(Role::Manager))->get(route('panel'))->assertSee('name="cari"', false)->assertSee(route('panel.gadai.index'), false);
        $this->actingAs($this->staf(Role::Admin))->get(route('panel'))->assertSee(route('panel.anggota'), false);
    }
}
