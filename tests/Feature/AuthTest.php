<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Member;
use App\Models\PenitipNotification;
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

    public function test_login_pertama_dengan_password_sementara_diwajibkan_ganti_sandi(): void
    {
        $user = $this->akun(['wajib_ganti_password' => true]);

        $this->post('/masuk', ['email' => $user->email, 'password' => self::SANDI])
            ->assertRedirect(route('panel.akun.sandi'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_tercatat_tanpa_membocorkan_alasan(): void
    {
        $user = $this->akun();

        $this->from('/masuk')->post('/masuk', ['email' => $user->email, 'password' => 'salah-salah-1'])
            ->assertRedirect('/masuk')
            ->assertSessionHasErrors(['password' => 'Kata sandi salah. Silakan periksa kembali.']);

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'auth.gagal']);

        $this->from('/masuk')->post('/masuk', ['email' => 'tidakada@persiden.test', 'password' => 'salah-salah-1'])
            ->assertRedirect('/masuk')
            ->assertSessionHasErrors(['email' => 'Akun tidak terdaftar atau email salah.']);
    }

    public function test_akun_nonaktif_tidak_bisa_masuk_dengan_pesan_yang_sama(): void
    {
        $user = $this->akun(['status' => 'nonaktif']);

        $this->from('/masuk')->post('/masuk', ['email' => $user->email, 'password' => self::SANDI])
            ->assertSessionHasErrors(['email' => 'Akun dinonaktifkan. Silakan hubungi pengelola cabang.']);
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

    public function test_pendaftaran_mandiri_publik_dialihkan_ke_masuk(): void
    {
        $this->get('/daftar')->assertRedirect('/masuk');
    }

    public function test_staf_loket_dapat_mendaftarkan_penitip_baru_dan_notifikasi_tercatat(): void
    {
        $cabang = Branch::factory()->create(['kode' => 'BTP', 'nama' => 'Persiden HP BTP']);
        $staf = User::factory()->create([
            'role' => Role::Teller,
            'branch_id' => $cabang->id,
            'status' => 'aktif',
        ]);

        $data = [
            'nama_lengkap' => 'Ahmad Penitip',
            'nik' => '7371010101900001',
            'telepon' => '081234567890',
            'email' => 'ahmad@persiden.test',
            'branch_id' => $cabang->id,
            'alamat' => 'Jl. BTP Blok M',
        ];

        $this->actingAs($staf)
            ->post('/panel/anggota', $data)
            ->assertRedirect('/panel/anggota')
            ->assertSessionHas('status');

        $userPenitip = User::where('email', 'ahmad@persiden.test')->firstOrFail();
        $this->assertSame(Role::Member, $userPenitip->role);
        $this->assertTrue($userPenitip->wajib_ganti_password);

        $member = Member::withoutGlobalScopes()->where('user_id', $userPenitip->id)->firstOrFail();
        $this->assertSame('terverifikasi', $member->status);
        $this->assertSame('7371010101900001', $member->nik_enc);
        $this->assertSame(64, strlen($member->nik_hash));

        // Verifikasi antrean notifikasi (WhatsApp & Email)
        $this->assertDatabaseHas('penitip_notifications', [
            'user_id' => $userPenitip->id,
            'member_id' => $member->id,
            'channel' => 'whatsapp',
            'status' => 'antrean',
        ]);
        $this->assertDatabaseHas('penitip_notifications', [
            'user_id' => $userPenitip->id,
            'member_id' => $member->id,
            'channel' => 'email',
            'status' => 'antrean',
        ]);
    }

    public function test_audit_tidak_memuat_kata_sandi(): void
    {
        $user = $this->akun();
        $log = AuditLog::where('aksi', 'user.dibuat')->where('subject_id', $user->id)->firstOrFail();

        $this->assertArrayNotHasKey('password', $log->sesudah);
        $this->assertArrayNotHasKey('remember_token', $log->sesudah);
    }
}
