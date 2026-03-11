<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * عنصر مفضلة: محتوى كامل، موسم، أو حلقة محددة.
 *
 * @property int $id
 * @property int $user_id
 * @property int $content_id
 * @property int|null $season_id
 * @property int|null $episode_id
 */
class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_id',
        'season_id',
        'episode_id',
    ];

    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
        ];
    }

    /**
     * المستخدم صاحب المفضلة.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المحتوى المرتبط.
     *
     * @return BelongsTo<Content, $this>
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * الموسم (إن كانت المفضلة لموسم أو حلقة).
     *
     * @return BelongsTo<Season, $this>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * الحلقة (إن كانت المفضلة لحلقة محددة).
     *
     * @return BelongsTo<Episode, $this>
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    /** هل المفضلة للمحتوى كاملاً (بدون موسم أو حلقة)؟ */
    public function isContentOnly(): bool
    {
        return $this->season_id === null && $this->episode_id === null;
    }

    /** هل المفضلة لموسم كامل؟ */
    public function isSeason(): bool
    {
        return $this->season_id !== null && $this->episode_id === null;
    }

    /** هل المفضلة لحلقة محددة؟ */
    public function isEpisode(): bool
    {
        return $this->episode_id !== null;
    }
}
