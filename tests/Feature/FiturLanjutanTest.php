<?php

namespace Tests\Feature;

use App\Console\Commands\CadangkanSistem;
use App\Enums\Role;
use App\Models\Application;
use App\Models\User;
use App\Notifications\VerifikasiEmail;
use App\Services\LoanService;
use App\Support\Totp;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Tests\PanelTestCase;

/** Taksir ulang, TOTP/2FA, verifikasi email, surat kuasa, cadangan, dan pemeriksa sistem. */
class FiturLanjutanTest extends PanelTestCase
{
    private const SANDI = 'Sandi-Uji-2FA-9731';

    // ------------------------------------------------------------ TOTP (vektor uji RFC 6238, SHA-1)

    public function test_totp_cocok_dengan_vektor_rfc_6238(): void
    {
        $rahasia = Totp::kodeBase32('12345678901234567890');
        foreach ([59 => '287082', 1111111109 => '081804', 1234567890 => '005924'] as $waktu => $kode) {
            $this->assertSame($kode, Totp::kode($rahasia, Totp::langkah($waktu)), "waktu $waktu");
        }
    }

    public function test_totp_menolak_kode_salah_dan_pemakaian_ulang(): void
    {
        $rahasia = Totp::buatRahasia();
        $waktu = 1_700_000_000;
        $kode = Totp::kode($rahasia, Totp::langkah($waktu));

        $langkah = Totp::verifikasi($rahasia, $kode, null, $waktu);
        $this->assertSame(Totp::langkah($waktu), $langkah);
        $this->assertNull(Totp::verifikasi($rahasia, $kode, $langkah, $waktu), 'kode yang sama tidak boleh dipakai dua kali');
        $this->assertNull(Totp::verifikasi($rahasia, '000000', null, $waktu));
        $this->assertNull(Totp::verifikasi($rahasia, 'abcdef', null, $waktu));
        $this->assertNull(Totp::verifikasi($rahasia, $kode, null, $waktu + 300), 'kode kedaluwarsa (di luar toleransi)');
        $this->assertNotNull(Totp::verifikasi($rahasia, $kode, null, $waktu + 29), 'toleransi selisih jam');
    }

    // ------------------------------------------------------------ alur 2FA

    private function stafDua(Role $role = Role::Teller): array
    {
        $u = User::factory()->peran($role, $role->isBranchBound() ? $this->a->id : null)->create(['password' => self::SANDI]);
        $rahasia = Totp::buatRahasia();
        $kode = ['abcd-1234', 'wxyz-5678'];
        $u->forceFill([
            'two_factor_secret' => $rahasia, 'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => array_map(fn ($k) => User::hashKodePemulihan($k), $kode),
        ])->saveQuietly();

        return [$u->refresh(), $rahasia, $kode];
    }

    private function kodeSekarang(string $rahasia): string
    {
        return Totp::kode($rahasia, Totp::langkah());
    }

