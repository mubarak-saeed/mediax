<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * النوع الفني (مثل: دراما، أكشن، كوميدي).
 *
 * @property int $id
 * @property string $name
 */
class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * المحتويات المرتبطة بهذا النوع.
     *
     * @return BelongsToMany<Content, $this>
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_genres')->withTimestamps();
    }
}
