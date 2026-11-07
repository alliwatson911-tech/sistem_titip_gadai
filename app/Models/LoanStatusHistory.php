<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanStatusHistory extends Model
{
    protected $guarded = ['id'];

    public function pelaku()
    {
        return $this->belongsTo(User::class, 'oleh');
    }
}
