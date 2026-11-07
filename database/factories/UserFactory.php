<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            // Acak per proses uji; uji yang perlu login memakai state dengan 'password' eksplisit.
            'password' => static::$password ??= Hash::make(Str::random(24)),
            'role' => Role::Member,
            'branch_id' => null,
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    public function peran(Role $role, ?int $branchId = null): static
    {
        return $this->state(fn () => ['role' => $role, 'branch_id' => $branchId]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }
}
