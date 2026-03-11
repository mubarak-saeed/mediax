<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\CheckoutRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display subscription plans (from DB, linked to Stripe). For guests and authenticated users.
     */
    public function plans(Request $request): View
    {

        $plans = Plan::query()
            ->whereNotNull('stripe_price_id')
            ->whereNotNull('stripe_product_id')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->with('features')
            ->get();

        $currentSubscriptionPriceId = null;
        $currentSubscription = $request->user()?->currentSubscription();
        if ($currentSubscription) {
            $item = $currentSubscription->items->first();
            $currentSubscriptionPriceId = $item?->stripe_price;
        }

        return view('subscription.plans', [
            'plans' => $plans,
            'currentSubscriptionPriceId' => $currentSubscriptionPriceId,
        ]);
    }

    /**
     * Start checkout for a plan, or upgrade/swap if already subscribed.
     */
    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();

        $plan = Plan::query()
            ->whereNotNull('stripe_price_id')
            ->find($request->validated('plan'));

        if (! $plan) {
            return redirect()->route('subscription.plans')
                ->with('error', 'الخطة المختارة غير متاحة.');
        }

        $currentSubscription = $user->currentSubscription();
        if ($currentSubscription) {
            $currentPriceId = $currentSubscription->items->first()?->stripe_price;
            if ($currentPriceId === $plan->stripe_price_id) {
                return redirect()->route('profile')
                    ->with('status', 'أنت مشترك بهذه الخطة بالفعل. يمكنك إدارة اشتراكك من الأسفل.');
            }
            try {
                $currentSubscription->swap($plan->stripe_price_id);
            } catch (\Throwable $e) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'تعذر تغيير الخطة. جرّب من "إدارة الاشتراك والفواتير" أو تواصل مع الدعم.');
            }

            return redirect()->route('profile')
                ->with('status', 'تم تغيير خطتك إلى '.$plan->name.' بنجاح. سيتم خصم الفرق أو إضافته كرصيد على طريقة الدفع المسجّلة (وفق احتساب Stripe التناسبي).');
        }

        $baseUrl = rtrim(config('app.url'), '/');
        try {
            $checkout = $user->newSubscription('default', $plan->stripe_price_id)->checkout([
                'success_url' => $baseUrl.route('profile', [], false).'?checkout=success',
                'cancel_url' => $baseUrl.route('subscription.plans', [], false).'?checkout=cancelled',

            ]);

            return $checkout->redirect();
        } catch (\Throwable $th) {
            $containsNetworkError = str_contains($th->getMessage(), 'Network error');

            return redirect()->route('subscription.plans')
                ->withErrors([$containsNetworkError ? 'تعذر الاتصال. تأكد من اتصالك بالإنترنت وجرّب مرة أخرى.' : 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Redirect to Stripe Billing Portal (manage subscription, payment methods, invoices).
     */
    public function billingPortal(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasStripeId()) {
            return redirect()->route('profile')->with('error', 'يجب أن تكون مشتركاً للوصول إلى إدارة الفواتير.');
        }

        $returnUrl = rtrim(config('app.url'), '/').route('profile', [], false);

        return $user->redirectToBillingPortal($returnUrl);
    }
}
