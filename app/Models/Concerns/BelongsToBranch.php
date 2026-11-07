<?php

namespace App\Models\Concerns;

use App\Enums\Role;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Isolasi data per cabang dan per anggota.
 *  - Owner: semua cabang.
 *  - Staf cabang: hanya baris dengan branch_id miliknya.
 *  - Member: hanya baris miliknya; kolom pemilik dideklarasikan lewat $memberColumn
 *    ('user_id' untuk tabel members, 'member_id' untuk tabel lain). Tanpa deklarasi: tidak ada akses.
 *  - Tanpa pengguna login (konsol, seeder, pendaftaran publik): tidak difilter.
 * Gunakan Model::withoutGlobalScopes() hanya pada kode tepercaya (scheduler, laporan konsolidasi Owner).
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope('cabang', function (Builder $q) {
            $user = Auth::user();
            if (! $user) {
                return;
            }
            $table = $q->getModel()->getTable();

            if ($user->role === Role::Owner) {
                return;
            }

            if ($user->role === Role::Member) {
                $kolom = $q->getModel()->memberColumn ?? null;
                if ($kolom === 'user_id') {
                    $q->where("$table.user_id", $user->id);
                } elseif ($kolom) {
                    $q->whereIn("$table.$kolom", DB::table('members')->select('id')->where('user_id', $user->id));
                } else {
                    $q->whereRaw('1 = 0');
                }

                return;
            }

            // Staf cabang. Tanpa cabang = tanpa akses (gagal aman).
            $user->branch_id
                ? $q->where("$table.branch_id", $user->branch_id)
                : $q->whereRaw('1 = 0');
        });

        // Baris baru otomatis mewarisi cabang staf bila belum diisi.
        static::creating(function ($model) {
            $user = Auth::user();
            if ($user && $user->branch_id && empty($model->branch_id)) {
                $model->branch_id = $user->branch_id;
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
