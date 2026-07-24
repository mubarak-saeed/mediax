<?php

use App\Models\Content;
use App\Models\SubSection;
use Livewire\Component;

new class extends Component
{
    public function getFeaturedProperty()
    {
        $contents = Content::query()
            ->featured()
            ->with('genres')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->latest()
            ->get();
        $featured = $contents->filter(function (Content $content) {
            if ($content->isSeriesOrMovieSerial()) {
                return $content->episodes()->whereNotNull('file_url')->exists();
            }
            return $content->file_url !== null;
        })->values();
        return $featured;
    }

    /**
     * صفوف الأقسام الفرعية: كل قسم فرعي يظهر مع أحدث محتوياته.
     *
     * @return array<int, array{subsection: SubSection, contents: \Illuminate\Database\Eloquent\Collection}>
     */
    public function getSectionRowsProperty(): array
    {
        return SubSection::query()
            ->whereHas('contents')
            ->whereHas('section', fn($q) => $q->where('status', 'active'))
            ->with('section')
            ->withCount('contents')
            ->orderBy('order')
            ->get()
            ->sortBy(fn(SubSection $s) => ($s->section?->order ?? 999) * 1000 + $s->order)
            ->values()
            ->map(function (SubSection $subsection): array {
                $query = Content::query()
                    ->where('sub_section_id', $subsection->id)
                    ->with('genres')
                    ->withCount('ratings')
                    ->withAvg('ratings', 'rating');

                if ($subsection->type === 'series' || $subsection->type === 'movieserial') {
                    $query
                        ->whereHas('episodes', fn($q) => $q->where('file_url', '!=', null))
                        ->with(['seasons.episodes'])
                        ->orderByLatestEpisode();
                } else {
                    $query
                        ->whereNotNull('file_url')
                        ->latest()->limit(20)->get();
                }

                return [
                    'subsection' => $subsection,
                    'contents' => $query->latest()->get(),
                ];
            })
            ->filter(fn(array $row): bool => $row['contents']->isNotEmpty())
            ->values()
            ->all();
    }
};
?>

<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    {{-- Hero: Featured (محتوى مميز) --}}
    @if($this->featured->isNotEmpty())
    <section class="mb-8 sm:mb-12">
        <h2 class="sr-only">محتوى مميز</h2>
        <div class="relative h-56 sm:h-112 lg:h-128 rounded-xl overflow-hidden bg-slate-800 shadow-2xl">
            @foreach($this->featured as $index => $item)
            <a href="{{ route('content.show', $item) }}"
                class="absolute inset-0 block {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }} transition-opacity duration-500">
                <img src="{{ $item->resolved_poster_url ?? 'https://placehold.co/1280x720/1e293b/94a3b8?text=' . urlencode($item->title) }}"
                    alt="{{ $item->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-linear-to-t from-slate-900/95 via-slate-900/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-2 text-sm">
                        @if(($item->views_count ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 text-slate-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ number_format($item->views_count) }} مشاهدة
                        </span>
                        @endif
                        @if($item->average_rating !== null && $item->average_rating > 0)
                        <span class="inline-flex items-center gap-1 text-amber-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-semibold text-white">{{ number_format($item->average_rating, 1) }}</span>
                        </span>
                        @endif
                    </div>
                    <h3 class="text-xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2 line-clamp-2">{{ $item->title }}
                    </h3>
                    <p class="text-slate-300 line-clamp-2 max-w-2xl text-xs sm:text-sm lg:text-base hidden sm:block">{{
                        Str::limit($item->description, 120) }}</p>
                    <span
                        class="inline-flex items-center justify-center min-h-11 mt-3 sm:mt-4 bg-rose-600 hover:bg-rose-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition active:scale-[0.98] touch-manipulation">عرض
                        المزيد</span>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- صفوف الأقسام الفرعية: الجديد في كل قسم فرعي --}}
    @foreach($this->sectionRows as $row)
    <section class="mb-8 sm:mb-10">
        <div class="flex items-center justify-between gap-3 mb-3 sm:mb-4">
            <h2 class="text-lg sm:text-xl font-bold">الجديد في {{ $row['subsection']->name }}</h2>
            @if($row['subsection']->section)
            <a href="{{ route('sections.subsection.show', [$row['subsection']->section->slug, $row['subsection']->slug]) }}"
                class="shrink-0 min-h-11 inline-flex items-center gap-1 text-rose-500 hover:text-rose-400 font-medium text-sm transition py-2.5 px-3 rounded-lg active:bg-slate-800/50 touch-manipulation">
                عرض المزيد
                <span aria-hidden="true">←</span>
            </a>
            @endif
        </div>
        <div
            class="flex gap-3 sm:gap-4 overflow-x-auto pb-2 -mx-3 px-3 sm:-mx-1 sm:px-1 snap-x snap-mandatory scrollbar-hide touch-pan-x">
            @foreach($row['contents'] as $content)
            <div class="shrink-0 w-45 snap-start">
                @include('components.content-card', [
                'content' => $content,
                'showLatestEpisode' => $row['subsection']->type === 'series' || $row['subsection']->type ===
                'movieserial',
                ])
            </div>
            @endforeach
        </div>
    </section>
    @endforeach

    @if($this->featured->isEmpty() && empty($this->sectionRows))
    <div class="text-center py-16 text-slate-500 rounded-xl bg-slate-800/30 border border-slate-700/50">
        <p>لا يوجد محتوى لعرضه حالياً.</p>
        <a href="{{ route('home') }}"
            class="mt-3 min-h-11 inline-flex items-center py-2.5 px-4 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 transition touch-manipulation">الرئيسية</a>
    </div>
    @endif
</div>