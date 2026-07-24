<?php

use App\Models\Content;
use Livewire\Component;

new class extends Component
{
    public string $query = '';

    public function updatedQuery(): void
    {
        $this->dispatch('search-updated');
    }

    public function getResultsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Content::query()
            ->where(fn ($q) => $q->where('title', 'like', '%'.$this->query.'%')
                ->orWhere('description', 'like', '%'.$this->query.'%'))
            ->with('genres')
            ->limit(10)
            ->get();
    }
};
?>

<div class="relative w-full max-w-full sm:max-w-xs" x-data="{ open: false }" @click.away="open = false">
    <input type="search" wire:model.live.debounce.300ms="query" @focus="open = true"
        placeholder="ابحث عن فيلم، مسلسل، كتاب..."
        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white px-4 py-2.5 text-base sm:text-sm focus:ring-2 focus:ring-rose-500 focus:border-transparent min-h-10"
        aria-label="البحث">
    @if(strlen($query) >= 2)
    <div x-show="open" x-transition
        class="absolute top-full left-0 right-0 mt-1 rounded-lg bg-slate-800 border border-slate-700 shadow-xl z-50 max-h-96 overflow-y-auto">
        @forelse($this->results as $content)
        <a href="{{ route('content.show', $content) }}" class="flex gap-3 p-3 hover:bg-slate-700/50 transition block">
            <img src="{{ $content->resolved_poster_url ?? 'https://placehold.co/80x120?text=' . urlencode($content->title) }}"
                alt="" class="w-12 h-18 object-cover rounded shrink-0">
            <div>
                <p class="font-medium text-sm">{{ $content->title }}</p>
                <p class="text-xs text-slate-500">{{ $content->type?->getLabel() ?? $content->type?->value ?? '—' }} •
                    {{ $content->release_year ?? '-' }}</p>
            </div>
        </a>
        @empty
        <p class="p-4 text-slate-500 text-sm">لا توجد نتائج</p>
        @endforelse
        @if($this->results->isNotEmpty())
        <a href="{{ route('search', ['q' => $query]) }}"
            class="block p-3 text-center text-rose-500 hover:bg-slate-700/50 text-sm font-medium border-t border-slate-700">
            عرض كل النتائج
        </a>
        @endif
    </div>
    @endif
</div>