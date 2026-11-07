<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->text('two_factor_secret')->nullable()->after('remember_token');          // terenkripsi
            $t->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret'); // terenkripsi, JSON hash
            $t->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $t->unsignedBigInteger('two_factor_last_step')->nullable()->after('two_factor_confirmed_at'); // cegah pemakaian ulang kode
        });

        Schema::table('loans', function (Blueprint $t) {
            // Plafon pinjaman hasil taksir ulang terakhir. Bila saldo pokok > plafon_ulang, anggota wajib menyetor selisihnya (lewat cicil) sebelum memperpanjang.
            $t->unsignedBigInteger('plafon_ulang')->nullable()->after('pokok_saldo');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'two_factor_last_step']));
        Schema::table('loans', fn (Blueprint $t) => $t->dropColumn('plafon_ulang'));
    }
};
