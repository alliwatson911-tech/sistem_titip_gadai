<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Kembalikan emas & perhiasan ke simulator publik (sempat disembunyikan seeder rebrand), setelah elektronik dan kendaraan. */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'is_client_featured')) {
            DB::table('products')->where('slug', 'gadai-emas-perhiasan')->update(['is_client_featured' => true, 'urutan' => 9]);
        }
    }

    public function down(): void {}
};
