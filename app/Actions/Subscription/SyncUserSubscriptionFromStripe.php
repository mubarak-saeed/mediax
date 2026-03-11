<?php

namespace App\Actions\Subscription;

use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription;

/**
 * مزامنة اشتراكات المستخدم من Stripe إلى قاعدة البيانات.
 * يُستخدم عند العودة من صفحة الدفع عندما لا يصل الـ webhook (مثلاً في التطوير المحلي).
 */
class SyncUserSubscriptionFromStripe
{
    /**
     * جلب اشتراكات العميل من Stripe وإنشاؤها محلياً إن لم تكن موجودة.
     */
    public function sync(User $user): bool
    {
        if (! $user->hasStripeId()) {
            return false;
        }

        try {
            $subscriptions = $user->stripe()->subscriptions->all([
                'customer' => $user->stripe_id,
                'expand' => ['data.items.data.price'],
                'status' => 'all',
            ]);
        } catch (\Throwable) {
            return false;
        }

        $synced = false;
        foreach ($subscriptions->data as $stripeSubscription) {
            $local = $user->subscriptions()->where('stripe_id', $stripeSubscription->id)->first();

            if ($local) {
                if ($this->updateSubscriptionFromStripe($local, $stripeSubscription)) {
                    $synced = true;
                }
            } elseif ($this->shouldCreateLocalSubscription($stripeSubscription->status)) {
                $this->createSubscriptionFromStripeObject($user, $stripeSubscription);
                $synced = true;
            }
        }

        return $synced;
    }

    /**
     * تحديث الاشتراك المحلي من بيانات Stripe.
     */
    private function updateSubscriptionFromStripe(Subscription $local, \Stripe\Subscription $stripe): bool
    {
        $trialEndsAt = isset($stripe->trial_end)
            ? Carbon::createFromTimestamp($stripe->trial_end)
            : null;

        $endsAt = isset($stripe->cancel_at)
            ? Carbon::createFromTimestamp($stripe->cancel_at)
            : ($stripe->canceled_at ? Carbon::createFromTimestamp($stripe->canceled_at) : null);

        $itemsData = $stripe->items->data;
        $firstItem = $itemsData[0] ?? null;
        $stripePrice = $firstItem?->price->id ?? null;
        $quantity = $firstItem?->quantity ?? null;

        $changed = $local->stripe_status !== $stripe->status
            || $local->trial_ends_at?->timestamp !== $trialEndsAt?->timestamp
            || $local->ends_at?->timestamp !== $endsAt?->timestamp
            || $local->stripe_price !== $stripePrice;

        if ($changed) {
            $local->update([
                'stripe_status' => $stripe->status,
                'stripe_price' => $stripePrice,
                'quantity' => $quantity,
                'trial_ends_at' => $trialEndsAt,
                'ends_at' => $endsAt,
            ]);
        }

        return $changed;
    }

    /**
     * إنشاء سجل اشتراك وعناصره من كائن Stripe.
     */
    private function createSubscriptionFromStripeObject(User $user, \Stripe\Subscription $stripeSubscription): void
    {
        $trialEndsAt = isset($stripeSubscription->trial_end)
            ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
            : null;

        $itemsData = $stripeSubscription->items->data;
        $firstItem = $itemsData[0];
        $isSinglePrice = count($itemsData) === 1;

        $metadata = $stripeSubscription->metadata;
        $type = (is_object($metadata) ? ($metadata->type ?? $metadata->name ?? null) : ($metadata['type'] ?? $metadata['name'] ?? null)) ?? 'default';

        $subscription = $user->subscriptions()->create([
            'stripe_id' => $stripeSubscription->id,
            'type' => $type,
            'stripe_status' => $stripeSubscription->status,
            'stripe_price' => $isSinglePrice ? $firstItem->price->id : null,
            'quantity' => $isSinglePrice && isset($firstItem->quantity) ? $firstItem->quantity : null,
            'trial_ends_at' => $trialEndsAt,
            'ends_at' => null,
        ]);

        foreach ($itemsData as $item) {
            $subscription->items()->create([
                'stripe_id' => $item->id,
                'stripe_product' => $item->price->product,
                'stripe_price' => $item->price->id,
                'quantity' => $item->quantity ?? null,
            ]);
        }
    }

    private function shouldCreateLocalSubscription(string $status): bool
    {
        return in_array($status, ['active', 'trialing', 'past_due', 'unpaid'], true);
    }
}
