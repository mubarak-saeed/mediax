@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200">الملف الشخصي</span>
    </nav>

    @if(request()->query('checkout') === 'success')
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
        تم تفعيل اشتراكك بنجاح. يمكنك الآن مشاهدة وقراءة كل المحتوى.
    </div>
    @endif
    @if(session('status'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
        {{ session('status') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- بطاقة المستخدم --}}
    <section class="mb-6 sm:mb-8 rounded-2xl border border-slate-700/80 bg-slate-800/40 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
            <div
                class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-linear-to-br from-rose-600/30 to-slate-700 flex items-center justify-center shrink-0 border border-slate-600/50">
                <span class="text-3xl sm:text-4xl font-bold text-rose-400/90">{{ mb_substr($user->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 text-center sm:text-right min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white mb-1 wrap-break-word">{{ $user->name }}</h1>
                <p class="text-slate-400 text-sm sm:text-base break-all">{{ $user->email }}</p>
                @if($user->created_at)
                <p class="text-slate-500 text-xs mt-2">عضو منذ {{ $user->created_at->translatedFormat('F Y') }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- تعديل البيانات (الاسم والبريد) --}}
    <section class="mb-6 sm:mb-8 rounded-2xl border border-slate-700/80 overflow-hidden">
        <div class="bg-slate-800/60 px-4 sm:px-6 py-3 border-b border-slate-700/80">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل البيانات
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">الاسم</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        maxlength="255"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-12 transition">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        maxlength="255"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-12 transition">
                </div>
                <button type="submit"
                    class="w-full sm:w-auto min-h-12 px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold transition touch-manipulation active:scale-[0.98]">
                    حفظ التغييرات
                </button>
            </form>
        </div>
    </section>

    {{-- تغيير كلمة المرور / تعيين كلمة المرور --}}
    <section class="mb-6 sm:mb-8 rounded-2xl border border-slate-700/80 overflow-hidden">
        <div class="bg-slate-800/60 px-4 sm:px-6 py-3 border-b border-slate-700/80">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                {{ $hasPassword ? 'تغيير كلمة المرور' : 'تعيين كلمة مرور' }}
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                @if($hasPassword)
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-300 mb-2">كلمة المرور
                        الحالية</label>
                    <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-12 transition">
                </div>
                @endif
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">{{ $hasPassword ? 'كلمة
                        المرور الجديدة' : 'كلمة المرور' }}</label>
                    <input type="password" name="password" id="password" required minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-12 transition">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">تأكيد كلمة
                        المرور</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        minlength="8" autocomplete="new-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-12 transition">
                </div>
                <button type="submit"
                    class="w-full sm:w-auto min-h-12 px-6 py-3 rounded-xl bg-slate-600 hover:bg-slate-500 text-white font-semibold transition touch-manipulation active:scale-[0.98]">
                    {{ $hasPassword ? 'تغيير كلمة المرور' : 'تعيين كلمة المرور' }}
                </button>
            </form>
        </div>
    </section>

    {{-- الاشتراك --}}
    <section class="mb-6 sm:mb-8 rounded-2xl border border-slate-700/80 overflow-hidden">
        <div class="bg-slate-800/60 px-4 sm:px-6 py-3 border-b border-slate-700/80">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                خطتك
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            @if($user->hasActiveSubscription())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-slate-300 font-medium">{{ $currentPlan?->name ?? 'اشتراك نشط' }}</p>
                    @if($currentPlan?->features->isNotEmpty())
                    <ul class="text-slate-500 text-sm mt-2 space-y-1">
                        @foreach($currentPlan->features as $feature)
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500" aria-hidden="true">✓</span>
                            {{ $feature->title }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    @if($currentSubscription?->trial_ends_at && $currentSubscription->trial_ends_at->isFuture())
                    <p class="text-amber-400 text-sm mt-2">فترة التجربة حتى {{
                        $currentSubscription->trial_ends_at->translatedFormat('d F Y') }}</p>
                    @elseif($currentSubscription?->ends_at)
                    <p class="text-slate-500 text-sm mt-2">ينتهي في {{
                        $currentSubscription->ends_at->translatedFormat('d F Y') }}</p>
                    @else
                    <p class="text-slate-500 text-sm mt-2">يُجدد تلقائياً</p>
                    @endif
                </div>
                <a href="{{ route('subscription.billing-portal') }}"
                    class="inline-flex items-center justify-center min-h-11 py-2.5 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition touch-manipulation active:scale-[0.98] shrink-0">
                    إدارة الاشتراك والفواتير
                </a>
            </div>
            @elseif($user->isSuperAdmin())
            <p class="text-slate-300 font-medium">مدير النظام (Super admin) — لديك وصول كامل لجميع المحتويات دون اشتراك.
            </p>
            @elseif($user->isAdmin())
            <p class="text-slate-300 font-medium">أدمن — لديك وصول كامل لجميع المحتويات دون اشتراك.</p>
            @else
            <p class="text-slate-400 mb-4">ليس لديك اشتراك نشط. اشترك لمشاهدة الأفلام والمسلسلات وقراءة الكتب.</p>
            <a href="{{ route('subscription.plans') }}"
                class="inline-flex items-center justify-center min-h-12 py-3 px-6 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold transition touch-manipulation active:scale-[0.98] shadow-lg shadow-rose-900/20">
                عرض خطط الاشتراك
            </a>
            @endif
        </div>
    </section>

    {{-- روابط سريعة --}}
    <section class="rounded-2xl border border-slate-700/80 bg-slate-800/30 overflow-hidden">
        <div class="divide-y divide-slate-700/80">
            <a href="{{ route('favorites') }}"
                class="flex items-center gap-3 px-4 sm:px-6 py-4 text-slate-300 hover:bg-slate-800/60 hover:text-white transition min-h-13 touch-manipulation">
                <span class="w-10 h-10 rounded-xl bg-slate-700/80 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
                <span class="font-medium">المفضلة</span>
                <svg class="w-5 h-5 mr-auto text-slate-500 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <a href="{{ route('subscription.plans') }}"
                class="flex items-center gap-3 px-4 sm:px-6 py-4 text-slate-300 hover:bg-slate-800/60 hover:text-white transition min-h-13 touch-manipulation">
                <span class="w-10 h-10 rounded-xl bg-slate-700/80 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </span>
                <span class="font-medium">خطط الاشتراك</span>
                <svg class="w-5 h-5 mr-auto text-slate-500 shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        </div>
    </section>
</div>
@endsection