<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** حالة الدفعة. */
enum PaymentStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Completed => 'مكتمل',
            self::Failed => 'فشل',
        };
    }
}
