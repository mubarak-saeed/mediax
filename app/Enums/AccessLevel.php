<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** مستوى الوصول للمحتوى: مجاني، أساسي، برو، أو بريميوم. */
enum AccessLevel: string implements HasLabel
{
    case Free = 'free';
    case Basic = 'basic';
    case Pro = 'pro';
    case Premium = 'premium';

    /** ترتيب المستوى (لللمقارنة: الأعلى = وصول أكثر). */
    public function order(): int
    {
        return match ($this) {
            self::Free => 0,
            self::Basic => 1,
            self::Pro => 2,
            self::Premium => 3,
        };
    }

    /** هل هذا المستوى يسمح بالوصول لمحتوى يتطلب المستوى المطلوب؟ */
    public function allowsAccessTo(AccessLevel $required): bool
    {
        return $this->order() >= $required->order();
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Free => 'مجاني',
            self::Basic => 'مدفوع',
            self::Pro => 'برو',
            self::Premium => 'بريميوم',
        };
    }
}
