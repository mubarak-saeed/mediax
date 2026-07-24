<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Traits\HasResolvedPosterUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * القسم الرئيسي (مثل: مسلسلات، أفلام، كتب).
 * المحتوى مرتبط بالأقسام الفرعية (SubSection) وليس مباشرة به.
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $type
 * @property int $order
 * @property string|null $poster_url
 */
class Section extends Model
{
    use HasFactory;
    use HasResolvedPosterUrl;

    protected $fillable = [
        'name',
        'status',
        'slug',
        'type',
        'order',
        'poster_url',
    ];

    protected function casts()
    {
        return [
            'order' => 'integer',
            'status' => StatusEnum::class,
        ];
    }

    /**
     * الأقسام الفرعية التابعة لهذا القسم.
     *
     * @return HasMany<SubSection, $this>
     */
    public function subSections(): HasMany
    {
        return $this->hasMany(SubSection::class, 'section_id')->orderBy('order');
    }

    public function subSection(): HasOne
    {
        return $this->hasOne(SubSection::class, 'section_id');
    }

    /**
     * المحتويات عبر الأقسام الفرعية (للعرض في لوحة التحكم).
     *
     * @return HasManyThrough<Content, SubSection, $this>
     */
    public function contents()
    {
        return $this->hasManyThrough(Content::class, SubSection::class, 'section_id', 'sub_section_id');
    }

    
}
