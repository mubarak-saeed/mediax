@extends('layouts.app')

@section('title', $content->title)

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-4 sm:mb-6 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('sections.index') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الأقسام</a>
        @if($content->subsection)
        <span aria-hidden="true">/</span>
        <a href="{{ route('sections.show', $content->subsection->section->slug) }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">{{
            $content->subsection->section->name }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('sections.subsection.show', [$content->subsection->section->slug, $content->subsection->slug]) }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">{{
            $content->subsection->name }}</a>
        @endif
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 truncate max-w-45 sm:max-w-none py-2" title="{{ $content->title }}">{{
            $content->title }}</span>
    </nav>
    <div class="flex flex-col lg:flex-row gap-6 sm:gap-8">
        <div class="w-full max-w-xs mx-auto lg:mx-0 lg:w-1/3 shrink-0">
            <div class="aspect-2/3 rounded-xl overflow-hidden bg-slate-800 shadow-xl">
                <img src="{{ $content->resolved_poster_url ?? 'https://placehold.co/400x600/1e293b/94a3b8?text=' . urlencode($content->title) }}"
                    alt="{{ $content->title }}" class="w-full h-full object-cover">
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-bold wrap-break-word">{{ $content->title }}</h1>
                <livewire:favorite-button :content-id="$content->id" />
            </div>
            {{-- معلومات حسب النوع: مسلسل = مواسم/حلقات/أنواع | فيلم = سنة/مدة/أنواع | كتاب = سنة/أنواع --}}
            <div class="flex flex-wrap gap-2 text-slate-400 text-sm mb-2">
                @if($content->type)
                <span class="font-medium text-rose-400/90">{{ $content->type->getLabel() }}</span>
                <span>·</span>
                @endif
                @if($content->release_year)
                <span>{{ $content->release_year }}</span>
                @endif
                @if($content->genres->isNotEmpty())
                <span>·</span>
                <span>{{ $content->genres->pluck('name')->join('، ') }}</span>
                @endif
                @if($content->isSeriesOrMovieSerial() && $content->seasons->isNotEmpty())
                @php
                $totalEpisodes = $content->seasons->sum(fn ($s) => $s->episodes_count ?? $s->episodes->count());
                @endphp
                <span>·</span>
                <span>{{ $content->seasons->count() }} موسم · {{ $totalEpisodes }} حلقة</span>
                @endif
                @if($content->duration)
                @php
                $rawDuration = $content->duration;
                $seconds = is_numeric($rawDuration) ? (int) $rawDuration : null;

                if ($seconds === null && $content->file_url) {
                try {
                $disk = 'local';
                if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($content->file_url)) {
                $absolutePath = \Illuminate\Support\Facades\Storage::disk($disk)->path($content->file_url);
                $seconds = app(\App\Services\MediaMetadataService::class)->getVideoDurationMinutes($absolutePath);
                }
                } catch (\Throwable $e) {
                $seconds = null;
                }
                }
                @endphp
                @if($seconds)
                <span>·</span>
                <span>{{ app(\App\Services\MediaMetadataService::class)->formatDurationToHoursMinutes($seconds)
                    }}</span>
                @endif
                @endif
            </div>
            {{-- المشاهدات والتقييم (للأفلام والكتب فقط — المسلسلات تعرضها على مستوى الحلقات) --}}
            @if($content->views_count)
            <div class="flex flex-wrap items-center gap-4 text-sm mb-4">
                @if(($content->views_count ?? 0) > 0)
                <span class="inline-flex items-center gap-1.5 text-slate-400">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($content->views_count) }} {{ $content->views_count === 1 ? 'مشاهدة' : 'مشاهدات' }}
                </span>
                @endif
                @if($content->average_rating !== null && $content->average_rating > 0)
                <span class="inline-flex items-center gap-1.5 text-amber-400">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold text-white">{{ number_format($content->average_rating, 1) }}</span>
                    <span class="text-slate-500">({{ number_format($content->ratings_count) }} تقييم)</span>
                </span>
                @endif
                <livewire:rating-button :content-id="$content->id" />
            </div>
            @else
            {{-- للمسلسلات: التقييم فقط (المشاهدات على مستوى الحلقات) --}}
            <div class="flex flex-wrap items-center gap-4 text-sm mb-4">
                @if($content->average_rating !== null && $content->average_rating > 0)
                <span class="inline-flex items-center gap-1.5 text-amber-400">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold text-white">{{ number_format($content->average_rating, 1) }}</span>
                    <span class="text-slate-500">({{ number_format($content->ratings_count) }} تقييم)</span>
                </span>
                @endif
                <livewire:rating-button :content-id="$content->id" />
            </div>
            @endif
            @if($content->description)
            <p class="text-slate-300 leading-relaxed mb-6">{{ $content->description }}</p>
            @endif

            {{-- Access buttons: touch-friendly on mobile --}}
            <div class="flex flex-wrap gap-3">
                @auth
                @if(auth()->user()->canAccessContent($content))
                @if($content->type->value === 'book')
                <a href="{{ route('read.show', $content) }}"
                    class="bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 flex items-center justify-center active:scale-[0.98]">قراءة
                    الآن</a>
                @if($downloadUrl)
                <a href="{{ $downloadUrl }}"
                    class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 flex items-center justify-center"
                    target="_blank" download>تحميل ({{
                    app(\App\Services\MediaMetadataService::class)->formatFileSize($content->file_size) }})</a>
                @endif
                @elseif($content->type->value === 'series' || $content->type->value === 'movieserial')
                <a href="javascript:void(0)" onclick="scrollToSeasons()"
                    class="bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 flex items-center justify-center active:scale-[0.98]">مشاهدة
                    الآن</a>
                <script>
                    function scrollToSeasons() {
                                    const seasons = document.getElementById('seasons');
                                    if (seasons) {
                                        seasons.scrollIntoView({ behavior: 'smooth' });
                                    }
                                }
                </script>
                @else
                <a href="{{ route('watch.show', $content) }}"
                    class="bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 flex items-center justify-center active:scale-[0.98]">مشاهدة
                    الآن</a>
                @if($downloadUrl)
                <a href="{{ $downloadUrl }}"
                    class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 flex items-center justify-center"
                    target="_blank" download>تحميل ({{
                    app(\App\Services\MediaMetadataService::class)->formatFileSize($content->file_size) }})</a>
                @endif
                @endif
                @else
                <a href="{{ route('subscription.plans') }}"
                    class="inline-flex bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 items-center justify-center active:scale-[0.98]">
                    @if($content->access_level_required->value === 'free')
                    تسجيل الدخول للوصول
                    @elseif($content->type->value === 'book')
                    اشترك للقراءة
                    @elseif($content->type->value === 'series')
                    اشترك لمشاهدة المسلسل
                    @elseif($content->type->value === 'movieserial')
                    اشترك لمشاهدة الفيلم
                    @else
                    اشترك لمشاهدة الفيلم
                    @endif
                </a>
                @endif
                @else
                <a href="{{ route('login', ['intended' => url()->current()]) }}"
                    class="inline-flex bg-rose-600 hover:bg-rose-500 text-white px-6 py-3 rounded-xl font-medium transition text-center min-h-11 items-center justify-center active:scale-[0.98]">
                    @if($content->access_level_required->value === 'free')
                    تسجيل الدخول للوصول
                    @elseif($content->type->value === 'book')
                    تسجيل الدخول للاشتراك والقراءة
                    @elseif($content->type->value === 'series')
                    تسجيل الدخول للاشتراك ومشاهدة المسلسل
                    @elseif($content->type->value === 'movieserial')
                    تسجيل الدخول للاشتراك ومشاهدة الفيلم
                    @else
                    تسجيل الدخول للاشتراك ومشاهدة الفيلم
                    @endif
                </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- المواسم (للمسلسلات فقط): بوسترات + عدد الحلقات + رابط لصفحة الموسم --}}
    @if($content->isSeriesOrMovieSerial() && $content->seasons->isNotEmpty())
    <section class="mt-8 sm:mt-12" id="seasons">
        <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">المواسم</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
            @foreach($content->seasons->sortBy(fn ($s) => (int) $s->season) as $season)
            <x-season-card :season="$season" :content="$content" :show-favorite-button="auth()->check()" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Related --}}
    @if($related->isNotEmpty())
    <section class="mt-8 sm:mt-12">
        <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">أعمال مشابهة</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($related as $item)
            @include('components.content-card', [
            'content' => $item,
            'showSeriesMeta' => in_array($item->type?->value ?? '', ['series', 'movieserial']),
            ])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection