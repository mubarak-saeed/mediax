@extends('layouts.app')

@section('title', ($episode->title ?? 'الحلقة ' . $episode->episode_number) . ' — ' . $content->title)

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-4 sm:mb-6 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}"
            class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('content.show', $content) }}"
            class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center truncate max-w-[120px] sm:max-w-none touch-manipulation">{{
            $content->title }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('seasons.show', [$content, $season]) }}"
            class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center truncate max-w-[120px] sm:max-w-none touch-manipulation">{{
            $season->title ?? 'الموسم ' . $season->season }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">{{ $episode->title ?? 'الحلقة ' . $episode->episode_number }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 sm:gap-8">
        <div class="w-full max-w-md mx-auto lg:mx-0 lg:w-2/5 shrink-0">
            <div class="aspect-video rounded-xl overflow-hidden bg-slate-800 shadow-xl">
                <img src="{{ $episode->resolved_poster_url ?? $season->resolved_poster_url ?? $content->resolved_poster_url ?? 'https://placehold.co/640x360/1e293b/94a3b8?text=' . urlencode($episode->title ?? 'ح' . $episode->episode_number) }}"
                    alt="{{ $episode->title ?? 'الحلقة ' . $episode->episode_number }}"
                    class="w-full h-full object-cover">
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-bold break-words">{{ $episode->title ?? 'الحلقة ' .
                    $episode->episode_number }}</h1>
                <livewire:favorite-button :content-id="$content->id" :season-id="$season->id"
                    :episode-id="$episode->id" />
            </div>
            <p class="text-slate-400 text-sm mb-2">{{ $content->title }} — {{ $season->title ?? 'الموسم ' .
                $season->season }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm mb-4">
                @if($episode->duration)
                <span class="text-slate-400">{{
                    app(\App\Services\MediaMetadataService::class)->formatDurationToHoursMinutes($episode->duration)
                    }}</span>
                @endif
                @if(($episode->views_count ?? 0) > 0)
                <span class="inline-flex items-center gap-1.5 text-slate-400">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($episode->views_count) }} {{ $episode->views_count === 1 ? 'مشاهدة' : 'مشاهدات' }}
                </span>
                @endif
            </div>
            @if($episode->description)
            <p class="text-slate-300 leading-relaxed mb-6">{{ $episode->description }}</p>
            @endif

            <div class="flex flex-wrap gap-3">
                @auth
                @if(auth()->user()->canAccessContent($content))
                @if($streamUrl)
                <a href="{{ route('watch.show', [$content, 'episode' => $episode->id]) }}"
                    class="inline-block bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-[44px] flex items-center justify-center active:scale-[0.98]">مشاهدة
                    الآن</a>
                @if($downloadUrl)
                <a href="{{ $downloadUrl }}"
                    class="inline-block bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-[44px] flex items-center justify-center"
                    target="_blank" download>تحميل</a>
                @endif
                @else
                <p class="text-slate-500">الملف غير متوفر حالياً.</p>
                @endif
                @else
                <a href="{{ route('profile') }}?upgrade=1"
                    class="inline-block bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-[44px] flex items-center justify-center active:scale-[0.98]">اشترك
                    للمشاهدة</a>
                @endif
                @else
                <a href="{{ route('login') }}"
                    class="inline-flex bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-[44px] items-center justify-center active:scale-[0.98]">تسجيل
                    الدخول للمشاهدة </a>
                @endauth
            </div>

            <a href="{{ route('seasons.show', [$content, $season]) }}"
                class="inline-flex items-center min-h-[44px] py-2 mt-6 text-rose-500 hover:text-rose-400 text-sm font-medium transition touch-manipulation">←
                جميع حلقات الموسم</a>
        </div>
    </div>
</div>
@endsection