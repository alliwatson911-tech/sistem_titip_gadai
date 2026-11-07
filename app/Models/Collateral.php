<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    use Auditable, BelongsToBranch;

    protected $table = 'collaterals';

    // Anggota tidak punya akses langsung ke barang jaminan (hanya ringkasan lewat pengajuan).
    protected $guarded = ['id', 'status'];

    protected $hidden = ['imei_serial_enc', 'imei_serial_hash'];

    protected function casts(): array
    {
        return ['imei_serial_enc' => 'encrypted', 'diterima_at' => 'datetime'];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function photos()
    {
        return $this->hasMany(CollateralPhoto::class);
    }
}
