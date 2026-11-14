<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\Auditable;
use App\Notifications\AturUlangSandi;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

// role, branch_id, dan status SENGAJA tidak mass-assignable: hanya diatur oleh kode tepercaya
// (seeder, modul manajemen pengguna Owner) agar tidak ada eskalasi hak akses lewat input form.
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Role $role
 * @property int|null $branch_id
 * @property string $status
 * @property bool $wajib_ganti_password
 * @property string|null $two_factor_secret
 * @property array|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $last_login_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Branch|null $branch
 * @property-read Member|null $member
 *
 * @method void saveQuietly(array $options = [])
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'wajib_ganti_password' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function duaFaktorAktif(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    /** Hash tetap (HMAC dengan kunci aplikasi) untuk kode pemulihan; kode asli tidak pernah disimpan. */
    public static function hashKodePemulihan(string $kode): string
    {
        return hash_hmac('sha256', strtolower(preg_replace('/[^a-z0-9]/i', '', $kode)), (string) config('app.key'));
    }

    /** Email atur ulang memakai notifikasi sendiri (bahasa Indonesia, tautan ke rute sandi.atur-ulang). */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AturUlangSandi($token));
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function hasPermission(string $izin): bool
    {
        // Jangan pakai config("permissions.$izin"): titik pada nama izin dibaca sebagai kunci bersarang.
        return $this->isActive()
            && in_array($this->role, config('permissions')[$izin] ?? [], true);
    }

    /** Daftar izin yang dimiliki pengguna ini (untuk tampilan dan uji). */
    public function permissions(): array
    {
        return array_keys(array_filter(
            config('permissions'),
            fn (array $roles) => in_array($this->role, $roles, true)
        ));
    }
}
