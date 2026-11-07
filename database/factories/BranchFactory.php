<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode' => strtoupper(fake()->unique()->lexify('??')),
            'nama' => 'Cabang '.fake()->unique()->word(),
            'is_active' => true,
        ];
    }
}
