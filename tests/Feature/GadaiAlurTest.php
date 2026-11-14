<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus as S;
use App\Enums\LoanStatus as L;
use App\Enums\Role;
use App\Models\Application;
use App\Models\Auction;
use App\Models\AuditLog;
use App\Models\Collateral;
use App\Models\Loan;
use App\Models\LoanExtension;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Redemption;
use App\Models\User;
use App\Services\ApplicationFlow;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\PanelTestCase;

/**
 * Angka acuan (smartphone, pokok Rp 2.000.000, sewa 0,15%/hari, administrasi 1% = Rp 20.000, tenor 30 hari):
 *   sewa 30 hari = 2.000.000 x 15 x 30 / 10.000 = 90.000 ; sewa 15 hari = 45.000 ; per hari = 3.000.
 */
class GadaiAlurTest extends PanelTestCase
{
    private ?User $teller = null;

    private ?User $analis = null;

    private Carbon $awal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->awal = now()->startOfDay(); // patokan hari ke-0; hariKe() selalu relatif terhadap ini
        $this->teller = $this->staf(Role::Teller, $this->a);
        $this->analis = $this->staf(Role::Analis, $this->a);
    }

    private function svc(): LoanService
    {
        return app(LoanService::class);
    }

    /** Pengajuan -> barang diterima -> taksiran -> disetujui. */
    private function disetujui(?Member $m = null, int $pinjam = 2_000_000): Application
    {
        $m ??= $this->anggota();
        $a = $this->pengajuan($m, $pinjam);
        $alur = app(ApplicationFlow::class);
        $alur->terimaBarang($a, $this->teller, ['identitas_dicocokkan' => true, 'merek' => 'Samsung', 'model' => 'A54', 'imei_serial' => (string) random_int(100000000000000, 999999999999999), 'no_segel' => 'SG-'.random_int(1000, 9999)]);
        $alur->taksir($a, $this->analis, ['nilai_pasar' => 3_500_000, 'nilai_taksiran' => 3_000_000, 'periksa' => [], 'rekomendasi' => 'setuju']);

        return $a->refresh();
    }

    private function gadai(?Member $m = null, int $pinjam = 2_000_000): Loan
    {
        return $this->svc()->cairkan($this->disetujui($m, $pinjam), $this->teller, 'tunai', null);
    }

    private function hariKe(int $n): void
    {
        $this->travelTo($this->awal->copy()->addDays($n)->addHours(9));
    }

    private function bayarTunai(Loan $l, string $tipe, ?int $pokok = null, ?User $oleh = null): Payment
    {
        return $this->svc()->bayar($l, $oleh ?? $this->teller, $tipe, 'tunai', $pokok);
    }

    // ---------- pencairan ----------

    public function test_pencairan_membuat_gadai_aktif_dengan_jatuh_tempo_dan_snapshot(): void
    {
        $a = $this->disetujui();

        $this->actingAs($this->teller)->post(route('panel.gadai.cairkan', $a), ['metode' => 'tunai'])->assertSessionHasNoErrors();

        $l = Loan::withoutGlobalScopes()->firstOrFail();
        $this->assertSame(L::Aktif, $l->status);
        $this->assertSame(2_000_000, (int) $l->pokok_awal);
        $this->assertSame(2_000_000, (int) $l->pokok_saldo);
        $this->assertSame(today()->addDays(30)->toDateString(), $l->jatuh_tempo->toDateString());
        $this->assertSame(today()->addDays(33)->toDateString(), $l->tenggang_sampai->toDateString());
        $this->assertSame(15, $l->snapshot_syarat['sewa_modal_per_hari_bp']);
        $this->assertSame(S::Dicairkan, $a->refresh()->status);
        $this->assertSame('tersimpan', Collateral::withoutGlobalScopes()->firstOrFail()->status);
        $this->assertSame(2_000_000, (int) $l->disbursement->jumlah);
        $this->assertSame(1, $l->histories()->count());
    }

    public function test_pencairan_hanya_teller_dan_tidak_bisa_diulang_atau_dilompati(): void
    {
        $a = $this->disetujui();
        foreach ([Role::Manager, Role::Analis, Role::Admin, Role::Owner] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->post(route('panel.gadai.cairkan', $a), ['metode' => 'tunai'])->assertForbidden();
        }
        $this->actingAs($a->member->user)->post(route('panel.gadai.cairkan', $a), ['metode' => 'tunai'])->assertForbidden();
        $this->assertSame(0, Loan::withoutGlobalScopes()->count());

        $this->actingAs($this->teller)->post(route('panel.gadai.cairkan', $a), ['metode' => 'transfer'])->assertSessionHasErrors('umum'); // referensi wajib
        $this->actingAs($this->teller)->post(route('panel.gadai.cairkan', $a), ['metode' => 'transfer', 'referensi' => 'TRX-1'])->assertSessionHasNoErrors();
        $this->actingAs($this->teller)->post(route('panel.gadai.cairkan', $a), ['metode' => 'tunai'])->assertSessionHasErrors('umum'); // sudah dicairkan
        $this->assertSame(1, Loan::withoutGlobalScopes()->count());

        $belum = $this->pengajuan($this->anggota());
        $this->actingAs($this->teller)->post(route('panel.gadai.cairkan', $belum), ['metode' => 'tunai'])->assertSessionHasErrors('umum');
    }

    // ---------- tagihan (hitungan manual) ----------

    public function test_tagihan_tebus_dini_per_blok_15_hari_tepat_waktu_dan_terlambat(): void
    {
        $l = $this->gadai();

        $this->hariKe(10);   // 10 hari -> dibulatkan 15 hari = 45.000
        $t = $this->svc()->tagihan($l);
        $this->assertSame(45_000, $t['tebus']['sewa_modal']);
        $this->assertSame(2_065_000, $t['tebus']['total']);

        $this->hariKe(20);   // 20 hari -> 30 hari = 90.000
        $this->assertSame(2_110_000, $this->svc()->tagihan($l)['tebus']['total']);

        $this->hariKe(30);   // tepat jatuh tempo
        $t = $this->svc()->tagihan($l);
        $this->assertSame(2_110_000, $t['tebus']['total']);
        $this->assertSame(0, $t['terlambat_hari']);
        $this->assertSame(110_000, $t['perpanjang']['total']);

        $this->hariKe(32);   // terlambat 2 hari: tebus 32 hari = 96.000 ; perpanjang 90.000 + 6.000 + 20.000
        $t = $this->svc()->tagihan($l);
        $this->assertSame(2, $t['terlambat_hari']);
        $this->assertSame(2_116_000, $t['tebus']['total']);
        $this->assertSame(116_000, $t['perpanjang']['total']);
    }

    public function test_perubahan_produk_setelah_akad_tidak_mengubah_tagihan(): void
    {
        $l = $this->gadai();
        $this->produk()->update(['sewa_modal_per_hari_bp' => 50, 'admin_bp' => 500]);

        $this->hariKe(30);
        $this->assertSame(110_000, $this->svc()->tagihan($l->refresh())['perpanjang']['total']);
    }

    // ---------- perpanjang / cicil / tebus ----------

    public function test_perpanjangan_tunai_memajukan_jatuh_tempo_dari_jatuh_tempo_lama(): void
    {
        $l = $this->gadai();
        $lama = $l->jatuh_tempo->copy();

        $this->hariKe(25); // bayar lebih awal: jatuh tempo baru tetap lama + 30, bukan tanggal bayar + 30
        $r = $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'tunai']);
        $r->assertSessionHasNoErrors();

        $l->refresh();
        $p = Payment::withoutGlobalScopes()->firstOrFail();
        $this->assertSame(110_000, (int) $p->total);
        $this->assertSame(90_000, (int) $p->sewa_modal);
        $this->assertSame(20_000, (int) $p->administrasi);
        $this->assertSame(0, (int) $p->pokok_dibayar);
        $this->assertSame('sah', $p->status);
        $this->assertSame($lama->copy()->addDays(30)->toDateString(), $l->jatuh_tempo->toDateString());
        $this->assertSame(1, $l->perpanjangan_ke);
        $this->assertSame(2_000_000, (int) $l->pokok_saldo, 'pokok tidak berubah saat perpanjang');
        $this->assertSame(1, LoanExtension::where('payment_id', $p->id)->count());
        $this->assertSame(L::Aktif, $l->status);

        $this->actingAs($this->teller)->get(route('panel.pembayaran.kwitansi', $p))->assertOk()->assertSee($p->no_kwitansi)->assertSee('Rp 110.000');
    }

    public function test_cicil_mengurangi_pokok_dan_menyetel_ulang_hitungan_perpanjangan(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->bayarTunai($l, 'perpanjangan'); // ke-1
        $this->hariKe(60);

        $p = $this->bayarTunai($l, 'cicil', 500_000);

        $l->refresh();
        $this->assertSame(610_000, (int) $p->total); // 500.000 + sewa 90.000 + admin 20.000
        $this->assertSame(500_000, (int) $p->pokok_dibayar);
        $this->assertSame(1_500_000, (int) $l->pokok_saldo);
        $this->assertSame(0, $l->perpanjangan_ke, 'cicil menyetel ulang hitungan perpanjangan');
        $this->assertSame(today()->addDays(30)->toDateString(), $l->jatuh_tempo->toDateString());
        // Administrasi periode berikutnya mengikuti saldo baru: 1% x 1.500.000 = 15.000
        $this->assertSame(15_000, $this->svc()->tagihan($l)['administrasi']);
    }

    public function test_cicil_di_bawah_minimum_atau_sama_dengan_saldo_ditolak(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);

        foreach ([40_000, 2_000_000, 2_500_000] as $pokok) {
            try {
                $this->bayarTunai($l, 'cicil', $pokok);
                $this->fail("cicil $pokok seharusnya ditolak");
            } catch (DomainException) {
                $this->assertTrue(true);
            }
        }
        try {
            $this->bayarTunai($l, 'cicil', null);
            $this->fail('cicil tanpa pokok seharusnya ditolak');
        } catch (DomainException) {
        }
        $this->assertSame(0, Payment::withoutGlobalScopes()->count());
        $this->assertSame(2_000_000, (int) $l->refresh()->pokok_saldo);
    }

    public function test_batas_tiga_perpanjangan_berturut_turut_lalu_wajib_cicil_atau_tebus(): void
    {
        $l = $this->gadai();
        foreach ([30, 60, 90] as $hari) {
            $this->hariKe($hari);
            $this->bayarTunai($l, 'perpanjangan');
        }
        $this->assertSame(3, $l->refresh()->perpanjangan_ke);

        $this->hariKe(120);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Batas perpanjangan');
        $this->bayarTunai($l, 'perpanjangan');
    }

    public function test_setelah_batas_perpanjangan_cicil_dan_tebus_tetap_bisa(): void
    {
        $l = $this->gadai();
        foreach ([30, 60, 90] as $hari) {
            $this->hariKe($hari);
            $this->bayarTunai($l, 'perpanjangan');
        }
        $this->hariKe(120);
        $this->bayarTunai($l, 'cicil', 100_000);
        $this->assertSame(0, $l->refresh()->perpanjangan_ke);
        $this->bayarTunai($l, 'perpanjangan'); // hitungan sudah disetel ulang
        $this->assertSame(1, $l->refresh()->perpanjangan_ke);
    }

    public function test_tebus_melunasi_lalu_barang_dikeluarkan_penggadai(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);

        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'tebus', 'metode' => 'tunai'])->assertSessionHasNoErrors();
        $l->refresh();
        $this->assertSame(L::Ditebus, $l->status);
        $this->assertSame(0, (int) $l->pokok_saldo);
        $this->assertSame(2_110_000, (int) Payment::withoutGlobalScopes()->firstOrFail()->total);
        $this->assertSame('tersimpan', Collateral::withoutGlobalScopes()->firstOrFail()->status, 'barang belum keluar sebelum diserahkan');

        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), ['penebus_tipe' => 'penggadai', 'nama_penebus' => $l->member->nama_lengkap])->assertSessionHasErrors('nota_asli_diperiksa');
        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), ['penebus_tipe' => 'penggadai', 'nama_penebus' => $l->member->nama_lengkap, 'nota_asli_diperiksa' => '1'])->assertSessionHasNoErrors();

        $this->assertSame('dikeluarkan', Collateral::withoutGlobalScopes()->firstOrFail()->status);
        $this->assertSame(1, Redemption::where('loan_id', $l->id)->count());
        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), ['penebus_tipe' => 'penggadai', 'nama_penebus' => 'X', 'nota_asli_diperiksa' => '1'])->assertSessionHasErrors('umum');
        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'tunai'])->assertSessionHasErrors('umum');
    }

    public function test_tebus_oleh_kuasa_wajib_surat_kuasa_dan_nik(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->bayarTunai($l, 'tebus');
        $dasar = ['penebus_tipe' => 'kuasa', 'nama_penebus' => 'Budi Kuasa', 'nota_asli_diperiksa' => '1'];

        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), $dasar)->assertSessionHasErrors('umum');
        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), $dasar + ['nik_penebus' => '7371010101900009'])->assertSessionHasErrors('umum'); // tanpa surat
        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), $dasar + ['nik_penebus' => '7371010101900009', 'surat_kuasa' => UploadedFile::fake()->create('k.exe', 10, 'application/x-msdownload')])->assertSessionHasErrors('surat_kuasa');
        $this->actingAs($this->teller)->post(route('panel.penebusan.store', $l), $dasar + ['nik_penebus' => '7371010101900009', 'surat_kuasa' => UploadedFile::fake()->create('kuasa.pdf', 100, 'application/pdf')])->assertSessionHasNoErrors();

        $r = Redemption::where('loan_id', $l->id)->firstOrFail();
        $this->assertSame('kuasa', $r->penebus_tipe);
        $this->assertNotNull($r->surat_kuasa_path);
        $this->assertNotSame('7371010101900009', \DB::table('redemptions')->value('nik_penebus_enc'), 'NIK penebus terenkripsi');
    }

    // ---------- transfer dan verifikasi ----------

    public function test_transfer_menunggu_verifikasi_petugas_lain_dan_tidak_mengubah_gadai_sebelumnya(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $lama = $l->jatuh_tempo->toDateString();

        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'transfer'])->assertSessionHasErrors('umum'); // bukti wajib
        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'transfer', 'bukti' => UploadedFile::fake()->image('b.jpg')])->assertSessionHasNoErrors();

        $p = Payment::withoutGlobalScopes()->firstOrFail();
        $this->assertSame('menunggu_verifikasi', $p->status);
        $this->assertSame($lama, $l->refresh()->jatuh_tempo->toDateString(), 'belum berlaku sebelum diverifikasi');
        \Storage::disk('local')->assertExists($p->bukti_path);

        // pembayaran kedua diblokir selama ada yang menunggu
        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'tebus', 'metode' => 'tunai'])->assertSessionHasErrors('umum');
        // pencatat tidak boleh memverifikasi sendiri
        $this->actingAs($this->teller)->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'setuju'])->assertSessionHasErrors('umum');
        // peran tanpa izin ditolak
        $this->actingAs($this->staf(Role::Analis, $this->a))->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'setuju'])->assertForbidden();

        $lain = $this->staf(Role::Teller, $this->a);
        $this->actingAs($lain)->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'setuju'])->assertSessionHasNoErrors();
        $this->assertSame('sah', $p->refresh()->status);
        $this->assertSame($lain->id, $p->diverifikasi_oleh);
        $this->assertSame(1, $l->refresh()->perpanjangan_ke);
    }

    public function test_manager_dapat_memverifikasi_dan_penolakan_wajib_alasan_serta_tidak_mengubah_gadai(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->svc()->bayar($l, $this->teller, 'tebus', 'transfer', null, UploadedFile::fake()->image('b.jpg'));
        $p = Payment::withoutGlobalScopes()->firstOrFail();
        $manager = $this->staf(Role::Manager, $this->a);

        $this->actingAs($manager)->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'tolak', 'alasan' => ''])->assertSessionHasErrors('umum');
        $this->actingAs($manager)->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'tolak', 'alasan' => 'Nominal tidak sesuai'])->assertSessionHasNoErrors();

        $this->assertSame('batal', $p->refresh()->status);
        $this->assertSame(L::Aktif, $l->refresh()->status);
        $this->assertSame(2_000_000, (int) $l->pokok_saldo);
        $this->actingAs($manager)->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'setuju'])->assertSessionHasErrors('umum'); // sudah diproses
    }

    public function test_verifikasi_ditolak_bila_keadaan_gadai_berubah(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->svc()->bayar($l, $this->teller, 'perpanjangan', 'transfer', null, UploadedFile::fake()->image('b.jpg'));
        $p = Payment::withoutGlobalScopes()->firstOrFail();
        Loan::withoutGlobalScopes()->whereKey($l->id)->update(['jatuh_tempo' => today()->addDays(5)]); // keadaan berubah

        $this->actingAs($this->staf(Role::Manager, $this->a))->post(route('panel.pembayaran.verifikasi', $p), ['keputusan' => 'setuju'])->assertSessionHasErrors('umum');
        $this->assertSame('menunggu_verifikasi', $p->refresh()->status);
    }

    // ---------- pembalikan ----------

    public function test_manager_membalikkan_perpanjangan_terakhir(): void
    {
        $l = $this->gadai();
        $lama = $l->jatuh_tempo->toDateString();
        $this->hariKe(30);
        $p = $this->bayarTunai($l, 'perpanjangan');
        $teller = $this->teller;

        $this->actingAs($teller)->post(route('panel.pembayaran.batalkan', $p), ['alasan' => 'Salah input'])->assertForbidden();
        $this->actingAs($this->staf(Role::Manager, $this->a))->post(route('panel.pembayaran.batalkan', $p), ['alasan' => ''])->assertSessionHasErrors('alasan');
        $this->actingAs($this->staf(Role::Manager, $this->a))->post(route('panel.pembayaran.batalkan', $p), ['alasan' => 'Salah input'])->assertSessionHasNoErrors();

        $l->refresh();
        $this->assertSame($lama, $l->jatuh_tempo->toDateString());
        $this->assertSame(0, $l->perpanjangan_ke);
        $this->assertSame('batal', $p->refresh()->status);
        $this->assertSame(0, LoanExtension::where('payment_id', $p->id)->count());
    }

    public function test_hanya_pembayaran_sah_terakhir_dan_sebelum_barang_keluar_yang_dapat_dibalikkan(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $p1 = $this->bayarTunai($l, 'perpanjangan');
        $this->hariKe(60);
        $p2 = $this->bayarTunai($l, 'tebus');
        $manager = $this->staf(Role::Manager, $this->a);

        $this->actingAs($manager)->post(route('panel.pembayaran.batalkan', $p1), ['alasan' => 'x'])->assertSessionHasErrors('umum'); // bukan yang terakhir

        $this->svc()->keluarkanBarang($l, $this->teller, ['penebus_tipe' => 'penggadai', 'nama_penebus' => 'A', 'nota_asli_diperiksa' => true]);
        $this->actingAs($manager)->post(route('panel.pembayaran.batalkan', $p2), ['alasan' => 'x'])->assertSessionHasErrors('umum'); // barang sudah keluar
        $this->assertSame(L::Ditebus, $l->refresh()->status);
    }

    public function test_pembalikan_tebus_sebelum_barang_keluar_mengembalikan_gadai_aktif(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $p = $this->bayarTunai($l, 'tebus');

        $this->actingAs($this->staf(Role::Manager, $this->a))->post(route('panel.pembayaran.batalkan', $p), ['alasan' => 'Uang tidak diterima'])->assertSessionHasNoErrors();

        $l->refresh();
        $this->assertSame(L::Aktif, $l->status);
        $this->assertSame(2_000_000, (int) $l->pokok_saldo);
    }

    // ---------- scheduler ----------

    public function test_scheduler_memindahkan_ke_tenggang_lalu_siap_lelang_dan_idempoten(): void
    {
        $l = $this->gadai();   // jatuh tempo hari ke-30, tenggang sampai hari ke-33

        $this->hariKe(30);
        Artisan::call('gadai:perbarui-status');
        $this->assertSame(L::Aktif, $l->refresh()->status, 'tepat jatuh tempo belum terlambat');

        $this->hariKe(31);
        Artisan::call('gadai:perbarui-status');
        $this->assertSame(L::Tenggang, $l->refresh()->status);

        $this->hariKe(33);
        Artisan::call('gadai:perbarui-status');
        $this->assertSame(L::Tenggang, $l->refresh()->status, 'masih dalam masa tenggang');

        $this->hariKe(34);
        Artisan::call('gadai:perbarui-status');
        Artisan::call('gadai:perbarui-status'); // idempoten
        $this->assertSame(L::SiapLelang, $l->refresh()->status);
        $this->assertSame(1, Auction::where('loan_id', $l->id)->count());
        $this->assertSame('dijadwalkan', Auction::where('loan_id', $l->id)->first()->status);
        $this->assertSame(['aktif>tenggang', 'tenggang>siap_lelang'], $l->histories()->whereNotNull('dari')->orderBy('id')->get()->map(fn ($h) => $h->dari.'>'.$h->ke)->all());
    }

    public function test_pembayaran_dalam_tenggang_atau_siap_lelang_menyelamatkan_barang(): void
    {
        $l = $this->gadai();
        $this->hariKe(34);
        Artisan::call('gadai:perbarui-status');
        $this->assertSame(L::SiapLelang, $l->refresh()->status);

        $p = $this->bayarTunai($l, 'perpanjangan'); // jatuh tempo baru hari ke-60 > hari ke-34

        $l->refresh();
        $this->assertSame(L::Aktif, $l->status);
        $this->assertSame('batal', Auction::where('loan_id', $l->id)->first()->status);
        // sewa: 90.000 + 4 hari terlambat x 3.000 + admin 20.000
        $this->assertSame(122_000, (int) $p->total);
    }

    public function test_terlalu_lama_terlambat_hanya_boleh_tebus(): void
    {
        $l = $this->gadai();
        $this->hariKe(65); // jatuh tempo + 30 = hari 60 sudah lewat
        Artisan::call('gadai:perbarui-status');

        try {
            $this->bayarTunai($l, 'perpanjangan');
            $this->fail('perpanjangan seharusnya ditolak');
        } catch (DomainException $e) {
            $this->assertStringContainsString('tebus', $e->getMessage());
        }
        $p = $this->bayarTunai($l, 'tebus');
        $this->assertSame(L::Ditebus, $l->refresh()->status);
        $this->assertSame(2_000_000 + 2_000_000 * 15 * 65 / 10000 + 20_000, (int) $p->total); // 65 hari x 3.000
    }

    // ---------- lelang ----------

    private function siapLelang(): Loan
    {
        $l = $this->gadai();
        $this->hariKe(34);
        Artisan::call('gadai:perbarui-status');

        return $l->refresh();
    }

    public function test_penjadwalan_lelang_menghormati_masa_pemberitahuan_dan_harga_limit(): void
    {
        $l = $this->siapLelang(); // diberitahukan hari ke-34 -> paling cepat hari ke-41
        $manager = $this->staf(Role::Manager, $this->a);
        $url = route('panel.lelang.jadwal', $l);

        $this->actingAs($this->teller)->post($url, ['tgl_lelang' => today()->addDays(8)->toDateString(), 'harga_limit' => '2.000.000'])->assertForbidden();
        $this->actingAs($manager)->post($url, ['tgl_lelang' => today()->addDays(6)->toDateString(), 'harga_limit' => '2.500.000'])->assertSessionHasErrors('umum');
        $this->actingAs($manager)->post($url, ['tgl_lelang' => today()->addDays(7)->toDateString(), 'harga_limit' => '1.000.000'])->assertSessionHasErrors('umum');
        $this->actingAs($manager)->post($url, ['tgl_lelang' => today()->addDays(7)->toDateString(), 'harga_limit' => '2.500.000'])->assertSessionHasNoErrors();

        $au = Auction::where('loan_id', $l->id)->firstOrFail();
        $this->assertSame(today()->addDays(7)->toDateString(), $au->tgl_lelang->toDateString());
        $this->assertSame($manager->id, $au->disetujui_oleh);
    }

    public function test_lelang_laku_dengan_kelebihan_menjadi_hak_anggota(): void
    {
        $l = $this->siapLelang();
        $manager = $this->staf(Role::Manager, $this->a);
        $this->svc()->jadwalkanLelang($l, $manager, today()->addDays(7)->toDateString(), 2_500_000);

        // belum waktunya
        $this->actingAs($this->teller)->post(route('panel.lelang.hasil', $l), ['hasil' => 'laku', 'harga_laku' => '3.000.000', 'biaya_lelang' => '100.000'])->assertSessionHasErrors('umum');

        $this->hariKe(41); // terlambat 11 hari -> tebus 41 hari: sewa 123.000 + admin 20.000 + pokok 2.000.000 = 2.143.000
        $this->actingAs($this->teller)->post(route('panel.lelang.hasil', $l), ['hasil' => 'laku', 'harga_laku' => '3.000.000', 'biaya_lelang' => '100.000'])->assertSessionHasNoErrors();

        $au = Auction::where('loan_id', $l->id)->firstOrFail();
        $this->assertSame('laku', $au->status);
        $this->assertSame(757_000, (int) $au->kelebihan); // 3.000.000 - 100.000 - 2.143.000
        $this->assertSame('belum', $au->kelebihan_status);
        $this->assertSame(L::Dilelang, $l->refresh()->status);
        $this->assertSame('dilelang', Collateral::withoutGlobalScopes()->firstOrFail()->status);

        $this->actingAs($this->staf(Role::Admin, $this->a))->post(route('panel.lelang.kelebihan', $l))->assertForbidden();
        $this->actingAs($this->teller)->post(route('panel.lelang.kelebihan', $l))->assertSessionHasNoErrors();
        $this->assertSame('dibayarkan', $au->refresh()->kelebihan_status);
        $this->assertSame(L::SelesaiLelang, $l->refresh()->status);
        $this->actingAs($this->teller)->post(route('panel.lelang.kelebihan', $l))->assertSessionHasErrors('umum'); // tidak bisa dua kali
    }

    public function test_lelang_kekurangan_tidak_ditagihkan_dan_gadai_langsung_selesai(): void
    {
        $l = $this->siapLelang();
        $this->svc()->jadwalkanLelang($l, $this->staf(Role::Manager, $this->a), today()->addDays(7)->toDateString(), 2_000_000);
        $this->hariKe(41);

        $this->svc()->catatHasilLelang($l, $this->teller, 'laku', 1_500_000, 50_000);

        $au = Auction::where('loan_id', $l->id)->firstOrFail();
        $this->assertLessThan(0, (int) $au->kelebihan);
        $this->assertSame('tidak_ada', $au->kelebihan_status);
        $this->assertSame(L::SelesaiLelang, $l->refresh()->status);
    }

    public function test_lelang_tidak_laku_kembali_ke_antrean_dan_penebusan_masih_mungkin(): void
    {
        $l = $this->siapLelang();
        $this->svc()->jadwalkanLelang($l, $this->staf(Role::Manager, $this->a), today()->addDays(7)->toDateString(), 2_000_000);
        $this->hariKe(41);

        $this->svc()->catatHasilLelang($l, $this->teller, 'tidak_laku', null, 0);
        $au = Auction::where('loan_id', $l->id)->firstOrFail();
        $this->assertSame('dijadwalkan', $au->status);
        $this->assertNull($au->tgl_lelang);
        $this->assertSame(L::SiapLelang, $l->refresh()->status);

        $this->bayarTunai($l, 'tebus');
        $this->assertSame(L::Ditebus, $l->refresh()->status);
        $this->assertSame('batal', $au->refresh()->status);
    }

    public function test_gadai_yang_sudah_dilelang_tidak_bisa_dibayar_atau_dibalikkan(): void
    {
        $l = $this->siapLelang();
        $this->svc()->jadwalkanLelang($l, $this->staf(Role::Manager, $this->a), today()->addDays(7)->toDateString(), 2_000_000);
        $this->hariKe(41);
        $this->svc()->catatHasilLelang($l, $this->teller, 'laku', 3_000_000, 0);

        $this->actingAs($this->teller)->post(route('panel.pembayaran.store', $l), ['tipe' => 'tebus', 'metode' => 'tunai'])->assertSessionHasErrors('umum');
        $this->assertSame(0, Payment::withoutGlobalScopes()->count());
    }

    public function test_halaman_lelang_hanya_manager_owner_dan_teller(): void
    {
        $this->actingAs($this->staf(Role::Manager, $this->a))->get(route('panel.lelang.index'))->assertOk();
        $this->actingAs($this->staf(Role::Owner))->get(route('panel.lelang.index'))->assertOk();
        $this->actingAs($this->teller)->get(route('panel.lelang.index'))->assertOk();
        foreach ([Role::Analis, Role::Admin] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get(route('panel.lelang.index'))->assertForbidden();
        }
        $this->actingAs($this->anggota()->user)->get(route('panel.lelang.index'))->assertForbidden();
    }

    // ---------- hak akses dan isolasi ----------

    public function test_hanya_teller_mencatat_pembayaran(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        foreach ([Role::Manager, Role::Analis, Role::Admin, Role::Owner] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'tunai'])->assertForbidden();
        }
        $this->actingAs($l->member->user)->post(route('panel.pembayaran.store', $l), ['tipe' => 'perpanjangan', 'metode' => 'tunai'])->assertForbidden();
        $this->assertSame(0, Payment::withoutGlobalScopes()->count());
    }

    public function test_isolasi_cabang_dan_anggota_pada_gadai(): void
    {
        $l = $this->gadai();
        $tellerB = $this->staf(Role::Teller, $this->b);
        $lain = $this->anggota($this->a);

        $this->actingAs($tellerB)->get(route('panel.gadai.show', $l))->assertNotFound();
        $this->actingAs($tellerB)->post(route('panel.pembayaran.store', $l), ['tipe' => 'tebus', 'metode' => 'tunai'])->assertNotFound();
        $this->actingAs($tellerB)->get(route('panel.gadai.index'))->assertDontSee($l->no_sbg);
        $this->actingAs($lain->user)->get(route('panel.gadai.show', $l))->assertNotFound();
        $this->actingAs($lain->user)->get(route('panel.gadai.nota', $l))->assertNotFound();
        $this->actingAs($this->staf(Role::Owner))->get(route('panel.gadai.show', $l))->assertOk();
    }

    public function test_anggota_melihat_gadai_sendiri_tanpa_formulir_staf(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->bayarTunai($l, 'perpanjangan');

        $this->actingAs($l->member->user)->get(route('panel.gadai.show', $l))->assertOk()
            ->assertSee($l->no_sbg)->assertSee('Tagihan hari ini')->assertSee('Riwayat pembayaran')
            ->assertDontSee('Catat pembayaran')->assertDontSee('Riwayat status')->assertDontSee('Balikkan');
        $this->actingAs($l->member->user)->get(route('panel.gadai.nota', $l))->assertOk()->assertSee($l->no_sbg);
        $this->actingAs($l->member->user)->get(route('panel.gadai.index'))->assertOk()->assertSee($l->no_sbg);
    }

    public function test_kwitansi_dan_bukti_hanya_untuk_staf(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $p = $this->bayarTunai($l, 'perpanjangan');
        $anggota = $l->member->user; // diambil sebelum ganti pengguna (relasi tunduk pada global scope)

        $this->actingAs($this->teller)->get(route('panel.pembayaran.kwitansi', $p))->assertOk();
        $this->actingAs($this->staf(Role::Teller, $this->b))->get(route('panel.pembayaran.kwitansi', $p))->assertNotFound();
        $this->actingAs($anggota)->get(route('panel.pembayaran.kwitansi', $p))->assertNotFound();
    }

    // ---------- rekonsiliasi dan audit ----------

    public function test_total_pokok_dibayar_ditambah_saldo_selalu_sama_dengan_pokok_awal(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $this->bayarTunai($l, 'cicil', 300_000);
        $this->hariKe(60);
        $this->bayarTunai($l, 'perpanjangan');
        $this->hariKe(90);
        $this->bayarTunai($l, 'cicil', 200_000);
        $l->refresh();
        $dibayar = (int) Payment::withoutGlobalScopes()->where('loan_id', $l->id)->where('status', 'sah')->sum('pokok_dibayar');
        $this->assertSame((int) $l->pokok_awal, $dibayar + (int) $l->pokok_saldo);
        $this->assertSame(1_500_000, (int) $l->pokok_saldo);

        $this->hariKe(120);
        $this->bayarTunai($l, 'tebus');
        $dibayar = (int) Payment::withoutGlobalScopes()->where('loan_id', $l->id)->where('status', 'sah')->sum('pokok_dibayar');
        $this->assertSame((int) $l->pokok_awal, $dibayar);
        $this->assertSame(0, (int) $l->refresh()->pokok_saldo);
        // total kwitansi = total komponen
        foreach (Payment::withoutGlobalScopes()->get() as $p) {
            $this->assertSame((int) $p->total, (int) ($p->pokok_dibayar + $p->sewa_modal + $p->administrasi + $p->denda));
        }
    }

    public function test_pembayaran_dan_status_tercatat_di_audit(): void
    {
        $l = $this->gadai();
        $this->hariKe(30);
        $p = $this->bayarTunai($l, 'perpanjangan');

        $this->assertTrue(AuditLog::where('aksi', 'payment.dibuat')->where('subject_id', $p->id)->exists());
        $this->assertTrue(AuditLog::where('aksi', 'loan.diubah')->where('subject_id', $l->id)->exists());
        $log = AuditLog::where('aksi', 'loan.diubah')->where('subject_id', $l->id)->latest('id')->first();
        $this->assertSame($l->jatuh_tempo->toDateString(), substr($log->sebelum['jatuh_tempo'], 0, 10), 'audit menyimpan jatuh tempo lama');
    }

    public function test_dashboard_dan_daftar_gadai_menampilkan_yang_segera_jatuh_tempo(): void
    {
        $l = $this->gadai();
        $this->hariKe(25);

        $this->actingAs($this->teller)->get(route('panel'))->assertOk()->assertSee('Gadai jatuh tempo ≤ 7 hari');
        $this->actingAs($this->teller)->get(route('panel.gadai.index', ['segera' => 1]))->assertSee($l->no_sbg);
        $this->hariKe(5);
        $this->actingAs($this->teller)->get(route('panel.gadai.index', ['segera' => 1]))->assertDontSee($l->no_sbg);
    }
}
