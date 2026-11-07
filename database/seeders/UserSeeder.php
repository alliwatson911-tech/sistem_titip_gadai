<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use App\Support\Pii;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Satu akun per peran per cabang + Owner + satu anggota contoh per cabang.
 * Kata sandi DIACAK dan hanya dicetak sekali ke konsol; tidak ada kredensial di kode.
 * Menjalankan ulang seeder tidak mengubah kata sandi akun yang sudah ada.
 * Email memakai domain .test (cadangan, tidak pernah terkirim). NIK anggota contoh palsu.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = Branch::whereIn('kode', ['A', 'B'])->get()->keyBy('kode');
        $baris = [];

        $akun = [['Owner linygadai', 'owner@linygadai.test', Role::Owner, null]];
        foreach (['A', 'B'] as $k) {
            foreach ([Role::Manager, Role::Admin, Role::Teller, Role::Analis] as $role) {
                $akun[] = [
                    $role->label().' '.$k,
                    strtolower($role->value).'.'.strtolower($k).'@linygadai.test',
                    $role,
                    $cabang[$k]->id,
                ];
            }
        }

        foreach ($akun as [$nama, $email, $role, $branchId]) {
            $baris[] = $this->buat($nama, $email, $role, $branchId);
        }

        foreach (['A', 'B'] as $i => $k) {
            $email = 'anggota.'.strtolower($k).'@linygadai.test';
            $hasil = $this->buat('Anggota Contoh '.$k, $email, Role::Member, null);
            $baris[] = $hasil;

            $user = User::where('email', $email)->first();
            $nik = '73710000000000'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT); // NIK palsu
            if (! Member::withoutGlobalScopes()->where('user_id', $user->id)->exists()) {
                $m = new Member([
                    'user_id' => $user->id,
                    'branch_id' => $cabang[$k]->id,
                    'nama_lengkap' => 'Anggota Contoh '.$k,
                    'nik_enc' => $nik,
                    'nik_hash' => Pii::hash($nik),
                    'telepon' => '081200000'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                ]);
                $m->forceFill(['status' => 'terverifikasi', 'verified_at' => now()])->save();
            }
        }

        $this->command?->warn('Catat kata sandi di bawah sekarang. Tidak disimpan di mana pun dan tidak ditampilkan lagi.');
        $this->command?->table(['Email', 'Peran', 'Kata sandi'], $baris);
    }

    private function buat(string $nama, string $email, Role $role, ?int $branchId): array
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return [$email, $role->value, '(sudah ada, tidak diubah)'];
        }

        $sandi = Str::password(16, symbols: false);
        $user = new User(['name' => $nama, 'email' => $email, 'password' => $sandi]);
        $user->forceFill(['role' => $role, 'branch_id' => $branchId, 'status' => 'aktif', 'email_verified_at' => now()])->save();

        return [$email, $role->value, $sandi];
    }
}
