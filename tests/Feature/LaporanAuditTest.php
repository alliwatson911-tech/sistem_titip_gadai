<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\LaporanService;
use App\Services\LoanService;
use App\Support\Csv;
use Illuminate\Http\UploadedFile;
use Tests\PanelTestCase;

class LaporanAuditTest extends PanelTestCase
{
    private function svc(): LoanService
    {
        return app(LoanService::class);
    }

    /**
     * Cabang A: 1 gadai Rp 2 jt dicairkan + 1 perpanjangan tunai (sewa 90.000 + admin 20.000 = 110.000).
     * Cabang B: 1 gadai Rp 1 jt dicairkan + ditebus tunai di hari yang sama (pokok 1.000.000 + sewa 1 blok 15 hari 22.500 + admin 10.000 = 1.032.500).
     */
    private function data(): array
    {
        $tellerA = $this->staf(Role::Teller, $this->a);
        $tellerB = $this->staf(Role::Teller, $this->b);
        $la = $this->buatGadai($this->anggota($this->a), 2_000_000, $tellerA);
        $lb = $this->buatGadai($this->anggota($this->b), 1_000_000, $tellerB);

        $this->svc()->bayar($la, $tellerA, 'perpanjangan', 'tunai');
        $this->svc()->bayar($lb, $tellerB, 'tebus', 'tunai');

        return compact('tellerA', 'tellerB', 'la', 'lb');
    }

    // ---------- angka laporan ----------

    public function test_ringkas_per_cabang_sesuai_hitungan_manual(): void
    {
        $this->data();
        $this->actingAs($this->staf(Role::Owner));
        $r = app(LaporanService::class);

        $a = $r->ringkas(today(), today(), $this->a->id);
        $this->assertSame(['n' => 1, 'total' => 2_000_000, 'tunai' => 2_000_000], $a['pencairan']);
        $this->assertSame(1, $a['pembayaran']['perpanjangan']['n']);
        $this->assertSame(110_000, $a['pembayaran']['perpanjangan']['total']);
        $this->assertSame(0, $a['pembayaran']['tebus']['n']);
        $this->assertSame(110_000, $a['kas_masuk']);
        $this->assertSame(['sewa_modal' => 90_000, 'administrasi' => 20_000, 'denda' => 0, 'total' => 110_000], $a['pendapatan']);
        $this->assertSame(0, $a['pokok_kembali']);
        $this->assertSame(['n' => 1, 'saldo_pokok' => 2_000_000, 'segera' => 0, 'terlambat' => 0], $a['portofolio']);

        $b = $r->ringkas(today(), today(), $this->b->id);
        $this->assertSame(1_000_000, $b['pencairan']['total']);
        $this->assertSame(1_032_500, $b['pembayaran']['tebus']['total']);
        $this->assertSame(1_000_000, $b['pokok_kembali']);
        $this->assertSame(32_500, $b['pendapatan']['total']);
        $this->assertSame(0, $b['portofolio']['n'], 'gadai yang sudah ditebus tidak lagi di portofolio');
        $this->assertSame(1, $b['status_gadai']['ditebus']);
    }

    public function test_total_sama_dengan_jumlah_cabang_dan_cocok_dengan_tabel_pembayaran(): void
    {
        $this->data();
        $this->actingAs($this->staf(Role::Owner));
        $r = app(LaporanService::class);

        $total = $r->ringkas(today(), today(), null);
        $a = $r->ringkas(today(), today(), $this->a->id);
        $b = $r->ringkas(today(), today(), $this->b->id);

        foreach (['kas_masuk', 'pokok_kembali'] as $k) {
            $this->assertSame($a[$k] + $b[$k], $total[$k], $k);
        }
        $this->assertSame($a['pendapatan']['total'] + $b['pendapatan']['total'], $total['pendapatan']['total']);
        $this->assertSame($a['pencairan']['total'] + $b['pencairan']['total'], $total['pencairan']['total']);
        // rekonsiliasi dengan sumber: kas masuk = jumlah total pembayaran sah
        $this->assertSame((int) Payment::withoutGlobalScopes()->where('status', 'sah')->sum('total'), $total['kas_masuk']);
        // pendapatan + pokok kembali = kas masuk
        $this->assertSame($total['kas_masuk'], $total['pendapatan']['total'] + $total['pokok_kembali']);
    }

    public function test_pembayaran_tidak_sah_dan_di_luar_periode_tidak_dihitung(): void
    {
        $d = $this->data();
        $this->svc()->bayar($d['la'], $d['tellerA'], 'perpanjangan', 'transfer', null, UploadedFile::fake()->image('b.jpg')); // menunggu verifikasi
        $this->actingAs($this->staf(Role::Owner));
        $r = app(LaporanService::class);

        $this->assertSame(110_000, $r->ringkas(today(), today(), $this->a->id)['kas_masuk'], 'transfer yang belum diverifikasi tidak dihitung');
        $this->assertSame(0, $r->ringkas(today()->subDays(10), today()->subDays(5), $this->a->id)['kas_masuk'], 'di luar periode');
    }

