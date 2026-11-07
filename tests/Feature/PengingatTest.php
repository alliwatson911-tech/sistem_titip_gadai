<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Loan;
use App\Models\LoanNotice;
use App\Notifications\PengingatGadai;
use App\Services\LoanService;
use App\Services\PengingatService;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\PanelTestCase;

/** Gadai baru: jatuh tempo hari ke-30, tenggang sampai hari ke-33, siap lelang hari ke-34. */
class PengingatTest extends PanelTestCase
{
    private Carbon $awal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->awal = now()->startOfDay();
    }

    private function hariKe(int $n): void
    {
        $this->travelTo($this->awal->copy()->addDays($n)->addHours(8));
    }

    private function jalan(): array
    {
        return app(PengingatService::class)->jalankan();
    }

    private function perbaruiStatus(): void
    {
        Artisan::call('gadai:perbarui-status');
    }

    private function jenisTercatat(Loan $l): array
    {
        return LoanNotice::where('loan_id', $l->id)->orderBy('id')->pluck('jenis')->all();
    }

    public function test_pengingat_h7_terkirim_sekali_ke_email_anggota_saja(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $anggota = $l->member->user;

        $this->hariKe(22);
        $this->assertSame(0, $this->jalan()['terkirim'], '8 hari lagi: belum waktunya');

        $this->hariKe(23);
        $this->assertSame(1, $this->jalan()['terkirim']);
        $this->assertSame(0, $this->jalan()['terkirim'], 'jalan ulang di hari yang sama tidak mengirim ganda');
        $this->hariKe(24);
        $this->assertSame(0, $this->jalan()['terkirim'], 'h7 hanya sekali per jatuh tempo');

        Notification::assertSentToTimes($anggota, PengingatGadai::class, 1);
        Notification::assertSentTo($anggota, PengingatGadai::class, fn ($n) => $n->jenis === 'h7' && $n->d['no_sbg'] === $l->no_sbg && $n->d['sisa'] === 7);
        Notification::assertCount(1); // tidak ada staf yang ikut dikirimi
    }

    public function test_h1_menyusul_dan_tidak_berulang_di_hari_jatuh_tempo(): void
    {
        Notification::fake();
        $l = $this->buatGadai();

        $this->hariKe(23);
        $this->jalan();
        $this->hariKe(29);
        $this->assertSame(1, $this->jalan()['terkirim']);
        $this->hariKe(30);
        $this->assertSame(0, $this->jalan()['terkirim'], 'h1 mencakup hari jatuh tempo; tidak dikirim lagi');

        $this->assertSame(['h7', 'h1'], $this->jenisTercatat($l));
    }

    public function test_scheduler_terlewat_tetap_mengirim_pengingat_yang_tertinggal(): void
    {
        Notification::fake();
        $l = $this->buatGadai();

        $this->hariKe(26); // tidak ada yang berjalan pada hari 23-25
        $this->assertSame(1, $this->jalan()['terkirim']);
        Notification::assertSentTo($l->member->user, PengingatGadai::class, fn ($n) => $n->jenis === 'h7' && $n->d['sisa'] === 4);
    }

    public function test_tenggang_dan_siap_lelang_dikirim_dengan_isi_yang_benar(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $anggota = $l->member->user;

        $this->hariKe(31);
        $this->perbaruiStatus();
        $this->assertSame(1, $this->jalan()['terkirim']);
        Notification::assertSentTo($anggota, PengingatGadai::class, fn ($n) => $n->jenis === 'tenggang' && $n->d['tenggang_sampai'] === $l->tenggang_sampai->translatedFormat('j F Y'));

        $this->hariKe(34);
        $this->perbaruiStatus();
        $this->assertSame(1, $this->jalan()['terkirim']);
        $this->assertSame(0, $this->jalan()['terkirim'], 'siap_lelang hanya sekali');

        $kirim = Notification::sent($anggota, PengingatGadai::class)->firstWhere('jenis', 'siap_lelang');
        $this->assertNotNull($kirim);
        $this->assertSame($this->awal->copy()->addDays(41)->translatedFormat('j F Y'), $kirim->d['paling_cepat'], 'lelang paling cepat = pemberitahuan + 7 hari');
        $isi = implode(' ', array_map('strval', $kirim->toMail($anggota)->introLines));
        $this->assertStringContainsString('MASIH DAPAT menebus', $isi);
        $this->assertStringContainsString('kelebihan itu menjadi hak Anda', $isi);
    }

    public function test_jadwal_lelang_diberitahukan(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $this->hariKe(34);
        $this->perbaruiStatus();
        app(LoanService::class)->jadwalkanLelang($l, $this->staf(Role::Manager, $this->a), $this->awal->copy()->addDays(41)->toDateString(), 2_000_000);

        $this->assertSame(2, $this->jalan()['terkirim']); // siap_lelang + jadwal_lelang
        Notification::assertSentTo($l->member->user, PengingatGadai::class, fn ($n) => $n->jenis === 'jadwal_lelang' && $n->d['tgl_lelang'] === $this->awal->copy()->addDays(41)->translatedFormat('j F Y'));
    }

    public function test_perpanjangan_menghasilkan_siklus_pengingat_baru(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $teller = $this->staf(Role::Teller, $this->a);

        $this->hariKe(23);
        $this->jalan();                                              // h7 untuk jatuh tempo hari-30
        app(LoanService::class)->bayar($l, $teller, 'perpanjangan', 'tunai'); // jatuh tempo baru hari-60
        $this->hariKe(24);
        $this->assertSame(0, $this->jalan()['terkirim']);

        $this->hariKe(53);                                           // 7 hari sebelum hari-60
        $this->assertSame(1, $this->jalan()['terkirim']);
        $this->assertSame(['h7', 'h7'], $this->jenisTercatat($l));
        $this->assertSame(2, LoanNotice::where('loan_id', $l->id)->distinct('kunci')->count('kunci'));
    }

    public function test_gadai_selesai_tidak_dikirimi_apa_pun(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $this->hariKe(25);
        app(LoanService::class)->bayar($l, $this->staf(Role::Teller, $this->a), 'tebus', 'tunai');

        $this->hariKe(28);
        $this->assertSame(0, $this->jalan()['terkirim']);
        Notification::assertNothingSent();
    }

    public function test_akun_anggota_nonaktif_dilewati_dan_tidak_dikirimi(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $l->member->user->forceFill(['status' => 'nonaktif'])->save();

        $this->hariKe(26);
        $h = $this->jalan();

        $this->assertSame(['terkirim' => 0, 'gagal' => 0, 'dilewati' => 1], $h);
        Notification::assertNothingSent();
        $this->assertSame('dilewati', LoanNotice::where('loan_id', $l->id)->first()->status);
        $this->assertSame(0, $this->jalan()['dilewati'], 'tidak dicoba terus-menerus');
    }

    public function test_kegagalan_kirim_dicatat_dicoba_lagi_dan_dibatasi_tiga_kali(): void
    {
        $l = $this->buatGadai();
        $this->hariKe(26);

        Event::listen(NotificationSending::class, fn () => throw new \RuntimeException('SMTP tidak tersedia'));
        $this->assertSame(1, $this->jalan()['gagal']);
        $n = LoanNotice::where('loan_id', $l->id)->first();
        $this->assertSame('gagal', $n->status);
        $this->assertSame(1, $n->percobaan);
        $this->assertStringContainsString('SMTP tidak tersedia', $n->catatan);

        $this->jalan();
        $this->jalan();
        $this->assertSame(3, $n->refresh()->percobaan);
        $this->assertSame(0, $this->jalan()['gagal'], 'setelah 3 percobaan berhenti');
        $this->assertSame(3, $n->refresh()->percobaan);
    }

    public function test_kegagalan_pulih_pada_percobaan_berikutnya(): void
    {
        $l = $this->buatGadai();
        $this->hariKe(26);

        Event::listen(NotificationSending::class, fn () => throw new \RuntimeException('SMTP tidak tersedia'));
        $this->jalan();
        Event::forget(NotificationSending::class);

        $this->assertSame(1, $this->jalan()['terkirim']);
        $n = LoanNotice::where('loan_id', $l->id)->first();
        $this->assertSame('terkirim', $n->status);
        $this->assertSame(2, $n->percobaan);
        $this->assertNull($n->catatan);
        $this->assertNotNull($n->sent_at);
    }

    public function test_isi_email_berisi_tagihan_dan_tidak_membocorkan_data_pribadi(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $nik = $l->member->nik_enc;
        $this->hariKe(29);
        $this->jalan();

        $n = Notification::sent($l->member->user, PengingatGadai::class)->first();
        $mail = $n->toMail($l->member->user);
        $teks = $mail->subject.' '.implode(' ', array_map('strval', $mail->introLines)).' '.implode(' ', array_map('strval', $mail->outroLines));

        $this->assertStringContainsString('jatuh tempo besok', $mail->subject);
        $this->assertStringContainsString('Rp 2.000.000', $teks);   // saldo pokok
        $this->assertStringContainsString('Rp 110.000', $teks);     // perpanjang: 90.000 + 20.000
        $this->assertStringNotContainsString($nik, $teks);
        $this->assertStringContainsString('tidak pernah meminta kata sandi', $teks);
        $this->assertStringContainsString('/panel/gadai/'.$l->id, $mail->actionUrl);
    }

    public function test_perintah_dan_jadwal_terdaftar(): void
    {
        $this->assertSame(0, Artisan::call('gadai:kirim-pengingat'));
        $this->assertStringContainsString('email terkirim', Artisan::output());
        Artisan::call('schedule:list');
        $this->assertStringContainsString('gadai:kirim-pengingat', Artisan::output());
    }

    public function test_staf_melihat_bukti_pemberitahuan_anggota_tidak(): void
    {
        Notification::fake();
        $l = $this->buatGadai();
        $this->hariKe(26);
        $this->jalan();
        $anggota = $l->member->user;

        $this->actingAs($this->staf(Role::Manager, $this->a))->get(route('panel.gadai.show', $l))->assertOk()
            ->assertSee('Pemberitahuan ke anggota')->assertSee('Pengingat jatuh tempo (≤ 7 hari)')->assertSee('terkirim');
        $this->actingAs($anggota)->get(route('panel.gadai.show', $l))->assertOk()->assertDontSee('Pemberitahuan ke anggota');
    }
}
