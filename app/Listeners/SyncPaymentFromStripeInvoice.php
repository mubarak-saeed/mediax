<?php

namespace App\Listeners;

use App\Actions\Subscription\SyncUserSubscriptionFromStripe;
use App\Models\User;
use App\Services\PaymentStripeSyncService;
use Laravel\Cashier\Events\WebhookReceived;

class SyncPaymentFromStripeInvoice
{
    public function __construct(
        protected PaymentStripeSyncService $syncService
    ) {}

    /**
     * معالجة أحداث Stripe: invoice.payment_succeeded و customer.subscription.*
     */
    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'] ?? '';

        if ($type === 'invoice.payment_succeeded') {
            $this->handleInvoicePaid($event);
        } elseif (in_array($type, ['customer.subscription.updated', 'customer.subscription.deleted'], true)) {
            $this->handleSubscriptionEvent($event);
        }
    }

    private function handleInvoicePaid(WebhookReceived $event): void
    {
        $invoice = $event->payload['data']['object'] ?? null;
        if (is_array($invoice)) {
            $this->syncService->syncFromInvoiceArray($invoice);
        }
    }

    private function handleSubscriptionEvent(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'] ?? null;
        if (! is_array($subscription)) {
            return;
        }

        $customerId = $subscription['customer'] ?? null;
        if (! $customerId) {
            return;
        }

        $user = User::query()->where('stripe_id', $customerId)->first();
        if ($user) {
            app(SyncUserSubscriptionFromStripe::class)->sync($user);
        }
    }
}