    // ---------- akses laporan ----------

    public function test_manager_hanya_melihat_cabangnya_dan_tidak_bisa_memilih_cabang_lain(): void
    {
        $this->data();
        $managerA = $this->staf(Role::Manager, $this->a);

        $this->actingAs($managerA)->get(route('panel.laporan.cabang', ['cabang' => $this->b->id]))->assertOk()
            ->assertSee('Cabang A')->assertSee('Rp 2.000.000')->assertDontSee('Rp 1.032.500');
    }

    public function test_owner_memilih_cabang_atau_semua(): void
    {
        $this->data();
        $owner = $this->staf(Role::Owner);

        $this->actingAs($owner)->get(route('panel.laporan.cabang'))->assertOk()->assertSee('Semua cabang')->assertSee('Rp 3.000.000');
        $this->actingAs($owner)->get(route('panel.laporan.cabang', ['cabang' => $this->b->id]))->assertOk()->assertSee('Cabang B')->assertSee('Rp 1.032.500')->assertDontSee('Rp 2.000.000');
    }

    public function test_konsolidasi_hanya_owner(): void
    {
        $this->data();
        $this->actingAs($this->staf(Role::Owner))->get(route('panel.laporan.konsolidasi'))->assertOk()->assertSee('Cabang A')->assertSee('Cabang B')->assertSee('Total');
        foreach ([Role::Manager, Role::Admin, Role::Teller, Role::Analis] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get(route('panel.laporan.konsolidasi'))->assertForbidden();
        }
        $this->actingAs($this->anggota()->user)->get(route('panel.laporan.konsolidasi'))->assertForbidden();
    }

    public function test_laporan_cabang_hanya_owner_dan_manager(): void
    {
        foreach ([Role::Admin, Role::Teller, Role::Analis] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get(route('panel.laporan.cabang'))->assertForbidden();
        }
        $this->actingAs($this->anggota()->user)->get(route('panel.laporan.cabang'))->assertForbidden();
        auth()->logout();
        $this->get(route('panel.laporan.cabang'))->assertRedirect('/masuk');
    }

    public function test_periode_tidak_valid_ditolak(): void
    {
        $owner = $this->staf(Role::Owner);

        $this->actingAs($owner)->get(route('panel.laporan.cabang', ['dari' => '2026-12-10', 'sampai' => '2026-12-01']))->assertStatus(422);
        $this->actingAs($owner)->get(route('panel.laporan.cabang', ['dari' => '2024-01-01', 'sampai' => '2026-01-01']))->assertStatus(422);
        $this->actingAs($owner)->get(route('panel.laporan.cabang', ['dari' => 'bukan-tanggal']))->assertSessionHasErrors('dari');
    }

    // ---------- laporan harian teller ----------

    public function test_harian_teller_menghitung_kas_tunai_bersih_dan_hanya_miliknya(): void
    {
        $d = $this->data(); // tellerA: cair 2.000.000 tunai, terima 110.000 tunai
        $this->actingAs($d['tellerA']);
        $h = app(LaporanService::class)->harian($d['tellerA'], today());

        $this->assertSame(110_000, $h['masuk_tunai']);
        $this->assertSame(2_000_000, $h['keluar_tunai']);
        $this->assertSame(-1_890_000, $h['kas_tunai_bersih']);
        $this->assertCount(1, $h['pembayaran']);
        $this->assertCount(1, $h['pencairan']);

        $this->actingAs($d['tellerA'])->get(route('panel.laporan.harian'))->assertOk()->assertSee('Kas tunai bersih')->assertDontSee('Rp 1.032.500');
        $this->actingAs($d['tellerB'])->get(route('panel.laporan.harian'))->assertOk()->assertSee('Rp 1.032.500')->assertDontSee('Rp 110.000');
    }

    public function test_harian_transfer_dipisah_dari_tunai_dan_tanggal_masa_depan_ditolak(): void
    {
        $d = $this->data();
        $this->svc()->bayar($d['la'], $d['tellerA'], 'perpanjangan', 'transfer', null, UploadedFile::fake()->image('b.jpg'));
        $this->actingAs($d['tellerA']);
        $h = app(LaporanService::class)->harian($d['tellerA'], today());

        $this->assertSame(110_000, $h['masuk_tunai'], 'transfer tidak masuk kas tunai');
        $this->assertSame(0, $h['transfer_sah']);
        $this->assertSame(110_000, $h['transfer_menunggu']);
        $this->actingAs($d['tellerA'])->get(route('panel.laporan.harian', ['tanggal' => today()->addDay()->toDateString()]))->assertSessionHasErrors('tanggal');
    }

    public function test_harian_hanya_teller(): void
    {
        foreach ([Role::Owner, Role::Manager, Role::Admin, Role::Analis] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get(route('panel.laporan.harian'))->assertForbidden();
        }
    }

