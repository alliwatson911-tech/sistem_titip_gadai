<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use Auditable, BelongsToBranch;

    public ?string $memberColumn = 'member_id';

    // Status dan pinjaman disetujui hanya berubah lewat ApplicationFlow, bukan mass assignment.
    protected $guarded = ['id', 'status', 'pinjaman_disetujui'];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'diajukan_at' => 'datetime',
            'batas_bawa_barang' => 'date',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function collaterals()
    {
        return $this->hasMany(Collateral::class);
    }

    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function taksiranSaatIni(): ?Appraisal
    {
        return $this->appraisals()->where('is_current', true)->latest('id')->first();
    }
}
