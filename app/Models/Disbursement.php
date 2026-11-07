<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use Auditable, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['dicairkan_at' => 'datetime'];
    }
}
