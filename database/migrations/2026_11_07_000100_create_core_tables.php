<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inti: anggota, produk, kebijakan, pengajuan, jaminan, taksiran, persetujuan.
 * Uang selalu bigint rupiah. Persentase disimpan sebagai basis poin (bp): 1% = 100 bp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->string('nama_lengkap');
            $t->text('nik_enc');                    // NIK terenkripsi
            $t->char('nik_hash', 64)->unique();     // HMAC untuk pencarian dan cegah duplikat
            $t->date('tgl_lahir')->nullable();
            $t->string('alamat')->nullable();
            $t->string('telepon', 30)->nullable();
            $t->string('ktp_path')->nullable();     // disk privat
            $t->string('status', 20)->default('calon'); // calon|terverifikasi|ditolak|blokir
            $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable();
            $t->timestamps();
            $t->index(['branch_id', 'status']);
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete(); // null = semua cabang
            $t->string('nama');
            $t->string('slug')->unique();
            $t->string('kategori_barang', 30);      // smartphone|laptop|kamera|televisi|smartwatch|konsol
            $t->text('deskripsi')->nullable();
            $t->unsignedBigInteger('pinjaman_min');
            $t->unsignedBigInteger('pinjaman_max');
            $t->unsignedSmallInteger('ltv_max_bp');            // 8000 = 80%
            $t->unsignedSmallInteger('tenor_hari');
            $t->unsignedSmallInteger('sewa_modal_per_hari_bp'); // 15 = 0,15% per hari
            $t->unsignedSmallInteger('blok_hari')->default(15);
            $t->unsignedSmallInteger('admin_bp');                // 100 = 1%
            $t->unsignedBigInteger('admin_min');
            $t->unsignedBigInteger('admin_max');
            $t->unsignedSmallInteger('urutan')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['is_active', 'urutan']);
        });

        Schema::create('policy_settings', function (Blueprint $t) {
            $t->id();
            $t->string('key', 60);
            $t->json('value');
            $t->date('berlaku_mulai');
            $t->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['key', 'berlaku_mulai']);
        });

        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 20)->unique();
            $t->foreignId('member_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedBigInteger('pinjaman_diminta');
            $t->unsignedSmallInteger('tenor_hari');
            $t->string('status', 30)->default('draft');
            $t->text('catatan_member')->nullable();
            $t->timestamp('diajukan_at')->nullable();
            $t->date('batas_bawa_barang')->nullable();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->timestamps();
            $t->index(['branch_id', 'status']);
        });

        Schema::create('collaterals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('application_id')->constrained()->restrictOnDelete();
            $t->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $t->string('jenis', 30);
            $t->string('merek', 60);
            $t->string('model', 100);
            $t->text('imei_serial_enc')->nullable();
            $t->char('imei_serial_hash', 64)->nullable()->index(); // deteksi barang ganda
            $t->string('kelengkapan')->nullable();
            $t->text('kondisi')->nullable();
            $t->string('no_segel', 40)->nullable();
            $t->string('lokasi_simpan', 60)->nullable();
            $t->foreignId('diterima_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('diterima_at')->nullable();
            $t->string('status', 20)->default('diterima'); // diterima|tersimpan|dikeluarkan|dilelang
            $t->timestamps();
            $t->index(['branch_id', 'status']);
        });

        Schema::create('collateral_photos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('collateral_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->string('jenis', 20); // depan|belakang|serial|segel
            $t->timestamps();
        });

        Schema::create('appraisals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('application_id')->constrained()->restrictOnDelete();
            $t->foreignId('analis_id')->constrained('users')->restrictOnDelete();
            $t->string('tipe', 10)->default('awal'); // awal|ulang
            $t->unsignedBigInteger('nilai_pasar');
            $t->unsignedBigInteger('nilai_taksiran');
            $t->unsignedSmallInteger('ltv_diterapkan_bp');
            $t->unsignedBigInteger('plafon_maks');
            $t->unsignedTinyInteger('skor_kelayakan'); // 0-100
            $t->string('rekomendasi', 10);             // setuju|tolak|revisi
            $t->text('catatan')->nullable();
            $t->boolean('is_current')->default(true);
            $t->timestamps();
            $t->index(['application_id', 'is_current']);
        });

        Schema::create('approvals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('application_id')->constrained()->restrictOnDelete();
            $t->string('level', 10);        // analis|manager|owner
            $t->string('keputusan', 10);    // setuju|tolak
            $t->foreignId('oleh')->constrained('users')->restrictOnDelete();
            $t->text('catatan')->nullable();
            $t->timestamp('decided_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['approvals', 'appraisals', 'collateral_photos', 'collaterals', 'applications', 'policy_settings', 'products', 'members'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
