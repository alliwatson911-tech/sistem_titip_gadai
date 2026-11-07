<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/** Jejak audit: hanya boleh ditambah. Ubah/hapus ditolak di level model. */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['sebelum' => 'array', 'sesudah' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit log tidak boleh diubah.'));
        static::deleting(fn () => throw new LogicException('Audit log tidak boleh dihapus.'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
