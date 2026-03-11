<?php

namespace App\Models;

use App\Traits\HasResolvedPosterUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * موسم مسلسل: مجموعة حلقات تحت مسمى واحد (موسم 1، 2، ...).
 *
 * @property int $id
 * @property int $content_id
 * @property string $season
 * @property string|null $title
 * @property string|null $description
 * @property string|null $poster_url
 */
class Season extends Model
{
    use HasFactory;
    use HasResolvedPosterUrl;

    protected $fillable = [
        'content_id',
        'season',
        'title',
        'description',
        'poster_url',
    ];

    /**
     * المسلسل الذي ينتمي إليه هذا الموسم.
     *
     * @return BelongsTo<Content, $this>
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * @return HasMany<Episode, $this>
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function episode(): HasOne
    {
        return $this->hasOne(Episode::class);
    }
}
