<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** العملات المتاحة للخطط. */
enum Currency: string implements HasLabel
{
    case Usd = 'usd';
    case Sar = 'sar';
    case Aed = 'aed';
    case Egp = 'egp';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Usd => 'دولار أمريكي ($)',
            self::Sar => 'ريال سعودي (﷼)',
            self::Aed => 'درهم إماراتي (د.إ)',
            self::Egp => 'جنيه مصري (ج.م)',
        };
    }
}
