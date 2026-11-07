<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollateralPhoto extends Model
{
    protected $guarded = ['id'];

    public function collateral()
    {
        return $this->belongsTo(Collateral::class);
    }
}
