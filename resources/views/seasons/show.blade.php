@extends('layouts.app')

@section('title', ($season->title ?? 'الموسم ' . $season->season) . ' — ' . $content->title)

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    {{-- Breadcrumb --}}
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-4 sm:mb-6 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('content.show', $content) }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center truncate max-w-[120px] sm:max-w-none touch-manipulation">{{ $content->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">{{ $season->title ?? 'الموسم ' . $season->season }}</span>
    </nav>

    {{-- Season header with poster --}}
    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 mb-8 sm:mb-10">
        <div class="w-full max-w-[200px] mx-auto sm:mx-0 sm:w-48 shrink-0 rounded-2xl overflow-hidden bg-slate-800/50 border border-slate-700/50 aspect-[2/3] shadow-lg shadow-black/10">
            <img src="{{ $season->resolved_poster_url ?? $content->resolved_poster_url ?? 'https://placehold.co/400x600/1e293b/94a3b8?text=' . urlencode($season->title ?? 'م' . $season->season) }}"
                 alt="{{ $season->title ?? 'الموسم ' . $season->season }}"
                 class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-bold">{{ $season->title ?? 'الموسم ' . $season->season }}</h1>
                @auth
                    <livewire:favorite-button :content-id="$content->id" :season-id="$season->id" />
                @endauth
            </div>
            <p class="text-slate-400 text-sm mb-2 mt-2">{{ $content->title }}</p>
            <p class="text-slate-300">{{ $episodes->count() }} حلقة</p>
            @if($season->description)
                <p class="text-slate-400 text-sm mt-4 line-clamp-3">{{ $season->description }}</p>
            @endif
            <a href="{{ route('content.show', $content) }}" class="inline-flex items-center min-h-[44px] py-2 mt-4 text-rose-500 hover:text-rose-400 text-sm font-medium transition touch-manipulation">← العودة إلى المسلسل</a>
        </div>
    </div>

    {{-- Episodes grid --}}
    <section>
        <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">الحلقات</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
            @foreach($episodes as $episode)
                <x-episode-card :episode="$episode" :season="$season" :content="$content" :show-favorite-button="auth()->check()" />
            @endforeach
        </div>
    </section>
</div>
@endsection
