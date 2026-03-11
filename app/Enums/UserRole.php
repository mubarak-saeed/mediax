<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** دور المستخدم في المنصة. */
enum UserRole: string implements HasLabel
{
    case Admin = 'admin';
    case User = 'user';
    case SuperAdmin = 'superadmin';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'مدير',
            self::User => 'مستخدم',
            self::SuperAdmin => 'مدير النظام',
        };
    }
}
