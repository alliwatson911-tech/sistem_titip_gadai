<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanNotice extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public const JENIS = [
        'h7' => 'Pengingat jatuh tempo (≤ 7 hari)',
        'h1' => 'Pengingat jatuh tempo (besok/hari ini)',
        'tenggang' => 'Masuk masa tenggang',
        'siap_lelang' => 'Pemberitahuan proses lelang',
        'jadwal_lelang' => 'Jadwal lelang',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
