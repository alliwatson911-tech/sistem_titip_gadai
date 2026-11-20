<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Member;
use App\Models\PenitipNotification;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ContohTaksiranSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\PanelTestCase;

class TemuanAuditTest extends PanelTestCase
{
    private function dataPenitip(array $ubah = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Budi Uji', 'nik' => '7371010101010001', 'telepon' => '081234567890',
            'email' => 'budi.uji@contoh.test', 'branch_id' => $this->a->id,
        ], $ubah);
    }

    private function notifikasi(string $cabang, string $pesan): void
    {
        $m = $this->anggota($cabang === 'A' ? $this->a : $this->b);
        PenitipNotification::create(['user_id' => $m->user_id, 'member_id' => $m->id, 'channel' => 'whatsapp', 'tujuan' => '6281', 'subjek' => 's', 'pesan' => $pesan, 'status' => 'antrean']);
    }

    public function test_antrean_notifikasi_hanya_menampilkan_penitip_cabang_staf(): void
    {
        $this->notifikasi('A', 'sandi-cabang-a');
        $this->notifikasi('B', 'sandi-cabang-b');

        $this->actingAs($this->staf(Role::Teller, $this->a))->get(route('panel.anggota.notifikasi'))
            ->assertOk()->assertSee('sandi-cabang-a')->assertDontSee('sandi-cabang-b');
    }

    public function test_staf_cabang_hanya_dapat_mendaftarkan_penitip_ke_cabangnya_sendiri(): void
    {
        $this->actingAs($this->staf(Role::Teller, $this->a))
            ->post(route('panel.anggota.store'), $this->dataPenitip(['branch_id' => $this->b->id]));

        $this->assertSame($this->a->id, Member::withoutGlobalScopes()->latest('id')->firstOrFail()->branch_id);
    }

    public function test_nik_tidak_dikembalikan_ke_form_saat_validasi_gagal(): void
    {
        $this->actingAs($this->staf(Role::Teller, $this->a))
            ->post(route('panel.anggota.store'), $this->dataPenitip(['email' => 'bukan-email']))
            ->assertSessionHasErrors('email')
            ->assertSessionMissing('_old_input.nik');
    }

    public function test_contoh_taksiran_menolak_angka_desimal_berkoma(): void
    {
        $produk = Product::where('kategori_barang', 'laptop')->firstOrFail();

        $this->actingAs($this->staf(Role::Owner))
            ->put(route('panel.contoh.update', $produk), ['contoh' => 'HP | 3,5 | 4,5'])
            ->assertSessionHasErrorsIn('p'.$produk->id, 'contoh');
    }

    public function test_seeder_contoh_tidak_menimpa_suntingan_owner(): void
    {
        $produk = Product::where('kategori_barang', 'laptop')->firstOrFail();
        $produk->update(['contoh_taksiran' => [['label' => 'Buatan Owner', 'min' => 1, 'max' => 2]]]);

        $this->seed(ContohTaksiranSeeder::class);

        $this->assertSame('Buatan Owner', $produk->refresh()->contoh_taksiran[0]['label']);
    }

    public function test_simulasi_menampilkan_pesan_galat_berbahasa_indonesia(): void
    {
        $r = $this->getJson('/simulasi?produk=1&taksiran=3000000&ltv_bp=99999&hari=7')->assertStatus(422);

        $this->assertStringNotContainsString('validation.', json_encode($r->json()));
    }

    public function test_halaman_cabang_tanpa_skrip_dan_handler_inline_agar_lolos_csp(): void
    {
        foreach (['/tentang-kami', '/tentang-kami/cabang', '/kontak'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('onkeyup=', $html, $url);
            $this->assertStringNotContainsString('function filterCabang', $html, $url);
        }
    }

    public function test_sandi_sementara_penitip_tidak_pernah_tersimpan_di_database(): void
    {
        $teller = $this->staf(Role::Teller, $this->a);
        $this->actingAs($teller)->post(route('panel.anggota.store'), $this->dataPenitip())->assertSessionHasNoErrors();
        $akun = session('akun_baru');
        $sandi = $akun['password'];

        $this->assertNotEmpty($sandi);
        $this->assertSame(2, PenitipNotification::count());
        foreach (PenitipNotification::all() as $n) {
            $this->assertStringNotContainsString($sandi, $n->pesan);
        }
        $this->assertStringContainsString(rawurlencode($sandi), $akun['wa_url'], 'petugas tetap dapat mengirim sandi lewat WhatsApp saat pendaftaran');
        $this->actingAs($teller)->get(route('panel.anggota.notifikasi'))->assertOk()->assertDontSee($sandi);
    }

    public function test_migrasi_redaksi_menyamarkan_sandi_pada_notifikasi_lama(): void
    {
        $m = $this->anggota($this->a);
        $id = DB::table('penitip_notifications')->insertGetId(['user_id' => $m->user_id, 'member_id' => $m->id, 'channel' => 'whatsapp', 'tujuan' => '6281', 'subjek' => 's',
            'pesan' => "Email: a@b.test\nKata Sandi Sementara: Persiden123456\n\nSilakan ganti.", 'status' => 'antrean', 'created_at' => now(), 'updated_at' => now()]);

        (require glob(database_path('migrations/*redaksi_sandi_notifikasi_penitip.php'))[0])->up();

        $pesan = DB::table('penitip_notifications')->where('id', $id)->value('pesan');
        $this->assertStringNotContainsString('Persiden123456', $pesan);
        $this->assertStringContainsString('Silakan ganti.', $pesan);
    }

    public function test_foto_ktp_hanya_untuk_pengelola_anggota_dan_pemilik(): void
    {
        $m = $this->anggota($this->a);
        $path = 'ktp/'.$m->id.'/ktp.jpg';
        Storage::disk('local')->put($path, 'isi');
        $m->forceFill(['ktp_path' => $path])->save();
        $url = route('panel.berkas.ktp', $m);

        foreach ([Role::Teller, Role::Manager, Role::Analis] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->get($url)->assertForbidden();
        }
        $this->actingAs($this->staf(Role::Admin, $this->a))->get($url)->assertOk();
        $this->actingAs($this->staf(Role::Owner))->get($url)->assertOk();
        $this->actingAs($m->user)->get($url)->assertOk();
    }

    public function test_login_dikunci_per_email_walau_alamat_ip_berganti(): void
    {
        $u = User::factory()->create(['password' => 'Rahasia12345']);

        for ($i = 1; $i <= 21; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.$i"])->post('/masuk', ['email' => $u->email, 'password' => 'salah-salah-1']);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.9.9.9'])->post('/masuk', ['email' => $u->email, 'password' => 'Rahasia12345'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_reset_sandi_oleh_owner_memaksa_ganti_sandi_saat_login(): void
    {
        $teller = $this->staf(Role::Teller, $this->a);

        $this->actingAs($this->staf(Role::Owner))->post(route('panel.pengguna.reset', $teller))->assertSessionHasNoErrors();

        $this->assertTrue($teller->refresh()->wajib_ganti_password);
        $this->assertTrue(Hash::check(session('sandi_baru.sandi'), $teller->password));
    }

    public function test_data_kalkulator_produk_dan_judul_halaman_lupa_sandi(): void
    {
        $p = Product::where('kategori_barang', 'laptop')->firstOrFail();

        $this->assertSame(['id', 'slug', 'nama', 'ltvMaxBp', 'min', 'max', 'tenor', 'contoh', 'contohTgl'], array_keys($p->dataKalkulator()));
        $this->get('/lupa-sandi')->assertOk()->assertSee('PERSIDEN HP', false)->assertDontSee('— linygadai', false);
    }
}
