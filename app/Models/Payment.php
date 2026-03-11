<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * سجل دفعة (مزامن من Stripe عند invoice.payment_succeeded أو يدوي).
 *
 * @property int $id
 * @property string|null $stripe_invoice_id
 * @property string|null $stripe_payment_intent_id
 * @property int $user_id
 * @property int $plan_id
 * @property float $amount
 * @property string|null $currency
 * @property string $payment_method
 * @property \App\Enums\PaymentStatus $payment_status
 * @property string $transaction_id
 * @property \Carbon\Carbon $paid_at
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_invoice_id',
        'stripe_payment_intent_id',
        'user_id',
        'plan_id',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    /**
     * المستخدم الذي قام بالدفع.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الخطة المشترى بها.
     *
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * هل الدفعة من Stripe؟
     */
    public function isFromStripe(): bool
    {
        return ! empty($this->stripe_invoice_id);
    }

    /**
     * رابط الفاتورة في لوحة Stripe (للعميل).
     */
    public function getStripeInvoiceUrlAttribute(): ?string
    {
        if (empty($this->stripe_invoice_id)) {
            return null;
        }

        return 'https://dashboard.stripe.com/invoices/'.$this->stripe_invoice_id;
    }
}
