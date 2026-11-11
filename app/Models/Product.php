<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'dokumen_wajib' => 'array',
            'atribut_khusus' => 'array',
            'syarat_ketentuan' => 'array',
        ];
    }

    public function labelKelompok(): string
    {
        return match ($this->kelompok_agunan) {
            'emas' => 'Emas & Logam Mulia',
            'kendaraan' => 'Kendaraan Bermotor (BPKB)',
            'properti' => 'Sertifikat Properti',
            'berharga' => 'Barang & Surat Berharga',
            default => 'Barang Elektronik',
        };
    }
}
