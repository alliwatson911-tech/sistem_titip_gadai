<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $t) {
            // faq = tanya-jawab; informasi = keterangan umum (jam layanan, biaya, dll.), keduanya tampil sebagai accordion.
            $t->string('kelompok', 10)->default('faq')->after('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', fn (Blueprint $t) => $t->dropColumn('kelompok'));
    }
};
