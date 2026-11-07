<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    // Panel: dibatasi cabang (Owner melihat semua). Halaman publik memakai withoutGlobalScopes().
    use Auditable, BelongsToBranch, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['mulai' => 'date', 'selesai' => 'date', 'is_published' => 'boolean'];
    }

    /** Pengumuman yang boleh tampil di situs publik hari ini. */
    public function scopeTayang(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->where(fn ($w) => $w->whereNull('mulai')->orWhere('mulai', '<=', today()))
            ->where(fn ($w) => $w->whereNull('selesai')->orWhere('selesai', '>=', today()))
            ->orderByRaw('mulai IS NULL')->orderByDesc('mulai')->orderByDesc('id');
    }
}
