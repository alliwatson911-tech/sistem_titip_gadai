<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $nama_tampilan
 * @property string|null $alamat
 * @property string|null $telepon
 * @property string|null $whatsapp
 * @property string|null $email
 * @property string|null $gmaps_link
 * @property string|null $instagram
 * @property array|null $medsos_lain
 * @property string|null $foto_path
 * @property string|null $jam_buka
 * @property string|null $lat
 * @property string|null $lng
 * @property bool $is_verified
 * @property string|null $catatan_verifikasi
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $nama_publik
 * @property-read string $jam_layanan
 */
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
