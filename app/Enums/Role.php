<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Manager = 'manager';
    case Admin = 'admin';
    case Teller = 'teller';
    case Analis = 'analis';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Manager => 'Manager Cabang',
            self::Admin => 'Admin Cabang',
            self::Teller => 'Teller',
            self::Analis => 'Analis',
            self::Member => 'Anggota',
        };
    }

    /** Peran staf yang wajib terikat pada satu cabang. */
    public function isBranchBound(): bool
    {
        return in_array($this, [self::Manager, self::Admin, self::Teller, self::Analis], true);
    }

    public function isStaff(): bool
    {
        return $this !== self::Member;
    }
}
