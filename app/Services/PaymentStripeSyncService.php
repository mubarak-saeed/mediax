<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

/**
 * مزامنة سجلات المدفوعات من Stripe (فواتير مدفوعة).
 */
class PaymentStripeSyncService
{
    private const ZERO_DECIMAL_CURRENCIES = [
        'bif',
        'clp',
        'djf',
        'gnf',
        'jpy',
        'kmf',
        'krw',
        'mga',
        'pyg',
        'rwf',
        'ugx',
        'vnd',
        'vuv',
        'xaf',
        'xof',
        'xpf',
    ];

    /**
     * إنشاء سجل دفعة من بيانات فاتورة Stripe (مصفوفة كما في الويب هوك).
     * يعيد الدفعة المُنشأة أو null إذا تم تخطيها.
     */
    public function syncFromInvoiceArray(array $invoice): ?Payment
    {
        if (empty($invoice['id'])) {
            return null;
        }

        $customerId = $this->extractCustomerId($invoice);
        $user = Cashier::findBillable($customerId);
        if (! $user) {
            Log::channel('single')->info('PaymentStripeSyncService: billable not found for customer '.($customerId ?? 'null'));

            return null;
        }

        $stripeInvoiceId = $invoice['id'];
        if (Payment::query()->where('stripe_invoice_id', $stripeInvoiceId)->exists()) {
            return null;
        }

        $priceId = $this->extractPriceIdFromInvoiceLines($invoice['lines']['data'] ?? []);
        $plan = $priceId ? Plan::query()->where('stripe_price_id', $priceId)->first() : null;

        if (! $plan) {
            Log::channel('single')->info('PaymentStripeSyncService: no plan for price '.($priceId ?? 'null'));

            return null;
        }

        $amountPaid = (int) ($invoice['amount_paid'] ?? 0);
        $currency = strtolower((string) ($invoice['currency'] ?? 'usd'));
        $amountDecimal = $this->centsToDecimal($amountPaid, $currency);

        $paidAt = isset($invoice['status_transitions']['paid_at'])
            ? Carbon::createFromTimestampUTC($invoice['status_transitions']['paid_at'])
            : now();

        $paymentIntentId = null;
        if (! empty($invoice['payment_intent'])) {
            $paymentIntentId = is_string($invoice['payment_intent'])
                ? $invoice['payment_intent']
                : ($invoice['payment_intent']['id'] ?? null);
        }

        return Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $amountDecimal,
            'currency' => $currency,
            'payment_method' => 'stripe',
            'payment_status' => 'completed',
            'transaction_id' => $stripeInvoiceId,
            'paid_at' => $paidAt,
            'stripe_invoice_id' => $stripeInvoiceId,
            'stripe_payment_intent_id' => $paymentIntentId,
        ]);
    }

    /**
     * استخراج معرف العميل من الفاتورة (يدعم string أو object).
     */
    private function extractCustomerId(array $invoice): ?string
    {
        $customer = $invoice['customer'] ?? null;
        if ($customer === null) {
            return null;
        }

        if (is_string($customer)) {
            return $customer;
        }

        return $customer['id'] ?? null;
    }

    /**
     * استخراج Price ID من خطوط الفاتورة (أول خط يطابق خطة معروفة).
     */
    private function extractPriceIdFromInvoiceLines(array $lines): ?string
    {
        foreach ($lines as $line) {
            $priceId = $this->extractPriceIdFromLine(is_array($line) ? $line : (array) $line);
            if ($priceId && Plan::query()->where('stripe_price_id', $priceId)->exists()) {
                return $priceId;
            }
        }

        $firstLine = $lines[0] ?? null;

        return $firstLine ? $this->extractPriceIdFromLine(is_array($firstLine) ? $firstLine : (array) $firstLine) : null;
    }

    /**
     * استخراج Price ID من عنصر خط الفاتورة.
     * يدعم: Stripe API الجديد (pricing.price_details.price) والقديم (price كـ string أو object).
     */
    private function extractPriceIdFromLine(array $line): ?string
    {
        if (! empty($line['pricing']['price_details']['price'])) {
            return (string) $line['pricing']['price_details']['price'];
        }

        $price = $line['price'] ?? null;
        if ($price === null) {
            return null;
        }

        if (is_string($price)) {
            return $price;
        }

        return $price['id'] ?? null;
    }

    private function centsToDecimal(int $amountCents, string $currency): float
    {
        return in_array(strtolower($currency), self::ZERO_DECIMAL_CURRENCIES, true)
            ? (float) $amountCents
            : round($amountCents / 100, 2);
    }
}
