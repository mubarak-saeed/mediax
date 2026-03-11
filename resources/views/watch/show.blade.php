@extends('layouts.app')

@section('title', isset($episode) ? 'مشاهدة: ' . $content->title . ' - ' . ($episode->title ?? 'الحلقة ' . $episode->episode_number) : 'مشاهدة: ' . $content->title)

@section('content')
<div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('content.show', $content) }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center truncate max-w-[150px] sm:max-w-none touch-manipulation">{{ $content->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">{{ isset($episode) ? ($episode->title ?? 'الحلقة ' . $episode->episode_number) : 'مشاهدة' }}</span>
    </nav>
    @php
        $nextEpisodeUrl = isset($nextEpisode) && $nextEpisode ? route('watch.show', [$content, 'episode' => $nextEpisode->id]) : null;
    @endphp
    <div class="rounded-xl overflow-hidden bg-black aspect-video w-full" x-data="{ nextUrl: @js($nextEpisodeUrl) }">
        <video  class="w-full h-full" autoplay controls controlsList="nodownload"  disablePictureInPicture playsinline title="{{ $content->title }}"  preload="metadata"
               @ended="if(nextUrl) window.location.href = nextUrl">
            <source src="{{ $streamUrl }}" type="video/mp4">
            متصفحك لا يدعم تشغيل الفيديو.
        </video>
    </div>
    <div class="flex flex-wrap items-center gap-3 mt-4 min-w-0">
        <h1 class="text-xl sm:text-2xl font-bold break-words">{{ $content->title }}</h1>
        @auth
            @if(isset($episode))
                <livewire:favorite-button :content-id="$content->id" :season-id="$episode->season_id" :episode-id="$episode->id" />
            @endif
        @endauth
    </div>
    @if(isset($episode))
        <p class="text-slate-400 mt-1 text-sm">الموسم {{ $episode->season->season }} — {{ $episode->title ?? 'الحلقة ' . $episode->episode_number }}</p>
        <div class="flex flex-wrap items-center gap-3 mt-3">
            @if(isset($nextEpisode) && $nextEpisode)
                <a href="{{ route('watch.show', [$content, 'episode' => $nextEpisode->id]) }}" class="inline-flex items-center gap-2 min-h-[44px] py-2 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-medium text-sm transition touch-manipulation">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    الحلقة التالية
                </a>
            @endif
            <a href="{{ route('episodes.show', [$content, $episode->season, $episode]) }}" class="inline-flex items-center min-h-[44px] py-2 text-rose-500 hover:text-rose-400 text-sm font-medium transition touch-manipulation">← صفحة تفاصيل الحلقة</a>
        </div>
        @if($allEpisodes->isNotEmpty())
            <details class="mt-4 group">
                <summary class="cursor-pointer list-none flex items-center gap-2 text-slate-400 hover:text-white text-sm font-medium py-2 min-h-[44px]">
                    <svg class="w-4 h-4 transition group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    جميع الحلقات ({{ $allEpisodes->count() }})
                </summary>
                <div class="mt-2 max-h-64 overflow-y-auto rounded-xl bg-slate-800/50 border border-slate-700/50 p-2 space-y-1">
                    @foreach($allEpisodes as $ep)
                        <a href="{{ route('watch.show', [$content, 'episode' => $ep->id]) }}"
                           class="flex items-center gap-3 p-2.5 rounded-lg transition min-h-[44px] {{ $ep->id === $episode->id ? 'bg-rose-600/20 text-rose-400' : 'hover:bg-slate-700/50 text-slate-300' }}">
                            <span class="shrink-0 w-8 h-8 rounded-lg bg-slate-700/80 flex items-center justify-center text-xs font-medium">
                                {{ $ep->episode_number }}
                            </span>
                            <span class="truncate">{{ $ep->title ?? 'الحلقة ' . $ep->episode_number }}</span>
                            @if($ep->id === $episode->id)
                                <span class="shrink-0 text-xs text-rose-400">جاري المشاهدة</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </details>
        @endif
    @endif
    @if($content->description)
        <p class="text-slate-400 mt-4 text-sm break-words">{{ Str::limit($content->description, 200) }}</p>
    @endif
    <a href="{{ route('content.show', $content) }}" class="inline-flex items-center min-h-[44px] py-2 mt-4 text-slate-400 hover:text-white text-sm transition touch-manipulation">← العودة إلى {{ $content->title }}</a>
</div>
@endsection
