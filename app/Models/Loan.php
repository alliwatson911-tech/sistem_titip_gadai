<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use Auditable, BelongsToBranch;

    public ?string $memberColumn = 'member_id';

    // Saldo, jatuh tempo, dan status hanya diubah oleh LoanService.
    protected $guarded = ['id', 'status', 'pokok_awal', 'pokok_saldo', 'plafon_ulang', 'jatuh_tempo', 'tenggang_sampai', 'perpanjangan_ke'];

    protected function casts(): array
    {
        return [
            'tgl_akad' => 'date',
            'jatuh_tempo' => 'date',
            'tenggang_sampai' => 'date',
            'snapshot_syarat' => 'array',
            'status' => LoanStatus::class,
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

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    // Relasi di bawah memakai withoutGlobalScopes: akses ke Loan sudah dibatasi cabang/pemilik oleh scope Loan itu sendiri.
    public function payments()
    {
        return $this->hasMany(Payment::class)->withoutGlobalScopes();
    }

    public function disbursement()
    {
        return $this->hasOne(Disbursement::class)->withoutGlobalScopes();
    }

    public function histories()
    {
        return $this->hasMany(LoanStatusHistory::class);
    }

    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    public function notices()
    {
        return $this->hasMany(LoanNotice::class);
    }

    public function redemption()
    {
        return $this->hasOne(Redemption::class);
    }

    public function collaterals()
    {
        return $this->hasMany(Collateral::class, 'application_id', 'application_id')->withoutGlobalScopes();
    }
}
