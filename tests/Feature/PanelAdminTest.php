<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\PanelTestCase;

class PanelAdminTest extends PanelTestCase
{
    // ---------- masuk/daftar menyatu di halaman utama ----------

    public function test_alamat_masuk_dan_daftar_dialihkan_ke_halaman_utama(): void
    {
        $this->get('/masuk')->assertRedirect('/#akun');
        $this->get('/daftar')->assertRedirect('/#akun');
    }

    // ---------- verifikasi anggota ----------

    public function test_admin_memverifikasi_anggota_cabangnya_setelah_ktp_diunggah(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);
        $m = $this->anggota($this->a, 'calon');

        $this->actingAs($admin)->post(route('panel.anggota.tindakan', $m), ['aksi' => 'verifikasi'])->assertSessionHasErrors('umum');
        $this->assertSame('calon', $m->refresh()->status);

        $m->forceFill(['ktp_path' => 'ktp/'.$m->id.'/x.jpg'])->save();
        $this->actingAs($admin)->post(route('panel.anggota.tindakan', $m), ['aksi' => 'verifikasi'])->assertSessionHasNoErrors();

        $m->refresh();
        $this->assertSame('terverifikasi', $m->status);
        $this->assertSame($admin->id, $m->verified_by);
        $this->assertNotNull($m->verified_at);
        $this->assertTrue(AuditLog::where('aksi', 'member.diubah')->where('subject_id', $m->id)->exists());
    }

    public function test_hanya_admin_cabang_yang_sama_yang_boleh_memverifikasi(): void
    {
        $m = $this->anggota($this->a, 'calon');
        $m->forceFill(['ktp_path' => 'ktp/1/x.jpg'])->save();

        $this->actingAs($this->staf(Role::Admin, $this->b))->post(route('panel.anggota.tindakan', $m), ['aksi' => 'verifikasi'])->assertNotFound();
        foreach ([Role::Teller, Role::Analis, Role::Manager] as $peran) {
            $this->actingAs($this->staf($peran, $this->a))->post(route('panel.anggota.tindakan', $m), ['aksi' => 'verifikasi'])->assertForbidden();
        }
        $this->actingAs($m->user)->post(route('panel.anggota.tindakan', $m), ['aksi' => 'verifikasi'])->assertForbidden();
        $this->assertSame('calon', $m->refresh()->status);
    }

    public function test_nik_tersamar_di_daftar_dan_lengkap_hanya_atas_permintaan_dengan_audit(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);
        $m = $this->anggota($this->a);
        $nik = $m->nik_enc;

        $this->actingAs($admin)->get(route('panel.anggota'))->assertDontSee($nik);
        $this->actingAs($admin)->get(route('panel.anggota.show', $m))->assertDontSee($nik);

        $this->actingAs($admin)->post(route('panel.anggota.nik', $m))->assertOk()->assertSee($nik);
        $this->assertTrue(AuditLog::where('aksi', 'anggota.lihat_nik')->where('subject_id', $m->id)->where('user_id', $admin->id)->exists());

        $this->actingAs($this->staf(Role::Teller, $this->a))->post(route('panel.anggota.nik', $m))->assertForbidden();
    }

    // ---------- profil dan KTP ----------

    public function test_anggota_mengubah_kontak_tetapi_bukan_nama_atau_nik(): void
    {
        $m = $this->anggota();
        $nama = $m->nama_lengkap;

        $this->actingAs($m->user)->put(route('panel.profil.update'), [
            'telepon' => '081299998888', 'alamat' => 'Jl. Baru 5', 'tgl_lahir' => '1990-05-20',
            'nama_lengkap' => 'Diganti Paksa', 'nik_enc' => '0000000000000000', 'status' => 'terverifikasi',
        ])->assertSessionHasNoErrors();

        $m->refresh();
        $this->assertSame('081299998888', $m->telepon);
        $this->assertSame('Jl. Baru 5', $m->alamat);
        $this->assertSame($nama, $m->nama_lengkap);
        $this->assertNotSame('0000000000000000', $m->nik_enc);
    }

    public function test_unggah_ktp_valid_tersimpan_privat_dan_menolak_selain_gambar(): void
    {
        $m = $this->anggota(null, 'ditolak');

        $this->actingAs($m->user)->post(route('panel.profil.ktp'), ['ktp' => UploadedFile::fake()->create('ktp.pdf', 50, 'application/pdf')])->assertSessionHasErrors('ktp');
        $this->actingAs($m->user)->post(route('panel.profil.ktp'), ['ktp' => UploadedFile::fake()->image('ktp.jpg')->size(5000)])->assertSessionHasErrors('ktp'); // > 4 MB

        $this->actingAs($m->user)->post(route('panel.profil.ktp'), ['ktp' => UploadedFile::fake()->image('ktp.jpg')])->assertSessionHasNoErrors();
        $m->refresh();
        $this->assertNotNull($m->ktp_path);
        Storage::disk('local')->assertExists($m->ktp_path);
        $this->assertFalse(Storage::disk('public')->exists($m->ktp_path));
        $this->assertSame('calon', $m->status, 'unggah ulang setelah ditolak = minta ditinjau lagi');
    }

    public function test_foto_ktp_hanya_dapat_dibuka_pemilik_dan_staf_cabangnya(): void
    {
        $m = $this->anggota($this->a);
        $lain = $this->anggota($this->a);
        $path = 'ktp/'.$m->id.'/ktp.jpg';
        Storage::disk('local')->put($path, 'isi-gambar');
        $m->forceFill(['ktp_path' => $path])->save();
        $url = route('panel.berkas.ktp', $m);

        $this->actingAs($m->user)->get($url)->assertOk();
        $this->actingAs($lain->user)->get($url)->assertNotFound();
        $this->actingAs($this->staf(Role::Admin, $this->a))->get($url)->assertOk();
        $this->assertTrue(AuditLog::where('aksi', 'berkas.ktp_dilihat')->where('subject_id', $m->id)->exists());
        $this->actingAs($this->staf(Role::Admin, $this->b))->get($url)->assertNotFound();
        auth()->logout();
        $this->get($url)->assertRedirect('/masuk');
    }

    // ---------- pengumuman dan FAQ ----------

    public function test_admin_membuat_pengumuman_untuk_cabangnya_dan_tampil_di_situs(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);

        $this->actingAs($admin)->post(route('panel.pengumuman.store'), ['judul' => 'Libur Cabang A', 'isi' => 'Tutup Senin.', 'is_published' => '1', 'branch_id' => $this->b->id])
            ->assertRedirect(route('panel.pengumuman.index'));

        $p = Announcement::withoutGlobalScopes()->where('judul', 'Libur Cabang A')->firstOrFail();
        $this->assertSame($this->a->id, $p->branch_id, 'Admin tidak bisa menaruh pengumuman di cabang lain');
        $this->assertSame($admin->id, $p->dibuat_oleh);

        auth()->logout();
        $this->get('/')->assertSee('Libur Cabang A')->assertSee('Pengumuman · Cabang A');
    }

    public function test_admin_tidak_dapat_mengubah_atau_melihat_pengumuman_cabang_lain(): void
    {
        $p = Announcement::create(['judul' => 'Milik B', 'isi' => 'x', 'is_published' => true, 'branch_id' => $this->b->id]);
        $adminA = $this->staf(Role::Admin, $this->a);

        $this->actingAs($adminA)->get(route('panel.pengumuman.edit', $p))->assertNotFound();
        $this->actingAs($adminA)->put(route('panel.pengumuman.update', $p), ['judul' => 'Dibajak', 'isi' => 'x'])->assertNotFound();
        $this->actingAs($adminA)->delete(route('panel.pengumuman.destroy', $p))->assertNotFound();
        $this->actingAs($adminA)->get(route('panel.pengumuman.index'))->assertDontSee('Milik B');
        $this->assertSame('Milik B', $p->refresh()->judul);
    }

    public function test_pengumuman_draf_tidak_tampil_dan_hapus_adalah_soft_delete(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);
        $this->actingAs($admin)->post(route('panel.pengumuman.store'), ['judul' => 'Draf Rahasia', 'isi' => 'x']);
        $p = Announcement::withoutGlobalScopes()->where('judul', 'Draf Rahasia')->firstOrFail();

        auth()->logout();
        $this->get('/')->assertDontSee('Draf Rahasia');

        $this->actingAs($admin)->delete(route('panel.pengumuman.destroy', $p))->assertRedirect();
        $this->assertSoftDeleted('announcements', ['id' => $p->id]);
    }

    public function test_pengumuman_tanggal_selesai_tidak_boleh_sebelum_mulai(): void
    {
        $this->actingAs($this->staf(Role::Admin, $this->a))->post(route('panel.pengumuman.store'), ['judul' => 'X', 'isi' => 'y', 'mulai' => '2026-12-10', 'selesai' => '2026-12-01'])
            ->assertSessionHasErrors('selesai');
    }

    public function test_peran_lain_tidak_boleh_mengelola_konten(): void
    {
        foreach ([Role::Manager, Role::Teller, Role::Analis] as $peran) {
            $u = $this->staf($peran, $this->a);
            $this->actingAs($u)->post(route('panel.pengumuman.store'), ['judul' => 'X', 'isi' => 'y'])->assertForbidden();
            $this->actingAs($u)->post(route('panel.faq.store'), ['kelompok' => 'faq', 'pertanyaan' => 'q', 'jawaban' => 'a'])->assertForbidden();
        }
        $this->actingAs($this->anggota()->user)->get(route('panel.pengumuman.index'))->assertForbidden();
        $this->assertSame(0, Announcement::withoutGlobalScopes()->where('judul', 'X')->count());
    }

    public function test_owner_membuat_pengumuman_global_dan_faq(): void
    {
        $owner = $this->staf(Role::Owner);

        $this->actingAs($owner)->post(route('panel.pengumuman.store'), ['judul' => 'Untuk Semua', 'isi' => 'x', 'is_published' => '1', 'branch_id' => ''])->assertSessionHasNoErrors();
        $this->assertNull(Announcement::withoutGlobalScopes()->where('judul', 'Untuk Semua')->firstOrFail()->branch_id);

        $this->actingAs($owner)->post(route('panel.faq.store'), ['kelompok' => 'informasi', 'pertanyaan' => 'Jam buka?', 'jawaban' => 'Pukul {tenor}.', 'urutan' => 9, 'is_published' => '1'])->assertSessionHasNoErrors();
        auth()->logout();
        $this->get('/')->assertSee('Jam buka?')->assertSee('Pukul 30.'); // token diganti
    }

    public function test_admin_faq_dibatasi_cabangnya(): void
    {
        $adminA = $this->staf(Role::Admin, $this->a);
        $this->actingAs($adminA)->post(route('panel.faq.store'), ['kelompok' => 'faq', 'pertanyaan' => 'Soal A?', 'jawaban' => 'Jawab', 'is_published' => '1']);
        $f = Faq::withoutGlobalScopes()->where('pertanyaan', 'Soal A?')->firstOrFail();
        $this->assertSame($this->a->id, $f->branch_id);

        $this->actingAs($this->staf(Role::Admin, $this->b))->get(route('panel.faq.edit', $f))->assertNotFound();
        $this->actingAs($adminA)->put(route('panel.faq.update', $f), ['kelompok' => 'faq', 'pertanyaan' => 'Soal A diubah?', 'jawaban' => 'J'])->assertSessionHasNoErrors();
        $this->assertSame('Soal A diubah?', $f->refresh()->pertanyaan);
    }

    // ---------- kontak cabang ----------

    public function test_admin_mengubah_kontak_cabangnya_sendiri(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);

        $this->actingAs($admin)->put(route('panel.kontak.update', $this->a), [
            'alamat' => 'Jl. Alfa No. 1', 'whatsapp' => '081234567890', 'telepon' => '0411 222 333', 'email' => 'a@contoh.test',
            'lat' => '-5.1476', 'lng' => '119.4327', 'jam_buka' => 'Senin-Jumat 08.00-16.00',
        ])->assertRedirect(route('panel.kontak.index'));

        $this->assertSame('Jl. Alfa No. 1', $this->a->refresh()->alamat);
        auth()->logout();
        $this->get('/')->assertSee('Jl. Alfa No. 1');
    }

    public function test_admin_tidak_dapat_mengubah_kontak_cabang_lain_atau_kantor_pusat(): void
    {
        $admin = $this->staf(Role::Admin, $this->a);
        $pusat = Branch::where('kode', 'PUSAT')->firstOrFail();

        $this->actingAs($admin)->put(route('panel.kontak.update', $this->b), ['alamat' => 'Dibajak'])->assertNotFound();
        $this->actingAs($admin)->put(route('panel.kontak.update', $pusat), ['alamat' => 'Dibajak'])->assertNotFound();
        $this->actingAs($admin)->get(route('panel.kontak.edit', $pusat))->assertNotFound();
        $this->assertNull($this->b->refresh()->alamat);
        $this->assertNull($pusat->refresh()->alamat);
    }

    public function test_owner_mengisi_kontak_pusat_dan_situs_menampilkannya(): void
    {
        $pusat = Branch::where('kode', 'PUSAT')->firstOrFail();

        $this->actingAs($this->staf(Role::Owner))->put(route('panel.kontak.update', $pusat), ['whatsapp' => '081234567890', 'email' => 'halo@contoh.test', 'lat' => '-5.1476', 'lng' => '119.4327'])
            ->assertSessionHasNoErrors();

        auth()->logout();
        $this->get('/')->assertSee('https://wa.me/6281234567890', false)->assertSee('mailto:halo@contoh.test', false)->assertSee('google.com/maps', false);
    }

    public function test_validasi_kontak(): void
    {
        $owner = $this->staf(Role::Owner);
        $this->actingAs($owner)->put(route('panel.kontak.update', $this->a), ['lat' => '95', 'lng' => '10'])->assertSessionHasErrors('lat');
        $this->actingAs($owner)->put(route('panel.kontak.update', $this->a), ['lat' => '-5.1'])->assertSessionHasErrors('lng');
        $this->actingAs($owner)->put(route('panel.kontak.update', $this->a), ['telepon' => 'abc<script>'])->assertSessionHasErrors('telepon');
        $this->actingAs($owner)->put(route('panel.kontak.update', $this->a), ['email' => 'bukan-email'])->assertSessionHasErrors('email');
    }

    // ---------- konten global (Owner) ----------

    public function test_owner_mengubah_tagline_dan_izin_usaha(): void
    {
        $this->actingAs($this->staf(Role::Owner))->put(route('panel.situs.update'), [
            'tagline' => 'Tagline Baru yang tenang', 'sambutan' => 'Sambutan baru.', 'ringkasan' => 'Ringkasan baru.',
            'izin_usaha' => 'Terdaftar dengan nomor CONTOH-123.', 'sosmed' => ['instagram' => 'https://instagram.com/contoh', 'facebook' => '', 'tiktok' => '', 'youtube' => ''],
        ])->assertSessionHasNoErrors();

        auth()->logout();
        $this->get('/')->assertSee('Tagline Baru yang', false)->assertSee('Terdaftar dengan nomor CONTOH-123.')->assertSee('https://instagram.com/contoh', false)
            ->assertDontSee('Informasi perizinan resmi akan ditampilkan di sini');
        $this->assertTrue(AuditLog::where('aksi', 'situs.diubah')->exists());
    }

    public function test_tautan_sosmed_harus_https_dan_admin_tidak_boleh_mengubah_konten_global(): void
    {
        $dasar = ['tagline' => 'T', 'sambutan' => 'S', 'ringkasan' => 'R'];
        $this->actingAs($this->staf(Role::Owner))->put(route('panel.situs.update'), $dasar + ['sosmed' => ['instagram' => 'http://tidak-aman.test']])->assertSessionHasErrors('sosmed.instagram');
        $this->actingAs($this->staf(Role::Owner))->put(route('panel.situs.update'), $dasar + ['sosmed' => ['instagram' => 'javascript:alert(1)']])->assertSessionHasErrors('sosmed.instagram');

        $admin = $this->staf(Role::Admin, $this->a);
        $this->actingAs($admin)->get(route('panel.situs'))->assertForbidden();
        $this->actingAs($admin)->put(route('panel.situs.update'), $dasar)->assertForbidden();
    }

    // ---------- manajemen pengguna ----------

    public function test_owner_membuat_staf_dengan_sandi_sementara_yang_dihash(): void
    {
        $owner = $this->staf(Role::Owner);

        $this->actingAs($owner)->post(route('panel.pengguna.store'), ['name' => 'Teller Baru', 'email' => 'teller.baru@contoh.test', 'role' => 'teller', 'branch_id' => $this->a->id])
            ->assertRedirect(route('panel.pengguna.index'))->assertSessionHas('sandi_baru');

        $sandi = session('sandi_baru.sandi');
        $u = User::where('email', 'teller.baru@contoh.test')->firstOrFail();
        $this->assertSame(Role::Teller, $u->role);
        $this->assertSame($this->a->id, $u->branch_id);
        $this->assertNotSame($sandi, $u->password);
        $this->assertTrue(Hash::check($sandi, $u->password));

        auth()->logout();
        $this->post('/masuk', ['email' => 'teller.baru@contoh.test', 'password' => $sandi])->assertRedirect('/panel');
    }

    public function test_pembuatan_staf_menolak_peran_anggota_cabang_pusat_dan_peran_tanpa_cabang(): void
    {
        $owner = $this->staf(Role::Owner);
        $pusat = Branch::where('kode', 'PUSAT')->firstOrFail();
        $dasar = ['name' => 'X', 'email' => 'x@contoh.test'];

        $this->actingAs($owner)->post(route('panel.pengguna.store'), $dasar + ['role' => 'member'])->assertSessionHasErrors('role');
        $this->actingAs($owner)->post(route('panel.pengguna.store'), $dasar + ['role' => 'teller'])->assertSessionHasErrors('branch_id');
        $this->actingAs($owner)->post(route('panel.pengguna.store'), $dasar + ['role' => 'teller', 'branch_id' => $pusat->id])->assertSessionHasErrors('branch_id');
        $this->actingAs($owner)->post(route('panel.pengguna.store'), $dasar + ['role' => 'superadmin'])->assertSessionHasErrors('role');
        $this->assertFalse(User::where('email', 'x@contoh.test')->exists());
    }

    public function test_hanya_owner_mengelola_pengguna(): void
    {
        $target = $this->staf(Role::Teller, $this->a);
        foreach ([Role::Manager, Role::Admin, Role::Teller, Role::Analis] as $peran) {
            $u = $this->staf($peran, $this->a);
            $this->actingAs($u)->get(route('panel.pengguna.index'))->assertForbidden();
            $this->actingAs($u)->post(route('panel.pengguna.store'), ['name' => 'X', 'email' => 'y@contoh.test', 'role' => 'owner'])->assertForbidden();
            $this->actingAs($u)->put(route('panel.pengguna.update', $target), ['name' => 'X', 'email' => 'z@contoh.test', 'role' => 'owner', 'status' => 'aktif'])->assertForbidden();
            $this->actingAs($u)->post(route('panel.pengguna.reset', $target))->assertForbidden();
        }
        $this->assertSame(Role::Teller, $target->refresh()->role);
    }

    public function test_owner_tidak_dapat_mengubah_peran_atau_menonaktifkan_diri_sendiri(): void
    {
        $owner = $this->staf(Role::Owner);

        $this->actingAs($owner)->put(route('panel.pengguna.update', $owner), ['name' => $owner->name, 'email' => $owner->email, 'role' => 'teller', 'branch_id' => $this->a->id, 'status' => 'aktif'])->assertSessionHasErrors('umum');
        $this->actingAs($owner)->put(route('panel.pengguna.update', $owner), ['name' => $owner->name, 'email' => $owner->email, 'role' => 'owner', 'status' => 'nonaktif'])->assertSessionHasErrors('umum');
        $this->assertSame(Role::Owner, $owner->refresh()->role);
        $this->assertSame('aktif', $owner->status);
    }

    public function test_menonaktifkan_pengguna_mengakhiri_sesinya(): void
    {
        $owner = $this->staf(Role::Owner);
        $teller = $this->staf(Role::Teller, $this->a);
        DB::table('sessions')->insert(['id' => 'sesi-uji', 'user_id' => $teller->id, 'payload' => '', 'last_activity' => time()]);

        $this->actingAs($owner)->put(route('panel.pengguna.update', $teller), ['name' => $teller->name, 'email' => $teller->email, 'role' => 'teller', 'branch_id' => $this->a->id, 'status' => 'nonaktif'])
            ->assertSessionHasNoErrors();

        $this->assertSame('nonaktif', $teller->refresh()->status);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $teller->id)->count());
    }

    public function test_reset_sandi_mengganti_sandi_dan_mengakhiri_sesi(): void
    {
        $owner = $this->staf(Role::Owner);
        $teller = User::factory()->peran(Role::Teller, $this->a->id)->create(['password' => 'SandiLama12345']);
        DB::table('sessions')->insert(['id' => 'sesi-uji-2', 'user_id' => $teller->id, 'payload' => '', 'last_activity' => time()]);

        $this->actingAs($owner)->post(route('panel.pengguna.reset', $teller))->assertSessionHas('sandi_baru');
        $baru = session('sandi_baru.sandi');

        $teller->refresh();
        $this->assertFalse(Hash::check('SandiLama12345', $teller->password));
        $this->assertTrue(Hash::check($baru, $teller->password));
        $this->assertSame(0, DB::table('sessions')->where('user_id', $teller->id)->count());
        $this->assertTrue(AuditLog::where('aksi', 'user.reset_sandi')->where('subject_id', $teller->id)->exists());
        // Sandi tidak boleh bocor ke audit.
        $this->assertFalse(AuditLog::where('sesudah', 'like', '%'.$baru.'%')->exists());
    }

    public function test_akun_anggota_tidak_muncul_atau_dapat_diubah_di_manajemen_staf(): void
    {
        $owner = $this->staf(Role::Owner);
        $m = $this->anggota();

        $this->actingAs($owner)->get(route('panel.pengguna.index'))->assertDontSee($m->user->email);
        $this->actingAs($owner)->get(route('panel.pengguna.edit', $m->user))->assertNotFound();
        $this->actingAs($owner)->post(route('panel.pengguna.reset', $m->user))->assertNotFound();
    }

    // ---------- tampilan dashboard anggota ----------

    public function test_dashboard_anggota_belum_terverifikasi_diarahkan_ke_unggah_ktp(): void
    {
        $m = $this->anggota(null, 'calon');

        $this->actingAs($m->user)->get(route('panel'))->assertOk()->assertSee('belum terverifikasi')->assertSee('Unggah foto KTP');
    }
}
