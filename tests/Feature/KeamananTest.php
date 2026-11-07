<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Faq;
use App\Models\Payment;
use App\Services\LoanService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\PanelTestCase;

class KeamananTest extends PanelTestCase
{
    // ---------- header ----------

    public function test_header_keamanan_dan_csp_ketat_di_halaman_publik_dan_panel(): void
    {
        $paneli = $this->actingAs($this->staf(Role::Owner))->get(route('panel'));
        $publik = $this->get('/');

        foreach ([$publik, $paneli] as $r) {
            $csp = $r->headers->get('Content-Security-Policy');
            $this->assertNotNull($csp);
            $this->assertStringContainsString("script-src 'self';", $csp, 'skrip hanya dari asal sendiri, tanpa unsafe-inline');
            $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);
            $this->assertStringNotContainsString('unsafe-eval', $csp);
            $this->assertStringContainsString("object-src 'none'", $csp);
            $this->assertStringContainsString("base-uri 'self'", $csp);
            $this->assertStringContainsString("form-action 'self'", $csp);
            $this->assertStringContainsString("frame-ancestors 'self'", $csp);
            $r->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        }
        $paneli->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_hsts_hanya_pada_https(): void
    {
        $this->get('/')->assertHeaderMissing('Strict-Transport-Security');
        $this->withServerVariables(['HTTPS' => 'on'])->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_cookie_sesi_httponly_dan_samesite(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertContains(config('session.same_site'), ['lax', 'strict']);
    }

    public function test_tidak_ada_penangan_atau_skrip_inline_agar_csp_efektif(): void
    {
        $owner = $this->staf(Role::Owner);
        $loan = $this->buatGadai();
        $this->svc()->bayar($loan, $this->staf(Role::Teller, $this->a), 'perpanjangan', 'tunai');
        $bayar = Payment::withoutGlobalScopes()->firstOrFail();
        Announcement::create(['judul' => 'x', 'isi' => 'y', 'is_published' => true]);

        $periksa = function (string $url, $user = null) {
            $r = $user ? $this->actingAs($user)->get($url) : $this->get($url);
            $html = $r->assertOk()->getContent();
            $this->assertSame(0, preg_match('/\son(click|submit|change|load|error|input|focus|blur)\s*=/i', $html), "penangan inline di $url");
            $this->assertSame(0, preg_match('/<script\b(?![^>]*\bsrc=)(?![^>]*type="application\/(ld\+)?json")[^>]*>/i', $html), "skrip inline di $url");
            $this->assertStringNotContainsString('javascript:', $html, "tautan javascript: di $url");
        };

        // Tamu (halaman publik dan alur sandi)
        $periksa('/');
        $periksa('/lupa-sandi');
        // Owner
        foreach ([route('panel'), route('panel.pengguna.index'), route('panel.gadai.show', $loan), route('panel.gadai.nota', $loan), route('panel.pengajuan.show', $loan->application_id),
            route('panel.anggota.show', $loan->member_id), route('panel.laporan.cabang'), route('panel.audit.index'), route('panel.pengumuman.index'), route('panel.faq.index')] as $url) {
            $periksa($url, $owner);
        }
        // Teller (kwitansi hanya untuk teller)
        $periksa(route('panel.pembayaran.kwitansi', $bayar), $this->staf(Role::Teller, $this->a));
    }

    private function svc(): LoanService
    {
        return app(LoanService::class);
    }

    // ---------- database ----------

    public function test_audit_log_tidak_bisa_diubah_atau_dihapus_bahkan_lewat_sql_langsung(): void
    {
        $log = AuditLog::create(['aksi' => 'uji.tetap', 'branch_id' => null]);

        foreach ([
            fn () => DB::table('audit_logs')->where('id', $log->id)->update(['aksi' => 'dimanipulasi']),
            fn () => DB::table('audit_logs')->where('id', $log->id)->delete(),
            fn () => DB::statement('UPDATE audit_logs SET aksi = ? WHERE id = ?', ['x', $log->id]),
            fn () => DB::statement('DELETE FROM audit_logs'),
        ] as $serangan) {
            try {
                $serangan();
                $this->fail('perubahan audit lewat SQL seharusnya ditolak trigger');
            } catch (QueryException $e) {
                $this->assertStringContainsString('audit_logs bersifat tambah-saja', $e->getMessage());
            }
        }
        $this->assertSame('uji.tetap', DB::table('audit_logs')->where('id', $log->id)->value('aksi'));
        $this->assertGreaterThan(0, AuditLog::create(['aksi' => 'uji.tambah'])->id, 'menambah tetap boleh');
    }

    public function test_total_pembayaran_harus_sama_dengan_komponen_di_level_database(): void
    {
        $loan = $this->buatGadai();
        $this->svc()->bayar($loan, $this->staf(Role::Teller, $this->a), 'perpanjangan', 'tunai');
        $p = Payment::withoutGlobalScopes()->firstOrFail();

        $this->expectException(QueryException::class);
        DB::table('payments')->where('id', $p->id)->update(['total' => 1]);
    }

    public function test_saldo_pokok_tidak_boleh_melebihi_pokok_awal(): void
    {
        $loan = $this->buatGadai();

        $this->expectException(QueryException::class);
        DB::table('loans')->where('id', $loan->id)->update(['pokok_saldo' => $loan->pokok_awal + 1]);
    }

    // ---------- perintah operasional ----------

    public function test_pemeriksa_sistem_lolos_di_pengembangan_dan_gagal_di_produksi_yang_salah_konfigurasi(): void
    {
        $this->assertSame(0, Artisan::call('sistem:periksa'));
        $this->assertStringContainsString('Trigger audit', Artisan::output());

        $this->app->detectEnvironment(fn () => 'production');
        config(['app.debug' => true, 'mail.default' => 'log', 'app.url' => 'http://localhost']);
        $this->assertSame(1, Artisan::call('sistem:periksa'));
        $o = Artisan::output();
        $this->assertStringContainsString('APP_DEBUG', $o);
        $this->assertStringContainsString('GAGAL', $o);
        $this->assertStringContainsString('MAIL_MAILER', $o);
    }

    public function test_tabel_izin_dibuat_dari_konfigurasi(): void
    {
        $this->assertSame(0, Artisan::call('izin:tabel'));
        $o = Artisan::output();

        foreach (array_keys(config('permissions')) as $izin) {
            $this->assertStringContainsString("`$izin`", $o);
        }
        foreach (Role::cases() as $r) {
            $this->assertStringContainsString($r->label(), $o);
        }
    }

    public function test_perintah_terjadwal_terdaftar(): void
    {
        Artisan::call('schedule:list');
        $o = Artisan::output();

        $this->assertStringContainsString('pengajuan:kedaluwarsa', $o);
        $this->assertStringContainsString('gadai:perbarui-status', $o);
    }

    // ---------- sapuan rute ----------

    /** Semua halaman GET panel tanpa parameter: tidak ada galat server untuk peran apa pun, dan tamu selalu dialihkan ke masuk. */
    public function test_sapuan_rute_panel_tanpa_parameter(): void
    {
        $this->buatGadai();
        $uris = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => in_array('GET', $r->methods(), true) && str_starts_with($r->uri(), 'panel') && ! str_contains($r->uri(), '{'))
            ->map(fn ($r) => '/'.$r->uri())->unique()->values();

        $this->assertGreaterThan(15, $uris->count(), 'sapuan harus mencakup banyak rute');

        foreach (Role::cases() as $peran) {
            $u = $this->staf($peran, $this->a);
            if ($peran === Role::Member) {
                $u = $this->anggota($this->a)->user;
            }
            foreach ($uris as $uri) {
                $kode = $this->actingAs($u)->get($uri)->getStatusCode();
                $this->assertLessThan(500, $kode, "{$peran->value} {$uri} -> $kode");
                $this->assertContains($kode, [200, 302, 403], "{$peran->value} {$uri} -> $kode");
            }
        }

        auth()->logout();
        foreach ($uris as $uri) {
            $this->get($uri)->assertRedirect('/masuk');
        }
    }

