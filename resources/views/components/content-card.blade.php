@props(['content', 'showLatestEpisode' => false, 'showSeriesMeta' => false])

@php
$avgRating = $content->average_rating ?? null;
$viewsCount = $content->views_count ?? 0;
$typeValue = $content->type?->value ?? null;
$isSeriesLike = in_array($typeValue, ['series', 'movieserial'], true);
$seasonsCount = $content->relationLoaded('seasons') ? $content->seasons->count() : 0;
$episodesCount = $content->relationLoaded('seasons')
? $content->seasons->sum(fn ($s) => $s->episodes_count ?? ($s->relationLoaded('episodes') ? $s->episodes->count() : 0))
: 0;
@endphp

<a href="{{ route('content.show', $content) }}"
    class="group rounded-2xl overflow-hidden bg-slate-800/50 hover:bg-slate-800 transition border border-slate-700/50 hover:border-rose-500/40 shadow-lg shadow-black/20 hover:shadow-xl hover:shadow-rose-500/5 active:scale-[0.98] min-h-65 sm:min-h-0 flex flex-col">
    <div class="aspect-2/3  relative overflow-hidden">
        <img src="{{ $content->resolved_poster_url ?? 'https://placehold.co/400x600/1e293b/94a3b8?text=' . urlencode($content->title) }}"
            alt="{{ $content->title }}" loading="lazy"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
        {{-- تدرج سفلي خفيف للقراءة --}}
        <div
            class="absolute inset-0 bg-linear-to-t from-slate-900/60 via-transparent to-transparent pointer-events-none">
        </div>
        @if($content->release_year)
        <span
            class="absolute top-2 right-2 bg-slate-900/90 text-xs font-medium px-2 py-0.5 rounded-lg backdrop-blur-sm">{{
            $content->release_year }}</span>
        @endif
        {{-- التقييم والمشاهدات في الأعلى --}}
        <div class="absolute top-2 left-2 flex flex-wrap items-center gap-1.5">
            @if($avgRating !== null && $avgRating > 0)
            <span
                class="inline-flex  items-center gap-0.5 bg-amber-500/90 text-slate-900 text-[10px] sm:text-xs font-bold px-1.5 py-0.5 rounded-md backdrop-blur-sm">
                <svg class="w-3 h-3  sm:w-3.5 sm:h-3.5 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ number_format($avgRating, 1) }}
            </span>
            @endif
            @if($viewsCount > 0 )
            <span
                class="inline-flex items-center gap-0.5 bg-slate-900/80 text-slate-200 text-[10px] sm:text-xs px-1.5 py-0.5 rounded-md backdrop-blur-sm">
                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ $viewsCount >= 1000 ? number_format($viewsCount / 1000, 1) . 'k' : $viewsCount }}
            </span>
            @endif
        </div>
        @if($showSeriesMeta && $isSeriesLike && $content->relationLoaded('seasons') && $seasonsCount > 0)
        <span
            class="absolute bottom-2 left-2 right-2 bg-slate-900/90 text-white text-xs px-2 py-1.5 rounded-lg text-center backdrop-blur-sm font-medium">
            {{ $seasonsCount }} موسم · {{ $episodesCount }} حلقة
        </span>
        @elseif($showLatestEpisode && $isSeriesLike && $content->newest_episode)
        @php $ep = $content->newest_episode; $season = $ep->season; @endphp
        <span
            class="absolute bottom-2 left-2 right-2 bg-rose-600/95 text-white text-xs px-2 py-1 rounded-lg text-center font-medium backdrop-blur-sm">
            موسم{{' '. $season?->season ?? '?' }} الحلقة{{' '. $ep->episode_number }}
        </span>
        @endif
    </div>
    <div class="p-2.5 sm:p-3 flex-1 flex flex-col justify-center">
        <h3 class="font-semibold text-sm line-clamp-1 group-hover:text-rose-400 transition">{{ $content->title }}</h3>
        @if($content->genres->isNotEmpty())
        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $content->genres->pluck('name')->take(2)->join('، ') }}
        </p>
        @endif
    </div>
</a>