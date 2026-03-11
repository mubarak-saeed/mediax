<?php

namespace App\Services;

use App\Exceptions\PlanSyncException;
use App\Models\Plan;
use Illuminate\Database\UniqueConstraintViolationException;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

/**
 * مزامنة بيانات الخطة من Stripe (المنتج والسعر).
 */
class PlanStripeSyncService
{
    /**
     * عملات Stripe بدون كسور عشرية (المبلغ = القيمة كما هي).
     *
     * @var array<string>
     */
    private const ZERO_DECIMAL_CURRENCIES = [
        'bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf',
        'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf',
    ];

    /**
     * مزامنة بيانات الخطة من Stripe باستخدام stripe_price_id.
     * يحدّث: name, description, price, currency, interval, interval_count (ومنتج Stripe إن وُجد).
     *
     * @throws PlanSyncException
     */
    public function sync(Plan $plan): Plan
    {
        if (empty($plan->stripe_price_id)) {
            return $plan;
        }

        try {
            $stripe = Cashier::stripe();
            $price = $stripe->prices->retrieve($plan->stripe_price_id, [
                'expand' => ['product'],
            ]);
        } catch (ApiErrorException $e) {
            throw PlanSyncException::stripeError($e->getMessage());
        }

        $product = $price->product;
        if (is_object($product)) {
            $newName = $product->name ?? $plan->name;
            $nameConflict = Plan::query()
                ->where('name', $newName)
                ->where('id', '!=', $plan->id)
                ->exists();
            if (! $nameConflict) {
                $plan->name = $newName;
            }
            $plan->description = $product->description ?? $plan->description;
            if (empty($plan->stripe_product_id)) {
                $plan->stripe_product_id = $product->id;
            }
        }

        $plan->currency = strtolower((string) $price->currency);
        $plan->price = $this->unitAmountToDecimal($price->unit_amount, $plan->currency);

        if (isset($price->recurring)) {
            $plan->interval = $price->recurring->interval ?? null;
            $plan->interval_count = (int) ($price->recurring->interval_count ?? 1);
        } else {
            $plan->interval = null;
            $plan->interval_count = 1;
        }

        try {
            $plan->save();
        } catch (UniqueConstraintViolationException $e) {
            throw PlanSyncException::uniqueName();
        }

        return $plan;
    }

    /**
     * تحويل unit_amount من Stripe (أصغر وحدة) إلى قيمة عشرية للعرض.
     */
    private function unitAmountToDecimal(?int $unitAmount, string $currency): float
    {
        if ($unitAmount === null) {
            return 0.0;
        }

        $currency = strtolower($currency);
        if (in_array($currency, self::ZERO_DECIMAL_CURRENCIES, true)) {
            return (float) $unitAmount;
        }

        return round($unitAmount / 100, 2);
    }

    /**
     * محاولة المزامنة دون رمي استثناء. للاستخدام عندما لا نحتاج عرض رسالة للمستخدم.
     */
    public function syncOrFail(Plan $plan): bool
    {
        try {
            $this->sync($plan);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
