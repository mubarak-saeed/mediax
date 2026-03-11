<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

/**
 * يوفر poster_url (القيمة الأصلية للمسار) و resolved_poster_url (الرابط الكامل للعرض).
 * poster_url يُخزّن كما هو في DB ليعمل Filament FileUpload تلقائياً.
 * resolved_poster_url يُستخدم في الـ views لعرض الصور.
 * يدعم: روابط خارجية (http)، مجلد public (images/)، أو مسارات التخزين.
 */
trait HasResolvedPosterUrl
{
    protected function resolvedPosterUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $value = $this->attributes['poster_url'] ?? null;
            if (blank($value)) {
                return null;
            }
            if (str_starts_with($value, 'http')) {
                return $value;
            }
            if (str_starts_with($value, 'images/') || str_starts_with($value, 'الملفات/') || str_starts_with($value, 'البوسترات/')) {
                return Storage::url($value);
            }

            return $value;
        });
    }
}
