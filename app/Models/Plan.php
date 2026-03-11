<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * خطة اشتراك (مجاني، أساسي، بريميوم).
 *
 * @property int $id
 * @property string|null $name
 * @property float $price
 * @property string|null $description
 * @property \App\Enums\AccessLevel $access_level
 * @property string|null $stripe_product_id
 * @property string|null $stripe_price_id
 * @property string|null $currency
 * @property string|null $interval
 * @property int $interval_count
 * @property int $sort_order
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'access_level',
        'stripe_product_id',
        'stripe_price_id',
        'currency',
        'interval',
        'interval_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'access_level' => AccessLevel::class,
        ];
    }

    /** عنوان الخطة للعرض (الاسم أو "خطة #id"). */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => $this->name ?? 'خطة #'.$this->id);
    }

    /**
     * الميزات المرتبطة بالخطة.
     *
     * @return HasMany<PlanFeature, $this>
     */
    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class)->orderBy('sort_order');
    }

    /**
     * المدفوعات المرتبطة بهذه الخطة.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * هل الخطة مرتبطة بسعر Stripe ويمكن استخدامها للاشتراك؟
     */
    public function isSubscribable(): bool
    {
        return ! empty($this->stripe_price_id);
    }

    /**
     * تسمية الفترة للعرض من Stripe (interval + interval_count).
     */
    public function getIntervalLabelAttribute(): string
    {
        if (empty($this->interval)) {
            return '—';
        }

        return match (strtolower($this->interval)) {
            'year' => $this->interval_count > 1 ? "{$this->interval_count} سنوات" : 'سنة',
            'month' => $this->interval_count > 1 ? "{$this->interval_count} أشهر" : 'شهر',
            'week' => $this->interval_count > 1 ? "{$this->interval_count} أسابيع" : 'أسبوع',
            'day' => $this->interval_count > 1 ? "{$this->interval_count} أيام" : 'يوم',
            default => $this->interval,
        };
    }
}
