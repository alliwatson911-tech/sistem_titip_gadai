<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan dukungan multi-agunan (elektronik, kendaraan, properti, dan surat berharga)
     * tanpa merusak skema tabel produk dan jaminan yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->string('kelompok_agunan', 30)->default('elektronik')->after('kategori_barang');
            $t->json('dokumen_wajib')->nullable()->after('deskripsi');
            $t->json('atribut_khusus')->nullable()->after('dokumen_wajib');
            $t->json('syarat_ketentuan')->nullable()->after('atribut_khusus');
            $t->text('proses_taksiran')->nullable()->after('syarat_ketentuan');
            $t->text('ketentuan_penyimpanan')->nullable()->after('proses_taksiran');
            $t->string('badge_label', 40)->nullable()->after('ketentuan_penyimpanan');
        });

        Schema::table('collaterals', function (Blueprint $t) {
            $t->string('kelompok_agunan', 30)->default('elektronik')->after('jenis');
            $t->json('atribut_data')->nullable()->after('kondisi');
            $t->json('dokumen_diterima')->nullable()->after('atribut_data');
            $t->timestamp('dokumen_dikembalikan_at')->nullable()->after('diterima_at');
            $t->string('diserahkan_kepada', 100)->nullable()->after('dokumen_dikembalikan_at');
            $t->foreignId('diserahkan_oleh')->nullable()->constrained('users')->nullOnDelete()->after('diserahkan_kepada');
            $t->text('catatan_serah_terima')->nullable()->after('diserahkan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('collaterals', function (Blueprint $t) {
            $t->dropForeign(['diserahkan_oleh']);
            $t->dropColumn([
                'kelompok_agunan',
                'atribut_data',
                'dokumen_diterima',
                'dokumen_dikembalikan_at',
                'diserahkan_kepada',
                'diserahkan_oleh',
                'catatan_serah_terima',
            ]);
        });

        Schema::table('products', function (Blueprint $t) {
            $t->dropColumn([
                'kelompok_agunan',
                'dokumen_wajib',
                'atribut_khusus',
                'syarat_ketentuan',
                'proses_taksiran',
                'ketentuan_penyimpanan',
                'badge_label',
            ]);
        });
    }
};

