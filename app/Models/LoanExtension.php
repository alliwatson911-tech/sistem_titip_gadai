<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanExtension extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['jatuh_tempo_lama' => 'date', 'jatuh_tempo_baru' => 'date'];
    }
}
