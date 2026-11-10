<?php

use App\Models\Branch;
use App\Models\Product;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Pastikan data awal sistem publik (cabang, kebijakan, produk layanan, konten, dan FAQ)
     * otomatis tersedia saat deployment dan migrasi pertama kali dijalankan.
     */
    public function up(): void
    {
        if (Branch::count() === 0) {
            (new BranchSeeder)->run();
        }

        if (DB::table('policy_settings')->count() === 0) {
            (new PolicySeeder)->run();
        }

        if (Product::count() === 0) {
            (new ProductSeeder)->run();
        }

        if (DB::table('site_contents')->count() === 0) {
            (new ContentSeeder)->run();
        }
    }

    public function down(): void
    {
        // Data operasional dasar tidak dihapus saat rollback
    }
};

