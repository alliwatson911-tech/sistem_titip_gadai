<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['tgl_lelang' => 'date', 'diberitahukan_at' => 'datetime'];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
