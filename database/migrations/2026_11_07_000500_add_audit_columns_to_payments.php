<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $t) {
            $t->json('sebelum')->nullable()->after('status');           // keadaan gadai sebelum pembayaran (untuk pembalikan)
            $t->foreignId('diverifikasi_oleh')->nullable()->after('teller_id')->constrained('users')->nullOnDelete();
            $t->timestamp('diverifikasi_at')->nullable()->after('diverifikasi_oleh');
            $t->string('alasan')->nullable()->after('diverifikasi_at');  // alasan tolak/batal
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $t) {
            $t->dropConstrainedForeignId('diverifikasi_oleh');
            $t->dropColumn(['sebelum', 'diverifikasi_at', 'alasan']);
        });
    }
};
