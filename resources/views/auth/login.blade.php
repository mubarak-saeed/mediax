@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-slate-700/80 bg-slate-900/60 backdrop-blur-sm p-6 sm:p-8 shadow-xl">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm mb-6 min-h-[44px] py-2 -my-2 transition touch-manipulation" aria-label="الرئيسية">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                الرئيسية
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">تسجيل الدخول</h1>
            <p class="text-slate-400 text-sm mb-6">ادخل إلى حسابك لمتابعة المحتوى</p>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- تسجيل الدخول عبر جوجل --}}
            @if(config('services.google.client_id'))
                <a href="{{ route('auth.google.redirect') }}" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-slate-100 text-slate-800 font-medium py-3.5 px-4 rounded-xl border border-slate-200 transition min-h-[52px] active:scale-[0.98] mb-5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    متابعة باستخدام Google
                </a>
                <div class="relative my-6">
                    <span class="absolute inset-0 flex items-center"><span class="w-full border-t border-slate-700"></span></span>
                    <span class="relative flex justify-center text-xs"><span class="bg-slate-900/60 px-3 text-slate-500">أو</span></span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                @if(request()->query('intended'))
                    <input type="hidden" name="intended" value="{{ request()->query('intended') }}">
                @endif
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-[52px] transition">
                    @error('email')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">كلمة المرور</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-[52px] transition">
                    @error('password')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between gap-3 min-h-[44px] flex-wrap">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="remember" id="remember" class="rounded w-5 h-5 border-slate-600 bg-slate-800 text-rose-600 focus:ring-rose-500">
                        <label for="remember" class="text-sm text-slate-400">تذكرني</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm text-rose-500 hover:text-rose-400 transition">نسيت كلمة المرور؟</a>
                </div>
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-semibold py-3.5 rounded-xl transition min-h-[52px] active:scale-[0.98] shadow-lg shadow-rose-900/20">
                    تسجيل الدخول
                </button>
            </form>

            <p class="mt-6 text-slate-500 text-sm text-center">
                ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-rose-500 hover:text-rose-400 font-medium transition">إنشاء حساب</a>
            </p>
        </div>
    </div>
</div>
@endsection
