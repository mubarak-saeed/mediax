@props(['season', 'content', 'showFavoriteButton' => false])

<div class="group rounded-xl overflow-hidden bg-slate-800/50 border border-slate-700/50 hover:border-slate-600 transition">
    <a href="{{ route('seasons.show', [$content, $season]) }}" class="block">
        <div class="aspect-2/3 relative overflow-hidden bg-slate-800">
            <img src="{{ $season->resolved_poster_url ?? $content->resolved_poster_url ?? 'https://placehold.co/400x600/1e293b/94a3b8?text=' . urlencode($season->title ?? 'م' . $season->season) }}"
                 alt="{{ $season->title ?? 'الموسم ' . $season->season }}"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            <span class="absolute bottom-2 left-2 right-2 bg-slate-900/90 text-center text-xs py-1.5 px-2 rounded">
                {{ $season->episodes_count ?? optional($season->episodes)->count() ?? 0 }} حلقة
            </span>
        </div>
        <div class="p-3">
            <h3 class="font-medium text-sm line-clamp-2 group-hover:text-rose-400 transition">{{ $season->title ?? 'الموسم ' . $season->season }}</h3>
        </div>
    </a>
    @if($showFavoriteButton)
        <div class="px-3 pb-3 flex justify-end">
            <livewire:favorite-button :content-id="$content->id" :season-id="$season->id" />
        </div>
    @endif
</div>
