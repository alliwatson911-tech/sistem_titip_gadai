<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected $hidden = ['nik_penebus_enc'];

    protected function casts(): array
    {
        return ['nik_penebus_enc' => 'encrypted', 'dikeluarkan_at' => 'datetime', 'nota_asli_diperiksa' => 'boolean'];
    }
}
