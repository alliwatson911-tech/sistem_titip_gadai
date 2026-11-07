<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus as S;
use App\Enums\Role;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Collateral;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\PanelTestCase;

class PengajuanAlurTest extends PanelTestCase
{
    // ---------- pengajuan oleh anggota ----------

    public function test_anggota_terverifikasi_mengajukan_lewat_form(): void
    {
        $m = $this->anggota();

        $r = $this->actingAs($m->user)->post(route('panel.pengajuan.store'), [
            'product_id' => $this->produk()->id, 'pinjaman_diminta' => '2.000.000', 'catatan_member' => 'Butuh minggu ini',
        ]);

        $a = Application::withoutGlobalScopes()->firstOrFail();
        $r->assertRedirect(route('panel.pengajuan.show', $a));
        $this->assertSame(S::MenungguBarang, $a->status);
        $this->assertSame(2_000_000, (int) $a->pinjaman_diminta);
        $this->assertSame($m->branch_id, $a->branch_id);
        $this->assertSame(today()->addDays(7)->toDateString(), $a->batas_bawa_barang->toDateString());
    }

    public function test_status_dan_pinjaman_disetujui_tidak_bisa_diisi_lewat_form(): void
    {
        $m = $this->anggota();

        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), [
            'product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000', 'status' => 'disetujui', 'pinjaman_disetujui' => '9999999',
        ]);

        $a = Application::withoutGlobalScopes()->firstOrFail();
        $this->assertSame(S::MenungguBarang, $a->status);
        $this->assertNull($a->pinjaman_disetujui);
    }

    public function test_anggota_belum_terverifikasi_tidak_bisa_mengajukan(): void
    {
        $m = $this->anggota(null, 'calon');

        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), ['product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000'])
            ->assertSessionHasErrors('umum');
        $this->assertSame(0, Application::withoutGlobalScopes()->count());
    }

    public function test_pinjaman_di_luar_batas_produk_ditolak(): void
    {
        $m = $this->anggota();

        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), ['product_id' => $this->produk()->id, 'pinjaman_diminta' => '50000'])->assertSessionHasErrors('umum');
        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), ['product_id' => $this->produk()->id, 'pinjaman_diminta' => '30000000'])->assertSessionHasErrors('umum');
        $this->assertSame(0, Application::withoutGlobalScopes()->count());
    }

    public function test_maksimal_tiga_pengajuan_terbuka(): void
    {
        $m = $this->anggota();
        foreach (range(1, 3) as $_) {
            $this->pengajuan($m);
        }

        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), ['product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000'])
            ->assertSessionHasErrors('umum');
        $this->assertSame(3, Application::withoutGlobalScopes()->count());
    }

    public function test_teller_membuat_pengajuan_atas_nama_anggota_cabangnya_saja(): void
    {
        $teller = $this->staf(Role::Teller, $this->a);
        $milikA = $this->anggota($this->a);
        $milikB = $this->anggota($this->b);

        $this->actingAs($teller)->post(route('panel.pengajuan.store'), ['member_id' => $milikA->id, 'product_id' => $this->produk()->id, 'pinjaman_diminta' => '1500000'])
            ->assertSessionHasNoErrors();
        $this->assertSame($teller->id, Application::withoutGlobalScopes()->firstOrFail()->created_by);

        // Anggota cabang lain tidak ditemukan (global scope) -> 404.
        $this->actingAs($teller)->post(route('panel.pengajuan.store'), ['member_id' => $milikB->id, 'product_id' => $this->produk()->id, 'pinjaman_diminta' => '1500000'])
            ->assertNotFound();
    }

    // ---------- alur lengkap ----------

    public function test_alur_di_bawah_limit_sampai_disetujui(): void
    {
        $m = $this->anggota();
        $a = $this->pengajuan($m, 2_000_000);

        // Teller menerima barang + foto
        $teller = $this->staf(Role::Teller, $this->a);
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima() + [
            'foto' => ['depan' => UploadedFile::fake()->image('depan.jpg'), 'segel' => UploadedFile::fake()->image('segel.png')],
        ])->assertRedirect(route('panel.pengajuan.show', $a))->assertSessionHasNoErrors();

        $a->refresh();
        $this->assertSame(S::BarangDiterima, $a->status);
        $c = Collateral::withoutGlobalScopes()->firstOrFail();
        $this->assertSame('diterima', $c->status);
        $this->assertSame('356938035643809', $c->imei_serial_enc);
        $this->assertNotSame('356938035643809', DB::table('collaterals')->value('imei_serial_enc'), 'IMEI harus terenkripsi di database');
        $this->assertCount(2, $c->photos);
        foreach ($c->photos as $f) {
            Storage::disk('local')->assertExists($f->path);
        }

        // Analis menaksir: taksiran 3 juta x 80% = plafon 2,4 juta; diminta 2 juta -> disetujui 2 juta (<= limit 5 juta)
        $analis = $this->staf(Role::Analis, $this->a);
        $this->actingAs($analis)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir())->assertSessionHasNoErrors();

        $a->refresh();
        $this->assertSame(S::Disetujui, $a->status);
        $this->assertSame(2_000_000, (int) $a->pinjaman_disetujui);
        $t = $a->taksiranSaatIni();
        $this->assertSame(2_400_000, (int) $t->plafon_maks);
        $this->assertSame(100, $t->skor_kelayakan);
        $this->assertSame(1, $a->approvals()->where('level', 'analis')->count());
    }

    public function test_pinjaman_di_atas_limit_menunggu_manager_lalu_disetujui(): void
    {
        $a = $this->pengajuan($this->anggota(), 8_000_000, 'gadai-laptop');
        $this->sampaiBarangDiterima($a);

        // taksiran 12 juta x 80% = 9,6 juta; diminta 8 juta -> disetujui 8 juta > 5 juta -> menunggu Manager
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['nilai_pasar' => '14000000', 'nilai_taksiran' => '12000000']));
        $a->refresh();
        $this->assertSame(S::MenungguManager, $a->status);
        $this->assertSame(8_000_000, (int) $a->pinjaman_disetujui);

        $this->actingAs($this->staf(Role::Manager, $this->a))->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'setuju', 'catatan' => 'OK'])
            ->assertSessionHasNoErrors();

        $this->assertSame(S::Disetujui, $a->refresh()->status);
        $this->assertSame(1, $a->approvals()->where('level', 'manager')->where('keputusan', 'setuju')->count());
    }

    public function test_pinjaman_di_atas_plafon_dipangkas_ke_plafon(): void
    {
        $a = $this->pengajuan($this->anggota(), 3_000_000);
        $this->sampaiBarangDiterima($a);

        // taksiran 2 juta x 80% = 1,6 juta < diminta 3 juta -> disetujui 1,6 juta
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['nilai_pasar' => '2500000', 'nilai_taksiran' => '2000000']));

        $this->assertSame(1_600_000, (int) $a->refresh()->pinjaman_disetujui);
    }

    public function test_manager_menolak_wajib_catatan(): void
    {
        $a = $this->pengajuan($this->anggota(), 8_000_000, 'gadai-laptop');
        $this->sampaiBarangDiterima($a);
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['nilai_pasar' => '14000000', 'nilai_taksiran' => '12000000']));
        $manager = $this->staf(Role::Manager, $this->a);

        $this->actingAs($manager)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'tolak', 'catatan' => ''])->assertSessionHasErrors('umum');
        $this->assertSame(S::MenungguManager, $a->refresh()->status);

        $this->actingAs($manager)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'tolak', 'catatan' => 'Risiko tinggi'])->assertSessionHasNoErrors();
        $this->assertSame(S::Ditolak, $a->refresh()->status);
    }

    public function test_analis_menolak_lalu_barang_dikembalikan_teller(): void
    {
        $a = $this->pengajuan($this->anggota());
        $this->sampaiBarangDiterima($a);
        $analis = $this->staf(Role::Analis, $this->a);

        $this->actingAs($analis)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['rekomendasi' => 'tolak', 'catatan' => '']))->assertSessionHasErrors('umum');
        $this->actingAs($analis)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['rekomendasi' => 'tolak', 'catatan' => 'Layar retak parah']))->assertSessionHasNoErrors();
        $this->assertSame(S::Ditolak, $a->refresh()->status);

        $this->actingAs($this->staf(Role::Teller, $this->a))->post(route('panel.pengajuan.kembalikan', $a))->assertSessionHasNoErrors();
        $this->assertSame('dikeluarkan', Collateral::withoutGlobalScopes()->firstOrFail()->status);
    }

    public function test_plafon_di_bawah_minimum_produk_tidak_bisa_disetujui(): void
    {
        $a = $this->pengajuan($this->anggota(), 500_000);
        $this->sampaiBarangDiterima($a);

        // taksiran 100 ribu x 80% = 80 ribu < minimum 100 ribu
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['nilai_pasar' => '120000', 'nilai_taksiran' => '100000']))
            ->assertSessionHasErrors('umum');
        $this->assertSame(S::BarangDiterima, $a->refresh()->status);
    }

    public function test_langkah_tidak_bisa_diulang_atau_dilompati(): void
    {
        $a = $this->pengajuan($this->anggota());
        $teller = $this->staf(Role::Teller, $this->a);
        $analis = $this->staf(Role::Analis, $this->a);

        // taksir sebelum barang diterima ditolak
        $this->actingAs($analis)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir())->assertSessionHasErrors('umum');

        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima())->assertSessionHasNoErrors();
        // terima dua kali ditolak
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima(['imei_serial' => '111111111111111']))->assertSessionHasErrors('umum');
        $this->assertSame(1, Collateral::withoutGlobalScopes()->count());
    }

    public function test_identitas_wajib_dicocokkan_dan_data_barang_wajib(): void
    {
        $a = $this->pengajuan($this->anggota());
        $teller = $this->staf(Role::Teller, $this->a);

        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima(['identitas_dicocokkan' => '']))->assertSessionHasErrors('identitas_dicocokkan');
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima(['no_segel' => '', 'merek' => '']))->assertSessionHasErrors(['no_segel', 'merek']);
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a), $this->dataTerima(['foto' => ['depan' => UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')]]))->assertSessionHasErrors('foto.depan');
        $this->assertSame(S::MenungguBarang, $a->refresh()->status);
    }

    public function test_imei_ganda_pada_barang_yang_masih_dititipkan_ditolak(): void
    {
        $a1 = $this->pengajuan($this->anggota());
        $this->sampaiBarangDiterima($a1);

        $a2 = $this->pengajuan($this->anggota());
        $teller = $this->staf(Role::Teller, $this->a);
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a2), $this->dataTerima(['no_segel' => 'SG-0002']))->assertSessionHasErrors('umum');
        $this->assertStringContainsString('IMEI', session('errors')->first('umum'));

        // Setelah barang pertama keluar, nomor yang sama boleh dipakai lagi.
        Collateral::withoutGlobalScopes()->update(['status' => 'dikeluarkan']);
        $this->actingAs($teller)->post(route('panel.pengajuan.terima', $a2), $this->dataTerima(['no_segel' => 'SG-0002']))->assertSessionHasNoErrors();
    }

    public function test_anggota_membatalkan_hanya_selama_menunggu_barang(): void
    {
        $m = $this->anggota();
        $a = $this->pengajuan($m);

        $this->actingAs($m->user)->post(route('panel.pengajuan.batal', $a))->assertSessionHasNoErrors();
        $this->assertSame(S::Dibatalkan, $a->refresh()->status);

        $b = $this->pengajuan($m);
        $this->sampaiBarangDiterima($b);
        $this->actingAs($m->user)->post(route('panel.pengajuan.batal', $b))->assertSessionHasErrors('umum');
        $this->assertSame(S::BarangDiterima, $b->refresh()->status);
    }

    // ---------- hak akses dan pemisahan tugas ----------

    public function test_peran_tidak_berwenang_ditolak_403(): void
    {
        $m = $this->anggota();
        $a = $this->pengajuan($m);
        $analis = $this->staf(Role::Analis, $this->a);
        $teller = $this->staf(Role::Teller, $this->a);
        $manager = $this->staf(Role::Manager, $this->a);
        $admin = $this->staf(Role::Admin, $this->a);

        $this->actingAs($analis)->post(route('panel.pengajuan.terima', $a), $this->dataTerima())->assertForbidden();
        $this->actingAs($teller)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir())->assertForbidden();
        $this->actingAs($manager)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir())->assertForbidden();
        $this->actingAs($analis)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'setuju'])->assertForbidden();
        $this->actingAs($teller)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'setuju'])->assertForbidden();
        $this->actingAs($m->user)->post(route('panel.pengajuan.terima', $a), $this->dataTerima())->assertForbidden();
        $this->actingAs($m->user)->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir())->assertForbidden();
        $this->actingAs($m->user)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'setuju'])->assertForbidden();
        $this->actingAs($admin)->post(route('panel.pengajuan.terima', $a), $this->dataTerima())->assertForbidden();
        $this->actingAs($analis)->post(route('panel.pengajuan.store'), ['product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000'])->assertForbidden();
    }

    public function test_pembuat_tidak_dapat_menyetujui_pengajuannya_sendiri(): void
    {
        $manager = $this->staf(Role::Manager, $this->a);
        $a = $this->pengajuan($this->anggota(), 8_000_000, 'gadai-laptop', $manager); // dibuat oleh manager
        $this->sampaiBarangDiterima($a);
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['nilai_pasar' => '14000000', 'nilai_taksiran' => '12000000']));

        $this->actingAs($manager)->post(route('panel.pengajuan.putuskan', $a), ['keputusan' => 'setuju'])->assertSessionHasErrors('umum');
        $this->assertSame(S::MenungguManager, $a->refresh()->status);
    }

    // ---------- isolasi ----------

    public function test_isolasi_cabang_dan_anggota(): void
    {
        $milikA = $this->anggota($this->a);
        $milikB = $this->anggota($this->b);
        $pa = $this->pengajuan($milikA);
        $pb = $this->pengajuan($milikB);

        $tellerA = $this->staf(Role::Teller, $this->a);
        $this->actingAs($tellerA)->get(route('panel.pengajuan.show', $pa))->assertOk();
        $this->actingAs($tellerA)->get(route('panel.pengajuan.show', $pb))->assertNotFound();
        $this->actingAs($tellerA)->post(route('panel.pengajuan.terima', $pb), $this->dataTerima())->assertNotFound();
        $this->actingAs($tellerA)->get(route('panel.pengajuan.index'))->assertSee($pa->kode)->assertDontSee($pb->kode);

        $this->actingAs($milikA->user)->get(route('panel.pengajuan.show', $pa))->assertOk();
        $this->actingAs($milikA->user)->get(route('panel.pengajuan.show', $pb))->assertNotFound();
        $this->actingAs($milikA->user)->get(route('panel.pengajuan.index'))->assertDontSee($pb->kode);

        $this->actingAs($this->staf(Role::Owner))->get(route('panel.pengajuan.index'))->assertSee($pa->kode)->assertSee($pb->kode);
    }

    public function test_anggota_tidak_melihat_foto_dan_detail_taksiran(): void
    {
        $m = $this->anggota();
        $a = $this->pengajuan($m);
        $this->actingAs($this->staf(Role::Teller, $this->a))->post(route('panel.pengajuan.terima', $a), $this->dataTerima() + ['foto' => ['depan' => UploadedFile::fake()->image('d.jpg')]]);
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir(['catatan' => 'CATATAN-RAHASIA-ANALIS']));

        $this->actingAs($m->user)->get(route('panel.pengajuan.show', $a))->assertOk()
            ->assertDontSee('CATATAN-RAHASIA-ANALIS')->assertDontSee('Barang jaminan')->assertDontSee('SG-0001')->assertSee('Plafon hasil taksiran');
    }

    public function test_berkas_foto_hanya_untuk_staf_cabang_yang_sama(): void
    {
        $m = $this->anggota();
        $a = $this->pengajuan($m);
        $this->actingAs($this->staf(Role::Teller, $this->a))->post(route('panel.pengajuan.terima', $a), $this->dataTerima() + ['foto' => ['depan' => UploadedFile::fake()->image('d.jpg')]]);
        $foto = Collateral::withoutGlobalScopes()->firstOrFail()->photos()->firstOrFail();
        $url = route('panel.berkas.foto', $foto);

        $this->actingAs($this->staf(Role::Analis, $this->a))->get($url)->assertOk();
        $this->actingAs($this->staf(Role::Analis, $this->b))->get($url)->assertNotFound();
        $this->actingAs($m->user)->get($url)->assertForbidden();
        auth()->logout();
        $this->get($url)->assertRedirect('/masuk');
        $this->assertFalse(Storage::disk('public')->exists($foto->path), 'foto tidak boleh ada di disk publik');
    }

    // ---------- kedaluwarsa dan audit ----------

    public function test_pengajuan_yang_barangnya_tidak_dibawa_kedaluwarsa(): void
    {
        $m = $this->anggota();
        $lewat = $this->pengajuan($m);
        $masih = $this->pengajuan($m);
        Application::withoutGlobalScopes()->whereKey($lewat->id)->update(['batas_bawa_barang' => today()->subDay()]);

        Artisan::call('pengajuan:kedaluwarsa');

        $this->assertSame(S::Kedaluwarsa, $lewat->refresh()->status);
        $this->assertSame(S::MenungguBarang, $masih->refresh()->status);
    }

    public function test_setiap_perubahan_status_tercatat_di_audit(): void
    {
        $a = $this->pengajuan($this->anggota());
        $this->sampaiBarangDiterima($a);
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pengajuan.taksir', $a), $this->dataTaksir());

        $log = AuditLog::where('subject_type', $a->getMorphClass())->where('subject_id', $a->id)->where('aksi', 'application.diubah')->orderBy('id')->get();
        $this->assertGreaterThanOrEqual(2, $log->count());
        $this->assertSame('menunggu_barang', $log->first()->sebelum['status']);
        $this->assertSame('disetujui', $log->last()->sesudah['status']);
        $this->assertNotNull($log->last()->user_id);
    }

    public function test_dashboard_menampilkan_antrean_sesuai_peran(): void
    {
        $this->pengajuan($this->anggota());
        $this->pengajuan($this->anggota());

        $this->actingAs($this->staf(Role::Teller, $this->a))->get(route('panel'))->assertOk()->assertSee('Menunggu barang dibawa');
        $this->actingAs($this->staf(Role::Teller, $this->b))->get(route('panel'))->assertOk()->assertSee('0');
    }
}
