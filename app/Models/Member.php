<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use Auditable, BelongsToBranch, HasFactory;

    /** Kolom pemilik untuk isolasi Member (lihat BelongsToBranch). */
    public ?string $memberColumn = 'user_id';

    protected $guarded = ['id', 'status', 'verified_by', 'verified_at'];

    protected $hidden = ['nik_enc', 'nik_hash', 'ktp_path'];

    protected function casts(): array
    {
        return [
            'nik_enc' => 'encrypted',
            'tgl_lahir' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** NIK tersamar untuk tampilan: 7371••••••••0001 */
    public function nikTersamar(): string
    {
        $nik = (string) $this->nik_enc;

        return strlen($nik) >= 8 ? substr($nik, 0, 4).str_repeat('•', max(strlen($nik) - 8, 0)).substr($nik, -4) : '••••';
    }
}