    public function test_sapuan_rute_berparameter_untuk_owner(): void
    {
        $owner = $this->staf(Role::Owner);
        $loan = $this->buatGadai();
        $this->svc()->bayar($loan, $this->staf(Role::Teller, $this->a), 'perpanjangan', 'tunai');
        $bayar = Payment::withoutGlobalScopes()->firstOrFail();
        $pengumuman = Announcement::create(['judul' => 'x', 'isi' => 'y', 'is_published' => true]);
        $faq = Faq::create(['kelompok' => 'faq', 'pertanyaan' => 'q', 'jawaban' => 'a']);
        $log = AuditLog::create(['aksi' => 'uji.sapu']);

        $this->actingAs($owner);
        foreach ([
            route('panel.pengajuan.show', $loan->application_id), route('panel.gadai.show', $loan), route('panel.gadai.nota', $loan),
            route('panel.anggota.show', $loan->member_id), route('panel.audit.show', $log), route('panel.kontak.edit', $this->a), route('panel.pengguna.edit', $owner),
            route('panel.pengumuman.edit', $pengumuman), route('panel.faq.edit', $faq),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_metode_tidak_diizinkan_dan_rute_tak_dikenal_tidak_membocorkan_apa_pun(): void
    {
        $this->actingAs($this->staf(Role::Owner));

        foreach (['/panel/tidak-ada', '/.env', '/storage/ktp/1/x.jpg', '/vendor/autoload.php', '/app/Models/User.php'] as $url) {
            $kode = $this->get($url)->getStatusCode();
            $this->assertSame(404, $kode, "$url -> $kode");
        }
        $this->post(route('panel.laporan.cabang'))->assertStatus(405);
    }
}