    // ---------- CSV ----------

    public function test_ekspor_csv_valid_tercatat_dan_aman(): void
    {
        $this->data();
        $owner = $this->staf(Role::Owner);

        $r = $this->actingAs($owner)->get(route('panel.laporan.cabang', ['format' => 'csv', 'cabang' => $this->a->id]));
        $r->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $isi = $r->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $isi);
        $this->assertStringContainsString('"Pencairan — total dana",2000000', $isi); // fputcsv mengutip sel berspasi
        $this->assertStringContainsString('"Perpanjangan — total diterima",110000', $isi);
        $this->assertTrue(AuditLog::where('aksi', 'laporan.ekspor')->exists());

        $k = $this->actingAs($owner)->get(route('panel.laporan.konsolidasi', ['format' => 'csv']))->streamedContent();
        $this->assertStringContainsString('Metrik,"Cabang A","Cabang B",Total', $k);
    }

    public function test_csv_menetralkan_rumus_spreadsheet(): void
    {
        foreach (['=1+1', '+SUM(A1)', '-2+3', '@cmd', "\tTAB", "\rCR"] as $berbahaya) {
            $this->assertStringStartsWith("'", Csv::sel($berbahaya), $berbahaya);
        }
        $this->assertSame('Cabang A', Csv::sel('Cabang A'));
        $this->assertSame('12345', Csv::sel(12345));
        $this->assertSame('', Csv::sel(''));
    }

    public function test_csv_hanya_untuk_yang_berhak(): void
    {
        $this->actingAs($this->staf(Role::Teller, $this->a))->get(route('panel.laporan.cabang', ['format' => 'csv']))->assertForbidden();
        $this->actingAs($this->staf(Role::Manager, $this->a))->get(route('panel.laporan.konsolidasi', ['format' => 'csv']))->assertForbidden();
    }

    // ---------- audit ----------

    private function isiAudit(): array
    {
        $a = AuditLog::create(['aksi' => 'uji.cabang_a', 'branch_id' => $this->a->id, 'ip' => '10.0.0.1']);
        $b = AuditLog::create(['aksi' => 'uji.cabang_b', 'branch_id' => $this->b->id, 'ip' => '10.0.0.2']);
        $n = AuditLog::create(['aksi' => 'uji.tanpa_cabang', 'branch_id' => null, 'sebelum' => ['x' => 1], 'sesudah' => ['x' => 2]]);

        return compact('a', 'b', 'n');
    }

    public function test_owner_melihat_semua_audit_manager_hanya_cabangnya(): void
    {
        $this->isiAudit();

        $this->actingAs($this->staf(Role::Owner))->get(route('panel.audit.index'))->assertOk()->assertSee('uji.cabang_a')->assertSee('uji.cabang_b')->assertSee('uji.tanpa_cabang');
        $this->actingAs($this->staf(Role::Manager, $this->a))->get(route('panel.audit.index'))->assertOk()->assertSee('uji.cabang_a')->assertDontSee('uji.cabang_b')->assertDontSee('uji.tanpa_cabang');
    }

    public function test_detail_audit_dibatasi_dan_menampilkan_sebelum_sesudah(): void
    {
        $x = $this->isiAudit();
        $managerA = $this->staf(Role::Manager, $this->a);

        $this->actingAs($managerA)->get(route('panel.audit.show', $x['a']))->assertOk();
        $this->actingAs($managerA)->get(route('panel.audit.show', $x['b']))->assertNotFound();
        $this->actingAs($managerA)->get(route('panel.audit.show', $x['n']))->assertNotFound();
        $this->actingAs($this->staf(Role::Owner))->get(route('panel.audit.show', $x['n']))->assertOk()->assertSee('&quot;x&quot;: 1', false)->assertSee('&quot;x&quot;: 2', false); // JSON di-escape HTML
    }

    public function test_audit_hanya_owner_dan_manager_dan_dapat_disaring(): void
    {
        $this->isiAudit();
        foreach ([Role::Admin, Role::Teller, Role::Analis] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get(route('panel.audit.index'))->assertForbidden();
        }
        $this->actingAs($this->anggota()->user)->get(route('panel.audit.index'))->assertForbidden();

        $owner = $this->staf(Role::Owner);
        $this->actingAs($owner)->get(route('panel.audit.index', ['aksi' => 'uji.cabang_b']))->assertSee('uji.cabang_b')->assertDontSee('uji.cabang_a');
        $this->actingAs($owner)->get(route('panel.audit.index', ['aksi' => '%']))->assertDontSee('uji.cabang_a'); // karakter wildcard tidak lolos sebagai pola
    }

    public function test_audit_xss_dari_data_di_escape(): void
    {
        $x = AuditLog::create(['aksi' => 'uji.xss', 'branch_id' => null, 'sesudah' => ['catatan' => '<script>alert(1)</script>']]);

        $this->actingAs($this->staf(Role::Owner))->get(route('panel.audit.show', $x))->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }
}
