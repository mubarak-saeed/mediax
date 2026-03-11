<?php

namespace App\Models;

use App\Enums\AccessLevel;
use App\Enums\ContentType;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\ContentResource;
use App\Traits\HasResolvedPosterUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Scout\Searchable;

/**
 * نموذج المحتوى: فيلم، مسلسل، أو كتاب.
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \App\Enums\ContentType $type
 * @property string $language
 * @property int|null $release_year
 * @property string|null $duration
 * @property string|null $poster_url
 * @property string|null $file_url
 * @property int|null $file_size
 * @property \App\Enums\AccessLevel $access_level_required
 * @property int|null $created_by
 * @property int|null $sub_section_id
 * @property bool $is_featured
 * @property int $views_count
 */
class Content extends Model
{
    use HasFactory;
    use HasResolvedPosterUrl;
    use Searchable;

    protected $fillable = [
        'title',
        'description',
        'type',
        'language',
        'release_year',
        'duration',
        'poster_url',
        'file_url',
        'file_size',
        'access_level_required',
        'created_by',
        'sub_section_id',
        'is_featured',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'file_size' => 'integer',
            'type' => ContentType::class,
            'access_level_required' => AccessLevel::class,
            'is_featured' => 'boolean',
        ];
    }

    /**
     * رابط تعديل المحتوى في لوحة الإدارة.
     * المحتوى متداخل تحت القسم الفرعي: sections/{section}/sub-sections/{subsection}/contents/{content}/edit
     */
    public function getAdminEditUrl(): string
    {
        $subsection = $this->subsection;
        $section = $subsection?->section;

        if (! $subsection || ! $section) {
            return '#';
        }

        return ContentResource::getUrl('edit', [
            'record' => $this,
            'sub_section' => $subsection,
            'section' => $section,
        ]);
    }

    /**
     * البيانات القابلة للبحث (لـ Laravel Scout).
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    /**
     * نطاق: المحتوى المميز فقط.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * نطاق: ترتيب المسلسلات حسب تاريخ أحدث حلقة (للصفحة الرئيسية).
     */
    public function scopeOrderByLatestEpisode(Builder $query): Builder
    {
        return $query
            ->withMax('episodes as latest_episode_updated_at', 'updated_at')
            ->orderByDesc('latest_episode_updated_at');
    }

    /**
     * المستخدم الذي أنشأ المحتوى.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * القسم الفرعي الذي ينتمي إليه المحتوى.
     *
     * @return BelongsTo<SubSection, $this>
     */
    public function subsection(): BelongsTo
    {
        return $this->belongsTo(SubSection::class, 'sub_section_id');
    }

    /**
     * القسم الرئيسي (عبر القسم الفرعي) — للعرض والـ breadcrumb.
     */
    public function getSectionAttribute(): ?Section
    {
        return $this->subsection?->section;
    }

    /**
     * الأنواع الفنية المرتبطة بالمحتوى.
     *
     * @return BelongsToMany<Genre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'content_genres')->withTimestamps();
    }

    /**
     * مواسم المسلسل (لنوع series فقط).
     *
     * @return HasMany<Season, $this>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * جميع حلقات المسلسل عبر المواسم.
     *
     * @return HasManyThrough<Episode, Season, $this>
     */
    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(Episode::class, Season::class);
    }

    /**
     * أحدث حلقة في المسلسل (حسب آخر موسم ثم آخر حلقة).
     */
    public function getLatestEpisodeAttribute(): ?Episode
    {
        if (! $this->isSeriesOrMovieSerial()) {
            return null;
        }

        if ($this->relationLoaded('seasons')) {
            $lastSeason = $this->seasons->sortByDesc(fn(Season $s) => (int) $s->season)->first();
            if (! $lastSeason || ! $lastSeason->relationLoaded('episodes')) {
                return null;
            }

            return $lastSeason->episodes->sortByDesc('episode_number')->first();
        }

        return Episode::query()
            ->whereHas('season', fn($q) => $q->where('content_id', $this->id))
            ->orderByDesc('season_id')
            ->orderByDesc('episode_number')
            ->first();
    }

    /**
     * آخر حلقة تمت إضافتها (حسب updated_at) — للعرض في الصفحة الرئيسية.
     */
    public function getNewestEpisodeAttribute(): ?Episode
    {
        if (! $this->isSeriesOrMovieSerial()) {
            return null;
        }

        return Episode::query()
            ->whereNotNull('file_url')
            ->whereHas('season', fn($q) => $q->where('content_id', $this->id))
            ->orderByDesc('updated_at')
            ->with('season')
            ->first();
    }

    /**
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * @return HasMany<Rating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * متوسط التقييم (1–5) أو null إن لم يُقيّم.
     */
    public function getAverageRatingAttribute(): ?float
    {
        if (isset($this->attributes['ratings_avg_rating'])) {
            return $this->attributes['ratings_avg_rating'] !== null
                ? (float) round($this->attributes['ratings_avg_rating'], 1)
                : null;
        }

        $avg = $this->ratings()->avg('rating');

        return $avg !== null ? (float) round($avg, 1) : null;
    }

    /**
     * عدد التقييمات.
     */
    public function getRatingsCountAttribute(): int
    {
        return $this->attributes['ratings_count'] ?? $this->ratings()->count();
    }

    public function isSeriesOrMovieSerial(): bool
    {
        return $this->type === ContentType::MovieSerial || $this->type === ContentType::Series;
    }
}
