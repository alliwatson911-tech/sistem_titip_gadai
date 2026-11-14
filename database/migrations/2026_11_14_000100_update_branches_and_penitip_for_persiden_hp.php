<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Perluas kolom tabel branches untuk data cabang PERSIDEN HP
        Schema::table('branches', function (Blueprint $table) {
            $table->string('nama_tampilan')->nullable()->after('nama');
            $table->text('gmaps_link')->nullable()->after('alamat');
            $table->string('instagram', 100)->nullable()->after('whatsapp');
            $table->text('medsos_lain')->nullable()->after('instagram');
            $table->string('foto_path')->nullable()->after('medsos_lain');
            $table->boolean('is_verified')->default(false)->after('jam_buka');
            $table->text('catatan_verifikasi')->nullable()->after('is_verified');
        });

        // 2. Tambah kolom wajib_ganti_password pada users untuk akun baru oleh staf
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('wajib_ganti_password')->default(false)->after('status');
        });

        // 3. Tambah kolom is_client_featured pada products untuk menandai 8 kategori pilihan klien
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_client_featured')->default(true)->after('is_active');
            $table->unsignedSmallInteger('durasi_opsi_minggu')->default(1)->after('tenor_hari');
        });

        // 4. Buat tabel antrean notifikasi penitip (email & WhatsApp)
        Schema::create('penitip_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('channel', 20); // 'email' | 'whatsapp'
            $table->string('tujuan');      // alamat email atau nomor WA
            $table->string('subjek')->nullable();
            $table->text('pesan');
            $table->string('status', 20)->default('antrean'); // 'antrean' | 'terkirim' | 'gagal'
            $table->text('response_payload')->nullable();
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penitip_notifications');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_client_featured', 'durasi_opsi_minggu']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wajib_ganti_password');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'nama_tampilan',
                'gmaps_link',
                'instagram',
                'medsos_lain',
                'foto_path',
                'is_verified',
                'catatan_verifikasi',
            ]);
        });
    }
};
