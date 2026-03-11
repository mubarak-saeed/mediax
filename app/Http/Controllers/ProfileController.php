<?php

namespace App\Http\Controllers;

use App\Actions\Subscription\SyncUserSubscriptionFromStripe;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        if ($request->query('checkout') === 'success' && $user->hasStripeId() && ! $user->hasActiveSubscription()) {
            app(SyncUserSubscriptionFromStripe::class)->sync($user);
        }

        $user->load(['subscriptions', 'subscriptions.items']);

        $currentPlan = null;
        $currentSub = $user->currentSubscription();
        if ($currentSub) {
            $item = $currentSub->items->first();
            $stripePriceId = $item?->stripe_price;
            if ($stripePriceId) {
                $currentPlan = Plan::query()
                    ->where('stripe_price_id', $stripePriceId)
                    ->with('features')
                    ->first();
            }
        }

        return view('profile.show', [
            'user' => $user,
            'currentPlan' => $currentPlan,
            'currentSubscription' => $currentSub,
            'hasPassword' => ! empty($user->password),
        ]);
    }

    /**
     * تحديث الاسم والبريد الإلكتروني.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profile')->with('status', 'تم تحديث بياناتك بنجاح.');
    }

    /**
     * تغيير كلمة المرور (أو تعيينها للمستخدمين الذين سجّلوا عبر Google).
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('profile')->with('status', 'تم تغيير كلمة المرور بنجاح.');
    }
}
