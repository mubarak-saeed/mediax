@extends('layouts.app')

@section('title', strlen($query) >= 2 ? 'نتائج البحث: ' . $query : 'البحث')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    {{-- مسار التنقل --}}
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">{{ strlen($query) >= 2 ? 'نتائج البحث' : 'البحث' }}</span>
    </nav>
    <div class="flex flex-col gap-4 mb-6 sm:mb-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold break-words min-w-0">
                @if(strlen($query) >= 2)
                    نتائج البحث: «{{ $query }}»
                @else
                    البحث
                @endif
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0">
                <a href="{{ route('sections.index') }}" class="min-h-[44px] inline-flex items-center text-slate-400 hover:text-white transition text-sm py-2.5 px-3 rounded-lg touch-manipulation">جميع الأقسام</a>
                <a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center text-slate-400 hover:text-white transition text-sm py-2.5 px-3 rounded-lg touch-manipulation">← الرئيسية</a>
            </div>
        </div>
        @if(strlen($query) >= 2)
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-slate-500 text-sm">ترتيب:</span>
                @foreach(['latest' => 'الأحدث', 'oldest' => 'الأقدم', 'title' => 'بالاسم', 'year_desc' => 'السنة', 'views_desc' => 'الأكثر مشاهدة', 'rating_desc' => 'الأعلى تقييماً'] as $sortKey => $sortLabel)
                    <a href="{{ route('search', ['q' => $query, 'sort' => $sortKey]) }}" class="min-h-[40px] sm:min-h-[44px] inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition touch-manipulation {{ ($sort ?? 'latest') === $sortKey ? 'bg-rose-600 text-white' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700/80' }}">{{ $sortLabel }}</a>
                @endforeach
            </div>
        @endif
    </div>

    @if(strlen($query) < 2)
        <div class="rounded-xl bg-slate-800/30 border border-slate-700/50 p-8 text-center text-slate-500">
            <p>أدخل كلمتين على الأقل للبحث في الأفلام، المسلسلات والكتب.</p>
            <p class="mt-2 text-sm">يمكنك أيضاً استخدام شريط البحث في الأعلى.</p>
        </div>
    @elseif($contents->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5 min-w-0">
            @foreach($contents as $content)
                @include('components.content-card', [
                    'content' => $content,
                    'showLatestEpisode' => in_array($content->type?->value ?? '', ['series', 'movieserial']),
                ])
            @endforeach
        </div>
        <div class="mt-8 flex justify-center overflow-x-auto py-2 [&_nav]:flex [&_nav]:flex-wrap [&_nav]:gap-2 [&_nav]:justify-center">
            {{ $contents->links() }}
        </div>
    @else
        <div class="text-center py-16 text-slate-500 rounded-xl bg-slate-800/30 border border-slate-700/50">
            <p>لا توجد نتائج لـ «{{ $query }}».</p>
            <div class="flex flex-wrap justify-center gap-3 mt-4">
                <a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center py-2.5 px-4 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">الرئيسية</a>
                <a href="{{ route('sections.index') }}" class="min-h-[44px] inline-flex items-center py-2.5 px-4 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">جميع الأقسام</a>
            </div>
        </div>
    @endif
</div>
@endsection
