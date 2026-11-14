<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use Auditable, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'medsos_lain' => 'array',
        ];
    }

    /** Nama yang ditampilkan ke publik */
    public function getNamaPublikAttribute(): string
    {
        return $this->nama_tampilan ?: $this->nama;
    }

    /** Jam operasional standar cabang PERSIDEN HP (08.00 - 22.00 WITA jika kosong) */
    public function getJamLayananAttribute(): string
    {
        return $this->jam_buka ?: '08.00 - 22.00 WITA';
    }
}
