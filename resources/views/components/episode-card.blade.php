@props(['episode', 'season', 'content', 'showFavoriteButton' => false])

<article
    class="rounded-xl overflow-hidden bg-slate-800/50 border border-slate-700/50 hover:border-slate-600 transition group/article">
    <a href="{{ route('episodes.show', [$content, $season, $episode]) }}" class="block">
        <div class="aspect-video relative overflow-hidden bg-slate-800">
            <img src="{{ $episode->resolved_poster_url ?? $season->resolved_poster_url ?? $content->resolved_poster_url ?? 'https://placehold.co/640x360/1e293b/94a3b8?text=' . urlencode($episode->title ?? 'ح' . $episode->episode_number) }}"
                alt="{{ $episode->title ?? 'الحلقة ' . $episode->episode_number }}" loading="lazy"
                class="w-full h-full object-cover group-hover/article:scale-105 transition duration-300">
            <span class="absolute bottom-2 left-2 bg-slate-900/90 text-xs px-2 py-0.5 rounded">ح{{
                $episode->episode_number }}</span>
        </div>
        <div class="p-3 flex justify-between items-center">
            <div>
                <h3 class="font-medium text-sm line-clamp-2 group-hover/article:text-rose-400 transition"> م{{ $season->season ?? '?' }} — {{
                    $episode->title ?? 'الحلقة
                    ' . $episode->episode_number }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $content->title }}</p>
               
            </div>
            <div class="px-3 pb-3 flex  justify-end">
                <livewire:favorite-button :content-id="$content->id" :season-id="$season->id"
                    :episode-id="$episode->id" />
            </div>
        </div>
    </a>
    @if($showFavoriteButton)

    @endif
</article>