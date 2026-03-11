<?php

namespace App\Models;

use App\Traits\HasResolvedPosterUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * حلقة من موسم مسلسل.
 *
 * @property int $id
 * @property int $season_id
 * @property int $episode_number
 * @property string|null $title
 * @property string|null $description
 * @property int|null $duration
 * @property string|null $poster_url
 * @property string|null $file_url
 * @property int|null $file_size
 * @property int $views_count
 */
class Episode extends Model
{
    use HasFactory;
    use HasResolvedPosterUrl;

    protected $fillable = [
        'season_id',
        'episode_number',
        'title',
        'description',
        'duration',
        'poster_url',
        'file_url',
        'file_size',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'episode_number' => 'integer',
            'duration' => 'integer',
            'file_size' => 'integer',
        ];
    }

    /**
     * الموسم الذي تنتمي إليه هذه الحلقة.
     *
     * @return BelongsTo<Season, $this>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
