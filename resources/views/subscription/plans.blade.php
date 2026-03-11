@extends('layouts.app')

@section('title', 'خطط الاشتراك')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">خطط الاشتراك</span>
    </nav>

    <div class="text-center mb-8 sm:mb-12">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">خطط الاشتراك</h1>
        <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto px-1">اشترك لمشاهدة الأفلام والمسلسلات وقراءة الكتب بدون حدود.</p>
    </div>

    @if(session('error'))
        <div class="max-w-md mx-auto mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if(session('status'))
        <div class="max-w-md mx-auto mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
            {{ session('status') }}
        </div>
    @endif
    @if(request()->query('checkout') === 'cancelled')
        <div class="max-w-md mx-auto mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm">
            تم إلغاء عملية الدفع. يمكنك المحاولة مرة أخرى عند الاستعداد.
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="max-w-lg mx-auto text-center py-12 rounded-xl bg-slate-800/30 border border-slate-700/50">
            <p class="text-slate-400 mb-4">الخطط غير مُعدّة حالياً.</p>
          
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center min-h-[44px] py-2.5 px-4 rounded-xl text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">العودة للرئيسية</a>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 max-w-5xl mx-auto">
            @foreach($plans as $plan)
                @php
                    $isCurrentPlan = $currentSubscriptionPriceId && $plan->stripe_price_id === $currentSubscriptionPriceId;
                    $isSubscribed = (bool) $currentSubscriptionPriceId;
                    $currentPlan = $isSubscribed ? $plans->first(fn ($p) => $p->stripe_price_id === $currentSubscriptionPriceId) : null;
                    $isUpgrade = $currentPlan && $plan->access_level->order() > $currentPlan->access_level->order();
                    $isDowngrade = $currentPlan && $plan->access_level->order() < $currentPlan->access_level->order();
                @endphp
                <div class="rounded-2xl border {{ $isCurrentPlan ? 'border-emerald-500/50 bg-emerald-500/5' : ($isUpgrade ? 'border-rose-500/50 bg-rose-500/5' : 'border-slate-700/80 bg-slate-800/30') }} p-6 sm:p-8 flex flex-col">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($isCurrentPlan)
                            <span class="inline-block text-xs font-semibold text-emerald-400 bg-emerald-500/20 px-3 py-1 rounded-full">خطتك الحالية</span>
                        @elseif($isUpgrade)
                            <span class="inline-block text-xs font-semibold text-rose-400 bg-rose-500/20 px-3 py-1 rounded-full">ترقية</span>
                        @elseif($isDowngrade)
                            <span class="inline-block text-xs font-semibold text-slate-400 bg-slate-600/50 px-3 py-1 rounded-full">تخفيض</span>
                        @endif
                        <span class="inline-block text-xs font-medium text-slate-400 bg-slate-700/50 px-3 py-1 rounded-full">{{ $plan->access_level->getLabel() }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold mb-2">{{ $plan->name }}</h2>
                    @if($plan->description)
                        <p class="text-slate-400 text-sm mb-4 flex-1 line-clamp-3">{{ $plan->description }}</p>
                    @endif
                    @if($plan->features->isNotEmpty())
                        <ul class="text-slate-300 text-sm mb-4 space-y-2 flex-1">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-500 shrink-0" aria-hidden="true">✓</span>
                                    {{ $feature->title }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl sm:text-4xl font-bold">{{ number_format((float) $plan->price, 2) }}</span>
                        <span class="text-slate-500">{{ $plan->currency ? strtoupper($plan->currency) : 'ر.س' }}</span>
                        <span class="text-slate-500 text-sm">/ {{ $plan->interval_label }}</span>
                    </div>
                    @auth
                        @if($isCurrentPlan)
                            <a href="{{ route('profile') }}" class="flex items-center justify-center w-full min-h-[48px] bg-slate-600 text-white font-semibold py-3.5 px-4 rounded-xl transition touch-manipulation">
                                إدارة اشتراكك
                            </a>
                        @else
                            <form method="POST" action="{{ route('subscription.checkout') }}" class="mt-auto">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $plan->id }}">
                                @if($isSubscribed)
                                    <p class="text-slate-500 text-xs mb-2">سيتم احتساب الفرق (أو إضافته كرصيد للفاتورة القادمة) على طريقة الدفع المسجّلة.</p>
                                @endif
                                <button type="submit" class="w-full min-h-[48px] {{ $isSubscribed ? 'bg-rose-600 hover:bg-rose-500' : 'bg-slate-600 hover:bg-slate-500' }} text-white font-semibold py-3.5 px-4 rounded-xl transition active:scale-[0.98] touch-manipulation">
                                    {{ $isSubscribed ? 'ترقية إلى هذه الخطة' : 'اختر الخطة' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login', ['intended' => url()->current()]) }}" class="flex items-center justify-center w-full min-h-[48px] bg-rose-600 hover:bg-rose-500 text-white font-semibold py-3.5 px-4 rounded-xl transition active:scale-[0.98] touch-manipulation">
                            تسجيل الدخول للاشتراك
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
