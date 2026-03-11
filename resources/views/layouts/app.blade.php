<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — MediaX</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
@php $isAuthPage = request()->routeIs('login', 'register'); @endphp
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased overflow-x-hidden" style="font-family: 'Tajawal', sans-serif;" x-data="{ menuOpen: false }" :class="{ 'overflow-hidden': menuOpen }">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-slate-950/90 backdrop-blur-xl border-b border-slate-800/80 shadow-[0_4px_24px_-4px_rgba(0,0,0,0.4)] pt-[env(safe-area-inset-top,0px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 min-h-[3.5rem] sm:h-[4.25rem] gap-2 sm:gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="shrink-0 flex items-center py-2 transition-opacity hover:opacity-90 active:opacity-95" aria-label="MediaX - الرئيسية">
                    <img src="{{ asset('images/mediaxlogo.png') }}"
                         alt="MediaX"
                         width="160"
                         height="40"
                         class="h-8 w-auto max-w-[130px] sm:h-9 sm:max-w-[160px] lg:h-10 lg:max-w-[200px] object-contain"
                         fetchpriority="high" />
                </a>

                @unless($isAuthPage)
                {{-- Search: في الـ nav على كل الشاشات (جوال وسطح المكتب) --}}
                <div class="flex-1 min-w-0 max-w-md mx-2 sm:mx-4 lg:mx-6">
                    <livewire:search-bar />
                </div>

                {{-- Desktop nav links --}}
                <div class="hidden md:flex items-center gap-1 shrink-0">
                    <a href="{{ route('home') }}" class="text-slate-400 hover:text-white hover:bg-slate-800/60 transition text-sm font-medium py-2.5 px-3 rounded-lg">الرئيسية</a>
                    @isset($navSections)
                        @foreach($navSections as $navSection)
                            <a href="{{ route('sections.show', $navSection->slug) }}" class="text-slate-400 hover:text-white hover:bg-slate-800/60 transition text-sm font-medium py-2.5 px-3 rounded-lg">{{ $navSection->name }}</a>
                        @endforeach
                    @endisset
                    <a href="{{ route('sections.index') }}" class="text-slate-400 hover:text-white hover:bg-slate-800/60 transition text-sm font-medium py-2.5 px-3 rounded-lg">جميع الأقسام</a>
                    <span class="w-px h-5 bg-slate-700 mx-1" aria-hidden="true"></span>
                    @auth
                        {{-- قائمة المستخدم المنسدلة (سطح المكتب) --}}
                        <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false" @keydown.escape.window="userMenuOpen = false">
                            <button type="button"
                                    @click="userMenuOpen = ! userMenuOpen"
                                    class="flex items-center gap-2 text-slate-300 hover:text-white hover:bg-slate-800/60 transition text-sm font-medium py-2.5 px-3 rounded-lg min-h-[40px]"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    :aria-expanded="userMenuOpen"
                                    id="user-menu-button">
                                <span class="max-w-[120px] truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 shrink-0 transition-transform" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="userMenuOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-cloak
                                 class="absolute left-0 top-full mt-1 w-56 rounded-xl bg-slate-800 border border-slate-700 shadow-xl py-1 z-50"
                                 role="menu"
                                 aria-orientation="vertical"
                                 aria-labelledby="user-menu-button"
                                 style="display: none;">
                                <div class="px-4 py-3 border-b border-slate-700/80">
                                    <p class="text-xs text-slate-500">مرحباً</p>
                                    <p class="font-medium text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                                </div>
                                 @if(auth()->user()->canAccessPanel(app(\Filament\Panel::class)))
                                    <a href="{{ url('/admin') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-emerald-300 hover:bg-emerald-600/10 hover:text-emerald-200 transition" role="menuitem">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M4 10h16v9H4zM9 3h6v4H9z"/></svg>
                                        لوحة التحكم
                                    </a>
                                @endif
                                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white transition" role="menuitem">
                                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    الملف الشخصي
                                </a>
                               
                                <a href="{{ route('subscription.plans') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white transition" role="menuitem">
                                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    خطط الاشتراك
                                </a>
                                <a href="{{ route('favorites') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white transition" role="menuitem">
                                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    المفضلة
                                </a>
                                <div class="border-t border-slate-700/80 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}" role="menuitem">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-slate-500 hover:bg-slate-700/80 hover:text-rose-400 transition text-right">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            تسجيل الخروج
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-400 hover:text-white hover:bg-slate-800/60 transition text-sm font-medium py-2.5 px-3 rounded-lg">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="bg-rose-600 hover:bg-rose-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-rose-900/20 hover:shadow-rose-500/20 active:scale-[0.98]">إنشاء حساب</a>
                    @endauth
                </div>

                {{-- Mobile: زر القائمة فقط (البحث في الـ nav أعلاه) --}}
                <div class="flex items-center gap-1 md:hidden shrink-0">
                    <button type="button"
                            @click="menuOpen = true"
                            class="p-3 min-h-[44px] min-w-[44px] flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition active:scale-95"
                            aria-label="فتح القائمة"
                            :aria-expanded="menuOpen"
                            aria-controls="mobile-menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                @else
                {{-- صفحة تسجيل الدخول/إنشاء حساب: رابط الرئيسية فقط --}}
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition text-sm font-medium py-2.5 px-3 rounded-lg">الرئيسية</a>
                @endunless
            </div>
        </div>
    </nav>

    {{-- Mobile menu overlay + panel (مخفي في صفحات المصادقة) --}}
    @unless($isAuthPage)
    <div id="mobile-menu"
         class="fixed inset-0 z-[60] md:hidden"
         x-show="menuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="menuOpen = false"
         x-cloak
         style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="absolute top-0 right-0 h-full w-80 max-w-[90vw] bg-slate-900/95 backdrop-blur-xl border-l border-slate-700/80 shadow-2xl flex flex-col"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700/80">
                <span class="font-bold text-lg text-white">القائمة</span>
                <button type="button" @click="menuOpen = false" class="p-2.5 min-h-[44px] min-w-[44px] flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition" aria-label="إغلاق القائمة">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-5 px-3">
                <ul class="space-y-1 px-2">
                    <li><a href="{{ route('home') }}" class="block py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false">الرئيسية</a></li>
                    @isset($navSections)
                        @foreach($navSections as $navSection)
                            <li><a href="{{ route('sections.show', $navSection->slug) }}" class="block py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false">{{ $navSection->name }}</a></li>
                        @endforeach
                    @endisset
                    <li><a href="{{ route('sections.index') }}" class="block py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false">جميع الأقسام</a></li>
                    <li class="my-2 border-t border-slate-700/80"></li>
                    @auth
                        {{-- المستخدم: اسم + روابط الحساب (جوال) --}}
                        <li class="px-4 py-3 border-b border-slate-700/80 mb-2">
                            <p class="text-xs text-slate-500">مرحباً</p>
                            <p class="font-bold text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                        </li>
                        @if(auth()->user()->canAccessPanel(app(\Filament\Panel::class)))
                            <li>
                                <a href="{{ url('/admin') }}" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-emerald-300 hover:bg-emerald-600/10 hover:text-emerald-200 transition font-medium" @click="menuOpen = false">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-600/15 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M4 10h16v9H4zM9 3h6v4H9z"/>
                                        </svg>
                                    </span>
                                    لوحة التحكم
                                </a>
                            </li>
                        @endif
                        <li><a href="{{ route('profile') }}" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false"><span class="w-8 h-8 rounded-lg bg-slate-700/80 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>الملف الشخصي</a></li>
                        <li><a href="{{ route('subscription.plans') }}" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false"><span class="w-8 h-8 rounded-lg bg-slate-700/80 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></span>خطط الاشتراك</a></li>
                        <li><a href="{{ route('favorites') }}" class="flex items-center gap-3 py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false"><span class="w-8 h-8 rounded-lg bg-slate-700/80 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></span>المفضلة</a></li>
                        <li class="mt-2 border-t border-slate-700/80 pt-2">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 py-3.5 px-4 rounded-xl text-slate-500 hover:bg-slate-800/80 hover:text-rose-400 transition font-medium text-right">
                                    <span class="w-8 h-8 rounded-lg bg-slate-700/80 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="block py-3.5 px-4 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-white transition font-medium" @click="menuOpen = false">تسجيل الدخول</a></li>
                        <li class="pt-2 px-2"><a href="{{ route('register') }}" class="block py-3.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-center font-semibold transition shadow-lg shadow-rose-900/20" @click="menuOpen = false">إنشاء حساب</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
    @endunless

    <main class="pt-[calc(4rem+env(safe-area-inset-top,0px))] sm:pt-[calc(4.25rem+env(safe-area-inset-top,0px))] min-h-[calc(100vh-5rem)] pb-[env(safe-area-inset-bottom,0px)]">
        @if (session('status'))
            <div class="max-w-7xl mx-auto px-3 sm:px-6 py-2">
                <div class="bg-emerald-500/20 text-emerald-300 px-4 py-3 rounded-xl text-sm sm:text-base">{{ session('status') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="max-w-7xl mx-auto px-3 sm:px-6 py-2">
                <div class="bg-rose-500/20 text-rose-300 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="min-w-0 w-full">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-800 py-8 mt-8 sm:mt-12 pb-[env(safe-area-inset-bottom,0px)]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6">
            @unless($isAuthPage)
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-slate-500 text-sm mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition py-2.5 px-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
                @isset($navSections)
                    @foreach($navSections as $navSection)
                        <a href="{{ route('sections.show', $navSection->slug) }}" class="hover:text-white transition py-2.5 px-2 min-h-[44px] inline-flex items-center touch-manipulation">{{ $navSection->name }}</a>
                    @endforeach
                @endisset
                <a href="{{ route('sections.index') }}" class="hover:text-white transition py-2.5 px-2 min-h-[44px] inline-flex items-center touch-manipulation">جميع الأقسام</a>
                @auth
                    <a href="{{ route('profile') }}" class="hover:text-white transition py-2.5 px-2 min-h-[44px] inline-flex items-center touch-manipulation">الملف الشخصي</a>
                    <a href="{{ route('favorites') }}" class="hover:text-white transition py-2.5 px-2 min-h-[44px] inline-flex items-center touch-manipulation">المفضلة</a>
                @endauth
            </div>
            @endunless
            <p class="text-center text-slate-500 text-xs sm:text-sm">&copy; {{ date('Y') }} MediaX. منصة ترفيه وقراءة.</p>
        </div>
    </footer>

    @livewireScripts
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
