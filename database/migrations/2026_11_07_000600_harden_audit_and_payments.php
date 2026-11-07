<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Penguatan di level database (tidak bergantung pada kode aplikasi):
 *  1) audit_logs hanya boleh ditambah: UPDATE dan DELETE ditolak oleh trigger.
 *  2) payments: total harus sama dengan jumlah komponennya.
 * Catatan: hanya MySQL/MariaDB. Aksi referensial FK (ON DELETE SET NULL) tidak mengaktifkan trigger.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("CREATE TRIGGER audit_logs_tolak_ubah BEFORE UPDATE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs bersifat tambah-saja: UPDATE ditolak'");
        DB::statement("CREATE TRIGGER audit_logs_tolak_hapus BEFORE DELETE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs bersifat tambah-saja: DELETE ditolak'");
        DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payments_total CHECK (total = pokok_dibayar + sewa_modal + administrasi + denda)');
    }

    public function down(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS audit_logs_tolak_ubah');
        DB::statement('DROP TRIGGER IF EXISTS audit_logs_tolak_hapus');
        DB::statement('ALTER TABLE payments DROP CONSTRAINT chk_payments_total');
    }
};