    public function test_aktivasi_2fa_lewat_halaman_akun(): void
    {
        $u = $this->staf(Role::Teller);
        $this->actingAs($u)->get(route('panel.akun.2fa'))->assertOk();
        $this->actingAs($u)->post(route('panel.akun.2fa.mulai'))->assertRedirect(route('panel.akun.2fa'));
        $u->refresh();
        $this->assertNotNull($u->two_factor_secret);
        $this->assertFalse($u->duaFaktorAktif(), 'belum aktif sebelum kode dikonfirmasi');

        $this->actingAs($u)->post(route('panel.akun.2fa.konfirmasi'), ['kode' => '000000'])->assertSessionHasErrors('kode');
        $this->assertFalse($u->refresh()->duaFaktorAktif());

        $this->actingAs($u)->post(route('panel.akun.2fa.konfirmasi'), ['kode' => $this->kodeSekarang($u->two_factor_secret)])
            ->assertSessionHas('kode_pemulihan');
        $u->refresh();
        $this->assertTrue($u->duaFaktorAktif());
        $this->assertCount(8, $u->two_factor_recovery_codes);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'auth.2fa_aktif']);
    }

    public function test_anggota_tidak_boleh_memakai_halaman_2fa(): void
    {
        $m = $this->anggota();
        $this->actingAs($m->user)->get(route('panel.akun.2fa'))->assertForbidden();
        $this->actingAs($m->user)->post(route('panel.akun.2fa.mulai'))->assertForbidden();
    }

    public function test_masuk_dengan_2fa_meminta_kode_lalu_berhasil(): void
    {
        [$u, $rahasia] = $this->stafDua();

        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI])->assertRedirect(route('dua-faktor.tantangan'));
        $this->assertGuest();
        $this->get(route('dua-faktor.tantangan'))->assertOk();

        $this->post(route('dua-faktor.periksa'), ['kode' => $this->kodeSekarang($rahasia)])->assertRedirect(route('panel'));
        $this->assertAuthenticatedAs($u);
    }

    public function test_kode_2fa_salah_ditolak_dan_tidak_login(): void
    {
        [$u] = $this->stafDua();
        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);

        $this->from(route('dua-faktor.tantangan'))->post(route('dua-faktor.periksa'), ['kode' => '123456'])->assertSessionHasErrors('kode');
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'auth.2fa_gagal']);
    }

    public function test_kode_2fa_tidak_dapat_dipakai_dua_kali(): void
    {
        [$u, $rahasia] = $this->stafDua();
        $kode = $this->kodeSekarang($rahasia);

        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);
        $this->post(route('dua-faktor.periksa'), ['kode' => $kode])->assertRedirect(route('panel'));
        $this->post('/keluar');
        $this->flushSession();

        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI])->assertRedirect(route('dua-faktor.tantangan'));
        $this->from(route('dua-faktor.tantangan'))->post(route('dua-faktor.periksa'), ['kode' => $kode])->assertSessionHasErrors('kode');
        $this->assertGuest();
    }

    public function test_kode_pemulihan_berlaku_sekali(): void
    {
        [$u, , $pemulihan] = $this->stafDua();

        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);
        $this->post(route('dua-faktor.periksa'), ['kode' => $pemulihan[0]])->assertRedirect(route('panel'));
        $this->assertCount(1, $u->refresh()->two_factor_recovery_codes);
        $this->post('/keluar');
        $this->flushSession();

        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);
        $this->from(route('dua-faktor.tantangan'))->post(route('dua-faktor.periksa'), ['kode' => $pemulihan[0]])->assertSessionHasErrors('kode');
        $this->assertGuest();
    }

    public function test_tantangan_2fa_tanpa_sesi_atau_kedaluwarsa_kembali_ke_masuk(): void
    {
        $this->get(route('dua-faktor.tantangan'))->assertRedirect(route('masuk'));

        [$u, $rahasia] = $this->stafDua();
        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);
        $this->travel(11)->minutes();
        $this->post(route('dua-faktor.periksa'), ['kode' => $this->kodeSekarang($rahasia)])->assertRedirect(route('masuk'));
        $this->assertGuest();
    }

    public function test_kode_2fa_salah_berulang_dibatasi(): void
    {
        [$u, $rahasia] = $this->stafDua();
        RateLimiter::clear('2fa|'.$u->id.'|127.0.0.1');
        $this->post('/masuk', ['email' => $u->email, 'password' => self::SANDI]);

        for ($i = 0; $i < 5; $i++) {
            $this->from(route('dua-faktor.tantangan'))->post(route('dua-faktor.periksa'), ['kode' => '111111']);
        }
        // Percobaan ke-6 diblokir meski kodenya benar; sesi tantangan dibuang.
        $this->post(route('dua-faktor.periksa'), ['kode' => $this->kodeSekarang($rahasia)])->assertRedirect(route('masuk'));
        $this->assertGuest();
    }

    public function test_nonaktifkan_2fa_butuh_sandi_dan_kode(): void
    {
        [$u, $rahasia] = $this->stafDua();
        $this->actingAs($u);

        $this->delete(route('panel.akun.2fa.nonaktif'), ['sandi' => 'salah-salah-1', 'kode' => $this->kodeSekarang($rahasia)])->assertSessionHasErrors('sandi');
        $this->delete(route('panel.akun.2fa.nonaktif'), ['sandi' => self::SANDI, 'kode' => '000000'])->assertSessionHasErrors('kode');
        $this->assertTrue($u->refresh()->duaFaktorAktif());

        $this->delete(route('panel.akun.2fa.nonaktif'), ['sandi' => self::SANDI, 'kode' => $this->kodeSekarang($rahasia)])->assertSessionHas('status');
        $this->assertFalse($u->refresh()->duaFaktorAktif());
        $this->assertNull($u->two_factor_secret);
    }

    public function test_2fa_wajib_untuk_owner_dan_manager_bila_diaktifkan(): void
    {
        config(['linygadai.wajib_2fa' => true]);
        $owner = $this->staf(Role::Owner);
        $teller = $this->staf(Role::Teller);

        $this->actingAs($owner)->get(route('panel'))->assertRedirect(route('panel.akun.2fa'));
        $this->actingAs($owner)->get(route('panel.akun.2fa'))->assertOk();
        $this->actingAs($teller)->get(route('panel'))->assertOk(); // peran lain tidak diwajibkan
    }

    public function test_2fa_wajib_tidak_dapat_dinonaktifkan_oleh_perannya(): void
    {
        config(['linygadai.wajib_2fa' => true]);
        [$mgr, $rahasia] = $this->stafDua(Role::Manager);

        $this->actingAs($mgr)->delete(route('panel.akun.2fa.nonaktif'), ['sandi' => self::SANDI, 'kode' => $this->kodeSekarang($rahasia)])
            ->assertSessionHasErrors('umum');
        $this->assertTrue($mgr->refresh()->duaFaktorAktif());
    }

    public function test_rahasia_2fa_tersimpan_terenkripsi_dan_tidak_bocor_ke_serialisasi(): void
    {
        [$u, $rahasia] = $this->stafDua();
        $mentah = DB::table('users')->where('id', $u->id)->value('two_factor_secret');
        $this->assertStringNotContainsString($rahasia, (string) $mentah);
        $this->assertArrayNotHasKey('two_factor_secret', $u->toArray());
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $u->toArray());
    }

    // ------------------------------------------------------------ verifikasi email

    public function test_tautan_verifikasi_email_bertanda_tangan_memverifikasi(): void
    {
        $u = User::factory()->create(['email_verified_at' => null]);
        $url = VerifikasiEmail::url($u);

        $this->get($url)->assertRedirect()->assertSessionHas('status');
        $this->assertNotNull($u->refresh()->email_verified_at);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'email.diverifikasi']);
    }

    public function test_tautan_verifikasi_dengan_tanda_tangan_rusak_atau_hash_salah_ditolak(): void
    {
        $u = User::factory()->create(['email_verified_at' => null]);

        $this->get(route('email.verifikasi', ['id' => $u->id, 'hash' => sha1($u->email)]))->assertForbidden(); // tanpa signature
        $this->get(VerifikasiEmail::url($u).'x')->assertForbidden();
        $lain = User::factory()->create(['email_verified_at' => null]);
        $salah = URL::temporarySignedRoute('email.verifikasi', now()->addHour(), ['id' => $u->id, 'hash' => sha1($lain->email)]);
        $this->get($salah)->assertForbidden();
        $this->assertNull($u->refresh()->email_verified_at);
    }

    public function test_tautan_verifikasi_kedaluwarsa_ditolak(): void
    {
        $u = User::factory()->create(['email_verified_at' => null]);
        $url = VerifikasiEmail::url($u);
        $this->travel(25)->hours();
        $this->get($url)->assertForbidden();
        $this->assertNull($u->refresh()->email_verified_at);
    }

    public function test_kirim_ulang_verifikasi_mengirim_notifikasi_hanya_bila_belum_terverifikasi(): void
    {
        Notification::fake();
        $belum = $this->anggota()->user;
        $belum->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($belum)->post(route('panel.akun.verifikasi.kirim'))->assertSessionHas('status');
        Notification::assertSentTo($belum, VerifikasiEmail::class);

        $sudah = $this->anggota()->user;
        $this->actingAs($sudah)->post(route('panel.akun.verifikasi.kirim'));
        Notification::assertNotSentTo($sudah, VerifikasiEmail::class);
    }

    public function test_anggota_dengan_email_belum_terverifikasi_tidak_dapat_mengajukan(): void
    {
        $m = $this->anggota();
        $m->user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($m->user)->post(route('panel.pengajuan.store'), [
            'product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000',
        ])->assertSessionHasErrors();
        $this->assertSame(0, Application::withoutGlobalScopes()->count());

        $m->user->forceFill(['email_verified_at' => now()])->save();
        $this->actingAs($m->user->refresh())->post(route('panel.pengajuan.store'), [
            'product_id' => $this->produk()->id, 'pinjaman_diminta' => '1000000',
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, Application::withoutGlobalScopes()->count());
    }

    // ------------------------------------------------------------ taksir ulang

    public function test_taksir_ulang_menurunkan_plafon_dan_menahan_perpanjangan_sampai_selisih_disetor(): void
    {
        $analis = $this->staf(Role::Analis);
        $teller = $this->staf(Role::Teller);
        $svc = app(LoanService::class);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        $this->assertTrue($svc->tagihan($l)['perpanjang']['boleh']);

        // Taksiran 2.000.000 x LTV 80% = plafon 1.600.000; saldo 2.000.000 -> selisih 400.000.
        $svc->taksirUlang($l->refresh(), $analis, 2_500_000, 2_000_000, 'Harga turun');
        $t = $svc->tagihan($l->refresh());
        $this->assertSame(1_600_000, $t['plafon_ulang']);
        $this->assertSame(400_000, $t['selisih_taksir']);
        $this->assertFalse($t['perpanjang']['boleh']);
        $this->assertStringContainsString('plafon baru', (string) $t['perpanjang']['alasan']);
        $this->assertSame(400_000, $t['cicil']['minimum']);
        $this->assertTrue($t['tebus']['boleh'], 'menebus tidak terpengaruh taksir ulang');

        $svc->bayar($l->refresh(), $teller, 'cicil', 'tunai', 400_000);
        $t = $svc->tagihan($l->refresh());
        $this->assertSame(0, $t['selisih_taksir']);
        $this->assertTrue($t['perpanjang']['boleh']);
    }

    public function test_cicil_di_bawah_selisih_taksir_ulang_ditolak(): void
    {
        $analis = $this->staf(Role::Analis);
        $teller = $this->staf(Role::Teller);
        $svc = app(LoanService::class);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        $svc->taksirUlang($l->refresh(), $analis, 2_500_000, 2_000_000);

        $this->expectException(\DomainException::class);
        $svc->bayar($l->refresh(), $teller, 'cicil', 'tunai', 100_000);
    }

    public function test_taksir_ulang_hanya_untuk_gadai_aktif_atau_tenggang(): void
    {
        $analis = $this->staf(Role::Analis);
        $teller = $this->staf(Role::Teller);
        $svc = app(LoanService::class);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        $svc->bayar($l->refresh(), $teller, 'tebus', 'tunai');

        $this->expectException(\DomainException::class);
        $svc->taksirUlang($l->refresh(), $analis, 2_000_000, 1_500_000);
    }

    public function test_taksir_ulang_lewat_http_hanya_untuk_analis(): void
    {
        $l = $this->buatGadai();
        $data = ['nilai_pasar' => '2.500.000', 'nilai_taksiran' => '2.000.000', 'catatan' => 'uji'];

        foreach ([Role::Teller, Role::Admin, Role::Manager] as $peran) {
            $this->actingAs($this->staf($peran))->post(route('panel.gadai.taksir-ulang', $l), $data)->assertForbidden();
        }
        $this->actingAs($l->member->user)->post(route('panel.gadai.taksir-ulang', $l), $data)->assertForbidden();
        $this->assertNull($l->refresh()->plafon_ulang);

        $this->actingAs($this->staf(Role::Analis))->post(route('panel.gadai.taksir-ulang', $l), $data)->assertSessionHas('status');
        $this->assertSame(1_600_000, (int) $l->refresh()->plafon_ulang);
        $this->assertDatabaseHas('appraisals', ['application_id' => $l->application_id, 'tipe' => 'ulang', 'is_current' => true]);
    }

    public function test_analis_cabang_lain_tidak_dapat_taksir_ulang(): void
    {
        $l = $this->buatGadai();
        $asing = $this->staf(Role::Analis, $this->b);
        $this->actingAs($asing)->post(route('panel.gadai.taksir-ulang', $l), ['nilai_pasar' => '2.500.000', 'nilai_taksiran' => '2.000.000'])->assertNotFound();
    }

    // ------------------------------------------------------------ surat kuasa

    private function gadaiDenganKuasa(): array
    {
        $teller = $this->staf(Role::Teller);
        $svc = app(LoanService::class);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        $svc->bayar($l->refresh(), $teller, 'tebus', 'tunai');
        $svc->keluarkanBarang($l->refresh(), $teller, ['penebus_tipe' => 'kuasa', 'nama_penebus' => 'Wakil Contoh', 'nik_penebus' => '7371999900000001', 'nota_asli_diperiksa' => true],
            UploadedFile::fake()->image('kuasa.png'));

        return [$l->refresh(), $teller];
    }

    public function test_surat_kuasa_dapat_dilihat_staf_cabang_dan_tercatat(): void
    {
        [$l, $teller] = $this->gadaiDenganKuasa();
        $this->assertNotNull($l->redemption->surat_kuasa_path);

        $this->actingAs($teller)->get(route('panel.berkas.kuasa', $l))->assertOk();
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'berkas.kuasa_dilihat']);
    }

    public function test_surat_kuasa_tertutup_bagi_anggota_dan_cabang_lain(): void
    {
        [$l] = $this->gadaiDenganKuasa();
        $this->actingAs($l->member->user)->get(route('panel.berkas.kuasa', $l))->assertForbidden();
        $this->actingAs($this->staf(Role::Teller, $this->b))->get(route('panel.berkas.kuasa', $l))->assertNotFound();
    }

    public function test_surat_kuasa_tanpa_berkas_menghasilkan_404(): void
    {
        $teller = $this->staf(Role::Teller);
        $l = $this->buatGadai(null, 2_000_000, $teller);
        $this->actingAs($teller)->get(route('panel.berkas.kuasa', $l))->assertNotFound();
    }

    // ------------------------------------------------------------ tautan WhatsApp

    public function test_tautan_whatsapp_hanya_untuk_staf_penagih(): void
    {
        $l = $this->buatGadai();
        $l->member->forceFill(['telepon' => '081234567890'])->save();

        $this->actingAs($this->staf(Role::Teller))->get(route('panel.gadai.show', $l))->assertOk()->assertSee('wa.me/6281234567890', false);
        $this->actingAs($this->staf(Role::Analis))->get(route('panel.gadai.show', $l))->assertOk()->assertDontSee('wa.me/', false);
        $this->actingAs($l->member->user)->get(route('panel.gadai.show', $l))->assertOk()->assertDontSee('wa.me/', false);
    }

    // ------------------------------------------------------------ cadangan dan pemeriksa sistem

    public function test_cadangan_membuat_zip_berisi_dump(): void
    {
        $cari = new \ReflectionMethod(CadangkanSistem::class, 'cariMysqldump');
        $cari->setAccessible(true);
        if (! $cari->invoke(new CadangkanSistem)) {
            $this->markTestSkipped('mysqldump tidak tersedia di mesin ini');
        }

        $tujuan = sys_get_temp_dir().'/cadangan-uji-'.uniqid();
        try {
            $this->assertSame(0, Artisan::call('sistem:cadangan', ['--ke' => $tujuan, '--simpan' => 14, '--sandi' => '']));
            $zips = glob("$tujuan/linygadai-*.zip");
            $this->assertCount(1, $zips);
            $z = new \ZipArchive;
            $this->assertTrue($z->open($zips[0]));
            $nama = [];
            for ($i = 0; $i < $z->numFiles; $i++) {
                $nama[] = $z->getNameIndex($i);
            }
            $z->close();
            $this->assertNotEmpty(array_filter($nama, fn ($n) => str_starts_with($n, 'db-') && str_ends_with($n, '.sql')));
            $this->assertEmpty(glob("$tujuan/db-*.sql"), 'dump mentah tidak boleh tersisa di luar ZIP');
        } finally {
            File::deleteDirectory($tujuan);
        }
    }

    public function test_cadangan_lama_dihapus_tetapi_tiga_terbaru_selalu_disisakan(): void
    {
        $dir = sys_get_temp_dir().'/retensi-uji-'.uniqid();
        File::ensureDirectoryExists($dir);
        try {
            foreach (range(1, 5) as $i) { // 5 berkas tua (40 hari) -> 2 terhapus, 3 tersisa
                $f = "$dir/linygadai-2026010$i-000000.zip";
                file_put_contents($f, 'x');
                touch($f, now()->subDays(40)->timestamp + $i);
            }
            $m = new \ReflectionMethod(CadangkanSistem::class, 'bersihkan');
            $m->setAccessible(true);
            $m->invoke(new CadangkanSistem, $dir, 14);
            $this->assertCount(3, glob("$dir/linygadai-*.zip"));
        } finally {
            File::deleteDirectory($dir);
        }
    }

    public function test_periksa_sistem_melaporkan_cadangan_2fa_dan_mode_uji(): void
    {
        $this->artisan('sistem:periksa')
            ->expectsOutputToContain('Cadangan')
            ->expectsOutputToContain('2FA staf')
            ->expectsOutputToContain('Mode uji coba');
    }

    public function test_banner_uji_coba_tidak_tampil_di_situs_siap_deploy(): void
    {
        $this->get('/')->assertOk()
            ->assertDontSee('VERSI UJI COBA', false)
            ->assertDontSee('fiktif', false);
    }
}
