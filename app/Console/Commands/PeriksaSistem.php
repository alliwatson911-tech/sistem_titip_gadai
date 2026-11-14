<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Pemeriksa kesiapan: php artisan sistem:periksa. Keluar dengan kode 1 bila ada yang GAGAL. */
class PeriksaSistem extends Command
{
    protected $signature = 'sistem:periksa';

    protected $description = 'Periksa konfigurasi keamanan dan operasional (gunakan sebelum dan sesudah go-live)';

    public function handle(): int
    {
        $produksi = app()->environment('production');
        $hasil = [];
        $catat = function (string $nama, string $status, string $catatan = '') use (&$hasil) {
            $hasil[] = [$nama, $status, $catatan];
        };
        // Di produksi, peringatan keamanan dianggap GAGAL.
        $tegas = fn (bool $ok, string $nama, string $pesan) => $catat($nama, $ok ? 'OK' : ($produksi ? 'GAGAL' : 'PERINGATAN'), $ok ? '' : $pesan);

        $catat('Lingkungan', 'OK', app()->environment());
        $tegas(! config('app.debug') || ! $produksi, 'APP_DEBUG', 'APP_DEBUG=true membocorkan detail galat; matikan di produksi.');
        $tegas(filled(config('app.key')), 'APP_KEY', 'APP_KEY kosong (php artisan key:generate).');
        $tegas(strlen((string) config('linygadai.pii_hash_key')) >= 40, 'PII_HASH_KEY', 'Kunci hash NIK/IMEI kosong atau terlalu pendek. Jangan diganti setelah ada data.');
        $tegas(! str_contains((string) config('app.url'), 'localhost'), 'APP_URL', 'APP_URL masih localhost; tautan di email akan salah.');
        $tegas(config('mail.default') !== 'log', 'MAIL_MAILER', 'Email hanya ditulis ke log. Isi SMTP; tautan reset sandi tersimpan di berkas log.');
        $tegas((bool) config('session.secure') || ! $produksi, 'SESSION_SECURE_COOKIE', 'Cookie sesi belum bertanda secure. Set SESSION_SECURE_COOKIE=true di HTTPS.');
        $tegas(config('session.http_only') === true, 'Cookie HttpOnly', 'Cookie sesi dapat dibaca JavaScript.');

        try {
            DB::select('select 1');
            $konf = config('database.connections.'.config('database.default'));
            $catat('Koneksi database', 'OK', (string) ($konf['database'] ?? ''));
            $tegas(! (($konf['username'] ?? '') === 'root' && ($konf['password'] ?? '') === ''), 'Akun database', 'Memakai root tanpa kata sandi. Buat pengguna khusus dengan sandi kuat dan hak terbatas.');
            $trigger = DB::select("select count(*) n from information_schema.triggers where trigger_schema = ? and trigger_name like 'audit_logs_tolak_%'", [$konf['database']]);
            $tegas(($trigger[0]->n ?? 0) >= 2, 'Trigger audit', 'Trigger penolak UPDATE/DELETE audit_logs belum terpasang (jalankan migrasi).');
        } catch (\Throwable $e) {
            $catat('Koneksi database', 'GAGAL', 'Tidak dapat terhubung: '.$e->getMessage());
        }

        $tegas(is_writable(storage_path('app')) && is_writable(storage_path('logs')), 'Folder storage', 'storage/app atau storage/logs tidak dapat ditulis.');
        $tegas(! is_dir(public_path('storage')), 'Tautan storage publik', 'public/storage ada: berkas privat (KTP, foto) tidak boleh dapat diakses lewat web.');

        $cadangan = glob(storage_path('backups/linygadai-*.zip')) ?: [];
        $terbaru = $cadangan ? max(array_map('filemtime', $cadangan)) : 0;
        $tegas($terbaru > 0 && now()->timestamp - $terbaru < 172800, 'Cadangan', 'Tidak ada cadangan dalam 48 jam terakhir. Jalankan `php artisan sistem:cadangan` dan pastikan scheduler aktif; salin hasilnya ke lokasi lain.');
        $tegas(! config('linygadai.wajib_2fa') ? ! $produksi : true, '2FA staf', 'LINYGADAI_2FA_WAJIB=false: 2FA belum diwajibkan untuk Owner/Manager.');
        $tegas(! config('linygadai.mode_uji') || ! $produksi, 'Mode uji coba', 'LINYGADAI_MODE_UJI=true di produksi: spanduk data fiktif tampil. Matikan saat go-live.');

        $detak = Cache::get('scheduler:detak');
        $tegas($detak && now()->timestamp - (int) $detak < 300, 'Scheduler', 'Tidak terdeteksi berjalan dalam 5 menit terakhir. Jalankan `php artisan schedule:run` tiap menit (Task Scheduler/cron).');

        $this->table(['Pemeriksaan', 'Status', 'Catatan'], $hasil);
        $gagal = collect($hasil)->where(1, 'GAGAL')->count();
        $warn = collect($hasil)->where(1, 'PERINGATAN')->count();
        $this->line($gagal ? "<fg=red>$gagal pemeriksaan GAGAL.</>" : ($warn ? "<fg=yellow>Tidak ada yang gagal; $warn peringatan (wajib dibereskan sebelum produksi).</>" : '<fg=green>Semua pemeriksaan lolos.</>'));

        return $gagal ? self::FAILURE : self::SUCCESS;
    }
}
