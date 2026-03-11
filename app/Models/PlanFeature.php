<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ميزة ضمن خطة اشتراك (لعرضها في صفحة الخطط).
 *
 * @property int $id
 * @property int $plan_id
 * @property string $title
 * @property int $sort_order
 */
class PlanFeature extends Model
{
    protected $fillable = [
        'plan_id',
        'title',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * الخطة التي تنتمي إليها الميزة.
     *
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
