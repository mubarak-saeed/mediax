<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** لغات المحتوى المتاحة. */
enum ContentLanguage: string implements HasLabel
{
    case Arabic = 'العربية';
    case English = 'English';
    case French = 'Français';
    case Turkish = 'Türkçe';
    case Spanish = 'Español';
    case German = 'Deutsch';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Arabic => 'العربية',
            self::English => 'English',
            self::French => 'Français',
            self::Turkish => 'Türkçe',
            self::Spanish => 'Español',
            self::German => 'Deutsch',
        };
    }
}
