<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Services\Situs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
            'contoh_taksiran' => 'array',
            'syarat_ketentuan' => 'array',
        ];
    }

    /** @return array<string, mixed> data satu produk untuk kalkulator simulasi publik */
    public function dataKalkulator(): array
    {
        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'nama' => $this->nama,
            'ltvMaxBp' => $this->ltv_max_bp,
            'min' => $this->pinjaman_min,
            'max' => $this->pinjaman_max,
            'tenor' => $this->tenor_hari,
            'contoh' => $this->contohSimulasi(),
            'contohTgl' => $this->updated_at?->toDateString(),
        ];

        if ($this->kategori_barang === 'emas') {
            $data['emas'] = Situs::hargaEmas();
        }

        return $data;
    }

    /**
     * Contoh barang beserta kisaran taksiran untuk chip simulasi (ASUMSI indikatif, bukan harga pasar resmi).
     *
     * @return list<array{label: string, min: int, max: int}>
     */
    public function contohSimulasi(): array
    {
        return collect((array) $this->contoh_taksiran)
            ->filter(fn ($c) => isset($c['label'], $c['min'], $c['max']))
            ->map(fn ($c) => ['label' => $c['label'], 'min' => (int) $c['min'], 'max' => (int) $c['max']])
            ->values()
            ->all();
    }

    /** Scope produk pilihan klien PERSIDEN HP (8 kategori resmi) */
    public function scopeClientFeatured($query)
    {
        $query->where('is_active', true);

        try {
            if (Schema::hasColumn('products', 'is_client_featured')) {
                $query->where('is_client_featured', true);
            }
        } catch (\Throwable) {
            // Jika skema belum termigrasi pada host deployment, gunakan is_active saja
        }

        return $query;
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
