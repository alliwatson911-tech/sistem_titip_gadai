<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_current' => 'boolean'];
    }

    public function analis()
    {
        return $this->belongsTo(User::class, 'analis_id');
    }
}
