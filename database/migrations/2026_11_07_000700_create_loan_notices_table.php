<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris per (gadai, jenis, kunci, kanal): bukti bahwa pemberitahuan sudah dikirim dan pencegah pengiriman ganda.
        Schema::create('loan_notices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained()->restrictOnDelete();
            $t->string('jenis', 20);        // h7|h1|tenggang|siap_lelang|jadwal_lelang
            $t->string('kunci', 20);        // tanggal yang menjadi dasar (jatuh tempo / tanggal pemberitahuan / tanggal lelang)
            $t->string('channel', 10)->default('email');
            $t->string('status', 10);       // terkirim|gagal|dilewati
            $t->unsignedTinyInteger('percobaan')->default(0);
            $t->string('catatan')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->timestamps();
            $t->unique(['loan_id', 'jenis', 'kunci', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_notices');
    }
};
