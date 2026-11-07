<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Auditable, BelongsToBranch;

    // Dibuat hanya oleh LoanService.
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['dibayar_at' => 'datetime', 'diverifikasi_at' => 'datetime', 'sebelum' => 'array'];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class)->withoutGlobalScopes();
    }

    public function teller()
    {
        return $this->belongsTo(User::class, 'teller_id');
    }
}
