@extends('layouts.app')

@section('title', 'قراءة: ' . $content->title)

@section('content')
<div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8" x-data="readProgress()" x-init="init()">
    {{-- شريط تقدم القراءة ثابت في الأعلى --}}
    <div class="sticky top-[calc(4rem+env(safe-area-inset-top,0px))] sm:top-[calc(4.25rem+env(safe-area-inset-top,0px))] z-40 -mx-3 px-3 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 -mt-5 pt-5 pb-2 bg-slate-950/95 backdrop-blur-sm border-b border-slate-800/50 mb-4">
        <div class="flex items-center justify-between gap-2 mb-1.5">
            <span class="text-xs font-medium text-slate-400">تقدم القراءة</span>
            <span class="text-xs font-semibold text-rose-400" x-text="Math.round(progress) + '%'">0%</span>
        </div>
        <div class="h-1.5 rounded-full bg-slate-800 overflow-hidden">
            <div class="h-full bg-rose-600 rounded-full transition-all duration-200" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>

    <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400 mb-3 sm:mb-4 min-w-0" aria-label="مسار التنقل">
        <a href="{{ route('home') }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center touch-manipulation">الرئيسية</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('content.show', $content) }}" class="hover:text-white transition py-2 min-h-[44px] inline-flex items-center truncate max-w-[150px] sm:max-w-none touch-manipulation">{{ $content->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-200 py-2">قراءة</span>
    </nav>
    <div class="rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] min-h-[50vh] sm:min-h-[600px] w-full">
        @if(str_ends_with(strtolower($content->file_url ?? ''), '.pdf'))
            {{-- toolbar=0 يخفّي شريط أدوات المتصفح (تحميل/طباعة). الكتب للقراءة فقط. --}}
            <iframe src="{{ $streamUrl }}#toolbar=0&navpanes=0" class="w-full h-full" title="{{ $content->title }}"></iframe>
        @else
            <div class="flex flex-col items-center justify-center h-full text-slate-500 p-4 text-center">
                <p class="mb-3">افتح الملف للقراءة:</p>
                <a href="{{ $streamUrl }}#toolbar=0&navpanes=0" target="_blank" class="min-h-[44px] inline-flex items-center px-4 py-2.5 rounded-lg text-rose-500 hover:text-rose-400 bg-slate-800/50 transition touch-manipulation">فتح في نافذة جديدة</a>
            </div>
        @endif
    </div>
    <h1 class="text-xl sm:text-2xl font-bold mt-4 break-words">{{ $content->title }}</h1>
    @if($content->description)
        <p class="text-slate-400 mt-2 text-sm">{{ Str::limit($content->description, 200) }}</p>
    @endif
</div>

<script>
function readProgress() {
    return {
        progress: 0,
        init() {
            this.updateProgress();
            window.addEventListener('scroll', () => this.updateProgress());
        },
        updateProgress() {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            this.progress = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
        }
    };
}
</script>
@endsection
