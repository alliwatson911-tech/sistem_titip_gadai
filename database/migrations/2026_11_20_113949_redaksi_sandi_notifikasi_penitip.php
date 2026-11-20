<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Samarkan sandi sementara yang terlanjur tersimpan di antrean notifikasi penitip. */
    public function up(): void
    {
        DB::table('penitip_notifications')->where('pesan', 'like', '%Sandi Sementara:%')->orderBy('id')->chunkById(100, function ($baris) {
            foreach ($baris as $n) {
                DB::table('penitip_notifications')->where('id', $n->id)->update([
                    'pesan' => preg_replace('/(Kata Sandi Sementara:\s*)\S+/', '$1[disembunyikan]', $n->pesan),
                ]);
            }
        });
    }

    /** Tidak dapat dibalikkan: sandi sengaja tidak disimpan lagi. */
    public function down(): void {}
};
