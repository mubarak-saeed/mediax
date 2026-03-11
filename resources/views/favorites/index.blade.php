@extends('layouts.app')

@section('title', 'المفضلة')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('profile') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الملف الشخصي</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200">المفضلة</span>
    </nav>

    <div class="flex flex-col gap-4 mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold">المفضلة</h1>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
            {{-- تصفية حسب النوع --}}
            <div class="flex flex-wrap gap-2" role="group" aria-label="نوع المفضلة">
                <span class="text-slate-500 text-sm w-full sm:w-auto py-1.5">النوع:</span>
                @foreach(['all' => 'الكل', 'content' => 'محتوى', 'season' => 'موسم', 'episode' => 'حلقة'] as $key => $label)
                    <a href="{{ route('favorites', ['type' => $key, 'sort' => $sort]) }}"
                       class="min-h-[44px] inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition touch-manipulation {{ $typeFilter === $key ? 'bg-rose-600 text-white' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700/80' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <span class="w-px h-6 bg-slate-600 hidden sm:block shrink-0" aria-hidden="true"></span>
            {{-- ترتيب --}}
            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="ترتيب">
                <span class="text-slate-500 text-sm py-1.5">ترتيب:</span>
                <a href="{{ route('favorites', ['type' => $typeFilter, 'sort' => 'newest']) }}"
                   class="min-h-[44px] inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition touch-manipulation {{ $sort === 'newest' ? 'bg-slate-700 text-white' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700/80' }}">
                    الأحدث
                </a>
                <a href="{{ route('favorites', ['type' => $typeFilter, 'sort' => 'oldest']) }}"
                   class="min-h-[44px] inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition touch-manipulation {{ $sort === 'oldest' ? 'bg-slate-700 text-white' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700/80' }}">
                    الأقدم
                </a>
            </div>
        </div>
    </div>

    @if($favorites->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 min-w-0">
            @foreach($favorites as $fav)
                @if($fav->isEpisode())
                    <x-episode-card :episode="$fav->episode" :season="$fav->season" :content="$fav->content" :show-favorite-button="true" />
                @elseif($fav->isSeason())
                    <x-season-card :season="$fav->season" :content="$fav->content" :show-favorite-button="true" />
                @else
                    @include('components.content-card', [
                        'content' => $fav->content,
                        'showLatestEpisode' => in_array($fav->content->type?->value ?? '', ['series', 'movieserial']),
                        'showSeriesMeta' => in_array($fav->content->type?->value ?? '', ['series', 'movieserial']),
                    ])
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-16 rounded-xl bg-slate-800/30 border border-slate-700/50 text-slate-500">
            <p class="text-slate-500 mb-4">
                @if($typeFilter !== 'all')
                    لا توجد عناصر في المفضلة تطابق التصفية المختارة.
                @else
                    لم تضف أي محتوى للمفضلة بعد.
                @endif
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center py-2.5 px-4 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">الرئيسية</a>
                <a href="{{ route('sections.index') }}" class="min-h-[44px] inline-flex items-center py-2.5 px-4 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 hover:bg-slate-800 transition touch-manipulation">جميع الأقسام</a>
            </div>
        </div>
    @endif
</div>
@endsection
