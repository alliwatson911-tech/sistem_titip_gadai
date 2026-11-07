<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private const SANDI = 'Rahasia12345';

    private function akun(array $state = []): User
    {
        return User::factory()->create(['password' => self::SANDI] + $state);
    }

    public function test_login_berhasil_dan_tercatat(): void
    {
        $user = $this->akun();

        $this->post('/masuk', ['email' => $user->email, 'password' => self::SANDI])->assertRedirect('/panel');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'auth.masuk', 'user_id' => $user->id]);
    }

    public function test_login_gagal_tercatat_tanpa_membocorkan_alasan(): void
    {
        $user = $this->akun();

        $this->from('/masuk')->post('/masuk', ['email' => $user->email, 'password' => 'salah-salah-1'])
            ->assertRedirect('/masuk')
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.']);

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'auth.gagal']);
    }

    public function test_akun_nonaktif_tidak_bisa_masuk_dengan_pesan_yang_sama(): void
    {
        $user = $this->akun(['status' => 'nonaktif']);

        $this->from('/masuk')->post('/masuk', ['email' => $user->email, 'password' => self::SANDI])
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.']);
        $this->assertGuest();
    }

    public function test_dibatasi_setelah_lima_percobaan_gagal(): void
    {
        $user = $this->akun();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/masuk', ['email' => $user->email, 'password' => 'salah-salah-1']);
        }

        // Percobaan ke-6 dengan sandi BENAR pun ditolak selama diblokir.
        $r = $this->post('/masuk', ['email' => $user->email, 'password' => self::SANDI]);
        $r->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_pengguna_dinonaktifkan_di_tengah_sesi_langsung_keluar(): void
    {
        $user = $this->akun();
        $this->actingAs($user);
        $this->get('/panel')->assertOk();

        $user->forceFill(['status' => 'nonaktif'])->save();

        $this->get('/panel')->assertRedirect('/masuk');
        $this->assertGuest();
    }

    public function test_keluar_mengakhiri_sesi(): void
    {
        $this->actingAs($this->akun());
        $this->post('/keluar')->assertRedirect('/');
        $this->assertGuest();
    }

    private function dataDaftar(Branch $cabang, array $ubah = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '7371010101900001',
            'telepon' => '081234567890',
            'email' => 'budi@contoh.test',
            'branch_id' => $cabang->id,
            'password' => self::SANDI,
            'password_confirmation' => self::SANDI,
            'setuju' => '1',
        ], $ubah);
    }

    public function test_registrasi_membuat_member_calon_dengan_nik_terenkripsi(): void
    {
        $cabang = Branch::factory()->create(['kode' => 'A']);

        $this->post('/daftar', $this->dataDaftar($cabang))->assertRedirect('/panel');

        $user = User::where('email', 'budi@contoh.test')->firstOrFail();
        $this->assertSame(Role::Member, $user->role);
        $this->assertNull($user->branch_id);

        $mentah = DB::table('members')->where('user_id', $user->id)->first();
        $this->assertSame('calon', $mentah->status);
        $this->assertNotSame('7371010101900001', $mentah->nik_enc, 'NIK tidak boleh tersimpan polos');
        $this->assertStringNotContainsString('7371010101900001', $mentah->nik_enc);
        $this->assertSame(64, strlen($mentah->nik_hash));
        $this->assertSame('7371010101900001', Member::withoutGlobalScopes()->find($mentah->id)->nik_enc);
        $this->assertNotSame(self::SANDI, $user->password, 'kata sandi harus di-hash');
    }

    public function test_registrasi_tidak_bisa_menyetel_peran_atau_status(): void
    {
        $cabang = Branch::factory()->create(['kode' => 'A']);

        $this->post('/daftar', $this->dataDaftar($cabang, ['role' => 'owner', 'status' => 'terverifikasi']));

        $user = User::where('email', 'budi@contoh.test')->firstOrFail();
        $this->assertSame(Role::Member, $user->role);
        $this->assertSame('calon', DB::table('members')->where('user_id', $user->id)->value('status'));
    }

    public function test_registrasi_menolak_nik_ganda_dan_kantor_pusat(): void
    {
        $cabang = Branch::factory()->create(['kode' => 'A']);
        $pusat = Branch::factory()->create(['kode' => 'PUSAT']);
        $this->post('/daftar', $this->dataDaftar($cabang))->assertRedirect('/panel');
        $this->post('/keluar');

        $this->post('/daftar', $this->dataDaftar($cabang, ['email' => 'lain@contoh.test']))->assertSessionHasErrors('nik');
        $this->post('/daftar', $this->dataDaftar($pusat, ['email' => 'lain2@contoh.test', 'nik' => '7371010101900002']))->assertSessionHasErrors('branch_id');
    }

    public function test_registrasi_menolak_sandi_lemah(): void
    {
        $cabang = Branch::factory()->create(['kode' => 'A']);

        $this->post('/daftar', $this->dataDaftar($cabang, ['password' => 'pendek1', 'password_confirmation' => 'pendek1']))
            ->assertSessionHasErrors('password');
    }

    public function test_audit_tidak_memuat_kata_sandi(): void
    {
        $user = $this->akun();
        $log = AuditLog::where('aksi', 'user.dibuat')->where('subject_id', $user->id)->firstOrFail();

        $this->assertArrayNotHasKey('password', $log->sesudah);
        $this->assertArrayNotHasKey('remember_token', $log->sesudah);
    }
}
