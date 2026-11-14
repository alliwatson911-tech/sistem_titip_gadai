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
            'is_client_featured' => 'boolean',
            'dokumen_wajib' => 'array',
            'atribut_khusus' => 'array',
            'syarat_ketentuan' => 'array',
        ];
    }

    /** Scope produk pilihan klien PERSIDEN HP (8 kategori resmi) */
    public function scopeClientFeatured($query)
    {
        return $query->where('is_active', true)->where('is_client_featured', true);
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
