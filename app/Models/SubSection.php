<?php

namespace App\Models;

use App\Traits\HasResolvedPosterUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * القسم الفرعي (مثل: مسلسلات عربية، أفلام أجنبية).
 * يرتبط بقسم رئيسي (Section) ويحتوي على محتويات (Content).
 *
 * @property int $id
 * @property int $section_id
 * @property string $name
 * @property string $slug
 * @property string|null $type
 * @property int $order
 * @property string|null $poster_url
 */
class SubSection extends Model
{
    use HasFactory;
    use HasResolvedPosterUrl;

    protected $fillable = [
        'section_id',
        'name',
        'slug',
        'type',
        'order',
        'poster_url',
    ];

    /**
     * القسم الرئيسي الذي يتبعه هذا القسم الفرعي.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * المحتويات في هذا القسم الفرعي.
     *
     * @return HasMany<Content, $this>
     */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'sub_section_id');
    }
}
