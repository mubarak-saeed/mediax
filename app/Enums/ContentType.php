<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** نوع المحتوى: فيلم، مسلسل، أو كتاب. */
enum ContentType: string implements HasColor, HasLabel
{
    case Movie = 'movie';
    case Series = 'series';
    case Book = 'book';
    case MovieSerial = 'movieserial';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Movie => 'فيلم',
            self::Series => 'مسلسل',
            self::Book => 'كتاب',
            self::MovieSerial => 'فيلم متسلسل',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Movie => 'info',
            self::Series => 'success',
            self::Book => 'warning',
            self::MovieSerial => 'gray',
        };
    }
}
