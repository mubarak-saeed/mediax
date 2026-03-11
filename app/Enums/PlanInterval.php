<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** فترات الاشتراك. */
enum PlanInterval: string implements HasLabel
{
    case Month = 'month';
    case Year = 'year';
    case Week = 'week';
    case Day = 'day';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Month => 'شهري',
            self::Year => 'سنوي',
            self::Week => 'أسبوعي',
            self::Day => 'يومي',
        };
    }
}
