@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-slate-700/80 bg-slate-900/60 backdrop-blur-sm p-6 sm:p-8 shadow-xl">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm mb-6 min-h-[44px] py-2 -my-2 transition touch-manipulation" aria-label="تسجيل الدخول">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                تسجيل الدخول
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">إعادة تعيين كلمة المرور</h1>
            <p class="text-slate-400 text-sm mb-6">أدخل كلمة المرور الجديدة لحسابك</p>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required autofocus autocomplete="username"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-[52px] transition">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">كلمة المرور الجديدة</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-[52px] transition">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl bg-slate-800/80 border border-slate-600 text-white placeholder-slate-500 px-4 py-3.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-rose-500 min-h-[52px] transition">
                </div>
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-semibold py-3.5 rounded-xl transition min-h-[52px] active:scale-[0.98] shadow-lg shadow-rose-900/20">
                    إعادة تعيين كلمة المرور
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
