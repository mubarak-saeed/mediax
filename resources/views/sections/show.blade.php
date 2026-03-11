@extends('layouts.app')

@section('title', isset($subsection) ? "{$subsection->name} - {$section->name}" : $section->name)

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">

    @if(isset($subsection))
    {{-- صفحة القسم الفرعي: عرض المحتوى مع الفلاتر --}}
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('sections.index') }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">الأقسام</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('sections.show', $section->slug) }}"
            class="hover:text-white transition py-2 min-h-11 inline-flex items-center touch-manipulation">{{
            $section->name }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">{{ $subsection->name }}</span>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ $subsection->name }}</h1>
        <a href="{{ route('sections.show', $section->slug) }}"
            class="min-h-11 inline-flex items-center text-slate-400 hover:text-white transition text-sm py-2.5 px-3 rounded-lg active:bg-slate-800/50 touch-manipulation">←
            {{ $section->name }}</a>
    </div>

    <form method="get" action="{{ route('sections.subsection.show', [$section->slug, $subsection->slug]) }}"
        class="mb-6 sm:mb-8 p-4 sm:p-5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
        <p class="text-slate-400 text-sm font-medium mb-3 sm:mb-4" id="filter-label">تصفية وترتيب</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="filter-genre" class="sr-only">النوع الفني</label>
                <select name="genre" id="filter-genre" aria-labelledby="filter-label"
                    class="w-full min-h-11 rounded-xl bg-slate-800 border border-slate-600 text-white px-4 py-2.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                    <option value="">كل الأنواع</option>
                    @foreach($genres as $genre)
                    <option value="{{ $genre->id }}" @selected(request('genre')==(string) $genre->id)>{{ $genre->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="filter-year" class="sr-only">السنة</label>
                <select name="year" id="filter-year" aria-labelledby="filter-label"
                    class="w-full min-h-11 rounded-xl bg-slate-800 border border-slate-600 text-white px-4 py-2.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                    <option value="">كل السنوات</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" @selected(request('year')==(string) $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="filter-sort" class="sr-only">ترتيب النتائج</label>
                <select name="sort" id="filter-sort" aria-labelledby="filter-label"
                    class="w-full min-h-11 rounded-xl bg-slate-800 border border-slate-600 text-white px-4 py-2.5 text-base focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                    <option value="latest" @selected(request('sort', 'latest' )==='latest' )>الأحدث إضافة</option>
                    <option value="oldest" @selected(request('sort')==='oldest' )>الأقدم إضافة</option>
                    <option value="title" @selected(request('sort')==='title' )>بالاسم (أ–ي)</option>
                    <option value="title_desc" @selected(request('sort')==='title_desc' )>بالاسم (ي–أ)</option>
                    <option value="year_desc" @selected(request('sort')==='year_desc' )>السنة (الأحدث)</option>
                    <option value="year_asc" @selected(request('sort')==='year_asc' )>السنة (الأقدم)</option>
                    <option value="views_desc" @selected(request('sort')==='views_desc' )>الأكثر مشاهدة</option>
                    <option value="views_asc" @selected(request('sort')==='views_asc' )>الأقل مشاهدة</option>
                    <option value="rating_desc" @selected(request('sort')==='rating_desc' )>الأعلى تقييماً</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit"
                    class="flex-1 min-w-30 min-h-11 bg-rose-600 hover:bg-rose-500 text-white px-4 py-3 rounded-xl text-sm font-medium transition active:scale-[0.98] touch-manipulation">تطبيق</button>
                @if(request()->hasAny(['genre', 'year', 'sort']) && (request('sort') !== 'latest' ||
                request()->has('genre') || request()->has('year')))
                <a href="{{ route('sections.subsection.show', [$section->slug, $subsection->slug]) }}"
                    class="min-h-11 inline-flex items-center justify-center text-slate-400 hover:text-white text-sm py-3 px-4 rounded-xl hover:bg-slate-700/50 transition touch-manipulation">إلغاء</a>
                @endif
            </div>
        </div>
    </form>

    @if($contents->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5 min-w-0">
        @foreach($contents as $content)
        @include('components.content-card', [
        'content' => $content,
        'showLatestEpisode' => $subsection->type === 'series' || $subsection->type === 'movieserial',
        'showSeriesMeta' => $subsection->type === 'series' || $subsection->type === 'movieserial',
        ])
        @endforeach
    </div>
    <div
        class="mt-8 flex justify-center overflow-x-auto py-2 [&_nav]:flex [&_nav]:flex-wrap [&_nav]:gap-2 [&_nav]:justify-center">
        {{ $contents->links() }}
    </div>
    @else
    <div class="text-center py-16 text-slate-500 rounded-xl bg-slate-800/30 border border-slate-700/50">
        <p>لا يوجد محتوى في هذا القسم حالياً.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-4">
            <a href="{{ route('sections.show', $section->slug) }}"
                class="min-h-11 inline-flex items-center py-2.5 px-4 rounded-xl text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-700 transition touch-manipulation">العودة
                لـ {{ $section->name }}</a>
            <a href="{{ route('sections.index') }}"
                class="min-h-11 inline-flex items-center py-2.5 px-4 rounded-xl text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-700 transition touch-manipulation">العودة
                للأقسام</a>
        </div>
    </div>
    @endif

    @else
    {{-- صفحة القسم الرئيسي: شبكة الأقسام الفرعية فقط --}}
    <div class="flex items-center justify-end mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ $section->name }}</h1>
    </div>

    @if($section->subSections->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
        @foreach($section->subSections as $sub)
        <a href="{{ route('sections.subsection.show', [$section->slug, $sub->slug]) }}" class="group block group/card">
            <div
                class="relative aspect-4/3 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700/50 group-hover/card:border-rose-500/40 shadow-lg shadow-black/20 group-hover/card:shadow-xl group-hover/card:shadow-rose-500/10 transition-all duration-300 group-hover/card:scale-[1.02]">
                @if($sub->poster_url)
                <img src="{{ $sub->resolved_poster_url }}" alt="{{ $sub->name }}"
                    class="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500 ease-out"
                    loading="lazy">
                @else
                <div
                    class="w-full h-full flex items-center justify-center bg-linear-to-br from-slate-700 via-slate-800 to-slate-900 group-hover/card:from-rose-950/40 group-hover/card:via-slate-800 group-hover/card:to-slate-900 transition-colors duration-300">
                    @if($sub->type === 'movie')
                    <svg class="w-12 h-12 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    @elseif($sub->type === 'series' || $sub->type === 'movieserial')
                    <svg class="w-12 h-12 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    @else
                    <svg class="w-12 h-12 text-slate-500 group-hover/card:text-rose-400/80 transition-colors"
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
                @if($sub->contents_count>=0)
                <div
                    class="absolute top-2 right-2 bg-rose-600/95 text-white text-xs font-semibold px-2.5 py-1 rounded-lg shadow-lg backdrop-blur-sm">
                    {{ $sub->contents_count }} عنصر
                </div>
                @endif
                <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-4 flex items-center justify-between gap-2">
                    <h2 class="text-xs sm:text-sm font-bold text-white drop-shadow-lg line-clamp-2 flex-1 min-w-0">{{
                        $sub->name }}</h2>
                    <span
                        class="shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 group-hover/card:bg-rose-500 flex items-center justify-center transition-colors duration-300"
                        aria-hidden="true">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
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
        <p>لا توجد أقسام فرعية في هذا القسم حالياً.</p>
        <a href="{{ route('sections.index') }}"
            class="mt-4 min-h-11 inline-flex items-center py-2.5 px-4 rounded-xl text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-700 transition touch-manipulation">العودة
            للأقسام</a>
    </div>
    @endif
    @endif
</div>
@endsection