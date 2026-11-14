<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\AturUlangSandi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\PanelTestCase;

class KataSandiTest extends PanelTestCase
{
    private const LAMA = 'SandiLama12345';
    private const BARU = 'SandiBaru67890';

    private function akun(Role $role = Role::Teller, array $state = []): User
    {
        return User::factory()->peran($role, $role->isBranchBound() ? $this->a->id : null)->create(['password' => self::LAMA] + $state);
    }

    // ---------- ganti kata sandi (sudah masuk) ----------

    public function test_semua_peran_dapat_mengganti_kata_sandi_dan_sandi_lama_tidak_berlaku(): void
    {
        foreach ([Role::Owner, Role::Manager, Role::Admin, Role::Teller, Role::Analis, Role::Member] as $peran) {
            $u = $this->akun($peran);
            $this->actingAs($u)->put(route('panel.akun.sandi.ubah'), ['sandi_lama' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU])
                ->assertRedirect(route('panel.akun.sandi'))->assertSessionHasNoErrors();

            $u->refresh();
            $this->assertTrue(Hash::check(self::BARU, $u->password), $peran->value);
            $this->assertFalse(Hash::check(self::LAMA, $u->password));
            $this->assertNotSame(self::BARU, $u->password);
        }
    }

    public function test_ganti_sandi_menolak_sandi_lama_salah_lemah_sama_dan_tidak_cocok(): void
    {
        $u = $this->akun();
        $url = route('panel.akun.sandi.ubah');
        $this->actingAs($u);

        $this->put($url, ['sandi_lama' => 'salah-salah-1', 'password' => self::BARU, 'password_confirmation' => self::BARU])->assertSessionHasErrors('sandi_lama');
        $this->put($url, ['sandi_lama' => self::LAMA, 'password' => 'pendek1', 'password_confirmation' => 'pendek1'])->assertSessionHasErrors('password');
        $this->put($url, ['sandi_lama' => self::LAMA, 'password' => 'hurufsajasemua', 'password_confirmation' => 'hurufsajasemua'])->assertSessionHasErrors('password');
        $this->put($url, ['sandi_lama' => self::LAMA, 'password' => self::LAMA, 'password_confirmation' => self::LAMA])->assertSessionHasErrors('password');
        $this->put($url, ['sandi_lama' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => 'berbeda12345'])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check(self::LAMA, $u->refresh()->password), 'sandi tidak boleh berubah bila ada galat');
    }

    public function test_ganti_sandi_mengeluarkan_perangkat_lain_dan_memutar_remember_token(): void
    {
        $u = $this->akun();
        $tokenLama = $u->remember_token;
        DB::table('sessions')->insert(['id' => 'perangkat-lain', 'user_id' => $u->id, 'payload' => '', 'last_activity' => time()]);
        $lainnya = $this->akun(Role::Teller);
        DB::table('sessions')->insert(['id' => 'milik-orang-lain', 'user_id' => $lainnya->id, 'payload' => '', 'last_activity' => time()]);

        $this->actingAs($u)->put(route('panel.akun.sandi.ubah'), ['sandi_lama' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU])->assertSessionHasNoErrors();

        $this->assertSame(0, DB::table('sessions')->where('id', 'perangkat-lain')->count());
        $this->assertSame(1, DB::table('sessions')->where('id', 'milik-orang-lain')->count(), 'sesi pengguna lain tidak boleh tersentuh');
        $this->assertNotSame($tokenLama, $u->refresh()->remember_token);
    }

    public function test_ganti_sandi_tercatat_di_audit_tanpa_membocorkan_sandi(): void
    {
        $u = $this->akun();
        $this->actingAs($u)->put(route('panel.akun.sandi.ubah'), ['sandi_lama' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU]);

        $this->assertTrue(AuditLog::where('aksi', 'auth.ganti_sandi')->where('user_id', $u->id)->exists());
        foreach (AuditLog::all() as $log) {
            $json = json_encode([$log->sebelum, $log->sesudah]);
            $this->assertStringNotContainsString(self::BARU, $json);
            $this->assertStringNotContainsString(self::LAMA, $json);
            $this->assertStringNotContainsString($u->password, $json);
        }
    }

    public function test_setelah_ganti_sandi_login_dengan_sandi_baru_berhasil_dan_lama_gagal(): void
    {
        $u = $this->akun(Role::Member);
        $this->actingAs($u)->put(route('panel.akun.sandi.ubah'), ['sandi_lama' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU]);
        auth()->logout();

        $this->post('/masuk', ['email' => $u->email, 'password' => self::LAMA])->assertSessionHasErrors('password');
        $this->post('/masuk', ['email' => $u->email, 'password' => self::BARU])->assertRedirect('/panel');
    }

    public function test_halaman_ganti_sandi_butuh_login(): void
    {
        $this->get(route('panel.akun.sandi'))->assertRedirect('/masuk');
        $this->put(route('panel.akun.sandi.ubah'), ['password' => self::BARU])->assertRedirect('/masuk');
        $this->actingAs($this->akun())->get(route('panel.akun.sandi'))->assertOk()->assertSee('Ganti kata sandi');
    }

    // ---------- lupa kata sandi ----------

    private function minta(string $email): void
    {
        $this->post(route('sandi.kirim'), ['email' => $email])->assertSessionHasNoErrors();
    }

    public function test_tautan_dikirim_hanya_untuk_akun_aktif_dengan_jawaban_yang_sama(): void
    {
        Notification::fake();
        $aktif = $this->akun();
        $nonaktif = $this->akun(Role::Teller, ['status' => 'nonaktif']);

        $this->minta($aktif->email);
        $pesanAktif = session('status');
        $this->minta('tidak-terdaftar@contoh.test');
        $pesanTidakAda = session('status');
        $this->minta($nonaktif->email);
        $pesanNonaktif = session('status');

        Notification::assertSentToTimes($aktif, AturUlangSandi::class, 1);
        Notification::assertNotSentTo($nonaktif, AturUlangSandi::class);
        Notification::assertCount(1);
        $this->assertSame($pesanAktif, $pesanTidakAda);
        $this->assertSame($pesanAktif, $pesanNonaktif);
        $this->assertTrue(AuditLog::where('aksi', 'sandi.lupa_diminta')->where('subject_id', $aktif->id)->exists());
    }

    public function test_permintaan_dibatasi_per_email_dan_tetap_menjawab_sama(): void
    {
        Notification::fake();
        $u = $this->akun();

        foreach (range(1, 6) as $_) {
            $this->minta($u->email);
            $this->assertStringContainsString('Jika email tersebut terdaftar', session('status'));
        }
        // Dibatasi 3 percobaan per 15 menit per email+IP; pengirim ulang juga dibatasi broker (60 detik).
        Notification::assertSentToTimes($u, AturUlangSandi::class, 1);
    }

    public function test_lupa_sandi_menolak_email_tidak_valid_dan_halaman_tidak_diindeks(): void
    {
        $this->post(route('sandi.kirim'), ['email' => 'bukan-email'])->assertSessionHasErrors('email');
        $this->get(route('sandi.lupa'))->assertOk()->assertSee('noindex,nofollow', false);
        $this->get('/')->assertSee('Lupa kata sandi?');
    }

    public function test_email_berisi_tautan_dengan_token_dan_masa_berlaku(): void
    {
        $u = $this->akun();
        $mail = (new AturUlangSandi('tokenuji123'))->toMail($u);

        $this->assertStringContainsString('/atur-ulang-sandi/tokenuji123', $mail->actionUrl);
        $this->assertStringContainsString('email='.urlencode($u->email), $mail->actionUrl);
        $this->assertStringContainsString('60 menit', implode(' ', array_map('strval', $mail->outroLines)));
    }

    // ---------- atur ulang lewat tautan ----------

    private function tokenUntuk(User $u): string
    {
        Notification::fake();
        $this->minta($u->email);
        $token = null;
        Notification::assertSentTo($u, AturUlangSandi::class, function ($n) use (&$token) {
            $token = $n->token;

            return true;
        });

        return $token;
    }

    private function atur(string $token, string $email, string $sandi = self::BARU)
    {
        return $this->post(route('sandi.simpan'), ['token' => $token, 'email' => $email, 'password' => $sandi, 'password_confirmation' => $sandi]);
    }

    public function test_atur_ulang_berhasil_sekali_pakai_dan_mengeluarkan_semua_perangkat(): void
    {
        $u = $this->akun(Role::Member);
        DB::table('sessions')->insert(['id' => 'sesi-lama', 'user_id' => $u->id, 'payload' => '', 'last_activity' => time()]);
        $token = $this->tokenUntuk($u);

        $this->get(route('sandi.atur-ulang', ['token' => $token, 'email' => $u->email]))->assertOk()->assertSee('noindex,nofollow', false)->assertSee($u->email);
        $this->atur($token, $u->email)->assertRedirect('/masuk')->assertSessionHas('status');

        $u->refresh();
        $this->assertTrue(Hash::check(self::BARU, $u->password));
        $this->assertSame(0, DB::table('sessions')->where('user_id', $u->id)->count());
        $this->assertTrue(AuditLog::where('aksi', 'sandi.diatur_ulang')->where('subject_id', $u->id)->exists());

        // token tidak bisa dipakai lagi
        $this->atur($token, $u->email, 'SandiLain54321')->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::BARU, $u->refresh()->password));

        // login dengan sandi baru
        $this->post('/masuk', ['email' => $u->email, 'password' => self::BARU])->assertRedirect('/panel');
    }

    public function test_token_atau_email_salah_ditolak_tanpa_mengubah_sandi(): void
    {
        $u = $this->akun();
        $lain = $this->akun(Role::Analis);
        $token = $this->tokenUntuk($u);

        $this->atur('token-palsu', $u->email)->assertSessionHasErrors('email');
        $this->atur($token, $lain->email)->assertSessionHasErrors('email'); // token milik orang lain
        $this->atur($token, 'tidak-ada@contoh.test')->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::LAMA, $u->refresh()->password));
        $this->assertTrue(Hash::check(self::LAMA, $lain->refresh()->password));
    }

    public function test_sandi_lemah_ditolak_tetapi_token_tetap_berlaku(): void
    {
        $u = $this->akun();
        $token = $this->tokenUntuk($u);

        $this->atur($token, $u->email, 'pendek1')->assertSessionHasErrors('password');
        $this->atur($token, $u->email)->assertRedirect('/masuk');
        $this->assertTrue(Hash::check(self::BARU, $u->refresh()->password));
    }

    public function test_token_kedaluwarsa_setelah_60_menit(): void
    {
        $u = $this->akun();
        $token = $this->tokenUntuk($u);

        $this->travel(61)->minutes();
        $this->atur($token, $u->email)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::LAMA, $u->refresh()->password));
    }

    public function test_akun_yang_dinonaktifkan_setelah_meminta_tautan_tidak_dapat_memakainya(): void
    {
        $u = $this->akun();
        $token = $this->tokenUntuk($u);
        $u->forceFill(['status' => 'nonaktif'])->save();

        $this->atur($token, $u->email)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::LAMA, $u->refresh()->password));
    }

    public function test_pengguna_yang_sudah_masuk_dialihkan_dari_halaman_lupa_sandi(): void
    {
        $this->actingAs($this->akun())->get(route('sandi.lupa'))->assertRedirect(route('panel'));
    }
}
