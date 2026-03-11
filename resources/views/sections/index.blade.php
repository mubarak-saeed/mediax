@extends('layouts.app')

@section('title', 'الأقسام')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    {{-- مسار التنقل --}}
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">الأقسام</span>
    </nav>
    {{-- العنوان --}}
    <div class="flex items-center justify-between gap-3 mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">الأقسام</h1>
        <a href="{{ route('home') }}"
            class="min-h-11 inline-flex items-center text-slate-400 hover:text-white transition text-sm py-2.5 px-3 rounded-lg touch-manipulation">←
            الرئيسية</a>
    </div>

    @if($sections->isNotEmpty())
    {{-- شبكة الأقسام الرئيسية --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-7 gap-4 sm:gap-5">
        @foreach($sections as $section)
        <a href="{{ route('sections.show', $section->slug) }}" class="group block group/card">
            <div
                class="relative aspect-4/3 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700/50 group-hover/card:border-rose-500/40 shadow-lg shadow-black/20 group-hover/card:shadow-xl group-hover/card:shadow-rose-500/10 transition-all duration-300 group-hover/card:scale-[1.02]">
                @if($section->poster_url)
                <img src="{{ $section->resolved_poster_url }}" alt="{{ $section->name }}"
                    class="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500 ease-out"
                    loading="lazy">
                @else
                <div
                    class="w-full h-full flex items-center justify-center bg-linear-to-br from-slate-700 via-slate-800 to-slate-900 group-hover/card:from-rose-950/40 group-hover/card:via-slate-800 group-hover/card:to-slate-900 transition-colors duration-300">
                    @if($section->type === 'movie')
                    <svg class="w-14 h-14 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    @elseif($section->type === 'series' || $section->type === 'movieserial')
                    <svg class="w-14 h-14 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    @else
                    <svg class="w-14 h-14 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    @endif
                </div>
                @endif
                <div
                    class="absolute inset-0 bg-linear-to-t from-slate-900/95 via-slate-900/30 to-transparent opacity-90">
                </div>
                <div
                    class="absolute inset-0 bg-rose-500/0 group-hover/card:bg-rose-500/10 transition-colors duration-300">
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-4 flex items-center justify-between gap-2">
                    <h2 class="text-sm sm:text-base font-bold text-white drop-shadow-lg line-clamp-2 flex-1 min-w-0">{{
                        $section->name }}</h2>
                    <span
                        class="shrink-0 w-8 h-8 rounded-full bg-white/20 group-hover/card:bg-rose-500 flex items-center justify-center transition-colors duration-300"
                        aria-hidden="true">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 text-slate-500 rounded-xl bg-slate-800/30 border border-slate-700/50">
        <p>لا توجد أقسام متاحة حالياً.</p>
        <a href="{{ route('home') }}"
            class="inline-flex items-center justify-center min-h-11 py-2.5 px-4 mt-4 rounded-xl text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">العودة
            للرئيسية</a>
    </div>
    @endif
</div>
@endsection