<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * تقييم مستخدم لمحتوى (1–5 مع مراجعة اختيارية).
 *
 * @property int $id
 * @property int $user_id
 * @property int $content_id
 * @property int $rating
 * @property string|null $review
 */
class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_id',
        'rating',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'rated_at' => 'datetime',
        ];
    }

    /**
     * المستخدم الذي قدّم التقييم.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المحتوى المُقيّم.
     *
     * @return BelongsTo<Content, $this>
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
