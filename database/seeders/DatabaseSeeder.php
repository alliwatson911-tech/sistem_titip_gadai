<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            PolicySeeder::class,
            ProductSeeder::class,
            ContentSeeder::class,
            UserSeeder::class,
            DemoSeeder::class, // hanya berjalan bila LINYGADAI_MODE_UJI=true
        ]);
    }
}
