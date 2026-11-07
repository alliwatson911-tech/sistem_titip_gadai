<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Tabel akad gadai, pembayaran, tebus, lelang, riwayat status, audit, dan konten situs. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $t) {
            $t->id();
            $t->string('no_sbg', 30)->unique();     // nomor nota gadai
            $t->foreignId('application_id')->unique()->constrained()->restrictOnDelete();
            $t->foreignId('member_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedBigInteger('pokok_awal');
            $t->unsignedBigInteger('pokok_saldo');
            $t->date('tgl_akad');
            $t->date('jatuh_tempo');
            $t->date('tenggang_sampai')->nullable();
            $t->unsignedTinyInteger('perpanjangan_ke')->default(0);
            $t->json('snapshot_syarat');            // tarif saat akad
            $t->string('status', 20)->default('aktif'); // aktif|tenggang|siap_lelang|dilelang|ditebus|selesai_lelang
            $t->timestamps();
            $t->index(['branch_id', 'status', 'jatuh_tempo']);
        });
        DB::statement('ALTER TABLE loans ADD CONSTRAINT chk_loans_pokok CHECK (pokok_saldo <= pokok_awal)');

        Schema::create('disbursements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->unsignedBigInteger('jumlah');
            $t->string('metode', 10);               // tunai|transfer
            $t->string('referensi')->nullable();
            $t->foreignId('teller_id')->constrained('users')->restrictOnDelete();
            $t->timestamp('dicairkan_at');
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->string('no_kwitansi', 30)->unique();
            $t->foreignId('loan_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->string('tipe', 12);                 // perpanjangan|cicil|tebus
            $t->unsignedBigInteger('sewa_modal')->default(0);
            $t->unsignedBigInteger('administrasi')->default(0);
            $t->unsignedBigInteger('denda')->default(0);
            $t->unsignedBigInteger('pokok_dibayar')->default(0);
            $t->unsignedBigInteger('total');
            $t->string('metode', 10);               // tunai|transfer
            $t->string('bukti_path')->nullable();
            $t->string('status', 20)->default('menunggu_verifikasi'); // menunggu_verifikasi|sah|batal
            $t->foreignId('teller_id')->constrained('users')->restrictOnDelete();
            $t->timestamp('dibayar_at');
            $t->timestamps();
            $t->index(['branch_id', 'dibayar_at']);
        });

        Schema::create('loan_extensions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained()->restrictOnDelete();
            $t->foreignId('payment_id')->unique()->constrained()->restrictOnDelete();
            $t->date('jatuh_tempo_lama');
            $t->date('jatuh_tempo_baru');
            $t->unsignedTinyInteger('ke');
            $t->timestamps();
        });

        Schema::create('redemptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->unique()->constrained()->restrictOnDelete();
            $t->foreignId('payment_id')->constrained()->restrictOnDelete();
            $t->string('penebus_tipe', 10);         // penggadai|kuasa
            $t->string('nama_penebus');
            $t->text('nik_penebus_enc')->nullable();
            $t->string('surat_kuasa_path')->nullable();
            $t->boolean('nota_asli_diperiksa')->default(false);
            $t->foreignId('dikeluarkan_oleh')->constrained('users')->restrictOnDelete();
            $t->timestamp('dikeluarkan_at');
            $t->timestamps();
        });

        Schema::create('auctions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->unique()->constrained()->restrictOnDelete();
            $t->foreignId('collateral_id')->constrained()->restrictOnDelete();
            $t->string('status', 15)->default('dijadwalkan'); // dijadwalkan|laku|tidak_laku
            $t->timestamp('diberitahukan_at')->nullable();
            $t->date('tgl_lelang')->nullable();
            $t->unsignedBigInteger('harga_limit')->nullable();
            $t->unsignedBigInteger('harga_laku')->nullable();
            $t->unsignedBigInteger('biaya_lelang')->default(0);
            $t->bigInteger('kelebihan')->default(0);
            $t->string('kelebihan_status', 12)->default('belum'); // belum|dibayarkan
            $t->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('loan_status_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $t->string('dari', 20)->nullable();
            $t->string('ke', 20);
            $t->foreignId('oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->string('alasan')->nullable();
            $t->timestamps();
        });

        // Audit log: hanya ditambah, tidak pernah diubah. Tanpa updated_at.
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $t->string('aksi', 60);
            $t->string('subject_type', 60)->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->json('sebelum')->nullable();
            $t->json('sesudah')->nullable();
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['subject_type', 'subject_id']);
            $t->index(['user_id', 'created_at']);
            $t->index(['branch_id', 'created_at']);
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $t->string('judul');
            $t->text('isi');
            $t->date('mulai')->nullable();
            $t->date('selesai')->nullable();
            $t->boolean('is_published')->default(false);
            $t->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->softDeletes();
            $t->index(['is_published', 'mulai']);
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $t->string('pertanyaan');
            $t->text('jawaban');
            $t->unsignedSmallInteger('urutan')->default(0);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
            $t->softDeletes();
            $t->index(['is_published', 'urutan']);
        });

        Schema::create('site_contents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $t->string('kunci', 60);
            $t->longText('nilai')->nullable();
            $t->timestamps();
            $t->unique(['branch_id', 'kunci']);
        });
    }

    public function down(): void
    {
        foreach (['site_contents', 'faqs', 'announcements', 'audit_logs', 'loan_status_histories', 'auctions', 'redemptions', 'loan_extensions', 'payments', 'disbursements', 'loans'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
