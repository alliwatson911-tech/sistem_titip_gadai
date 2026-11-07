<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use App\Support\Pii;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        $nik = fake()->unique()->numerify('################');

        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'nama_lengkap' => fake()->name(),
            'nik_enc' => $nik,
            'nik_hash' => Pii::hash($nik),
            'telepon' => '08'.fake()->numerify('##########'),
        ];
    }
}
