<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            // Pokok yang disetujui = min(pinjaman diminta, plafon hasil taksiran). Dipakai pencairan di Tahap 4.
            $t->unsignedBigInteger('pinjaman_disetujui')->nullable()->after('pinjaman_diminta');
        });
    }

    public function down(): void
    {
        Schema::table('applications', fn (Blueprint $t) => $t->dropColumn('pinjaman_disetujui'));
    }
};
