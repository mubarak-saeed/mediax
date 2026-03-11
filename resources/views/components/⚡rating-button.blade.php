<?php

use App\Models\Content;
use App\Models\Rating;
use Livewire\Component;

new class extends Component
{
    public int $contentId;

    public bool $showStars = false;

    public function mount(int $contentId): void
    {
        $this->contentId = $contentId;
    }

    public function getUserRatingProperty(): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        $r = Rating::query()
            ->where('user_id', auth()->id())
            ->where('content_id', $this->contentId)
            ->first();

        return $r?->rating;
    }

    public function getContentProperty(): ?Content
    {
        return Content::query()
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->find($this->contentId);
    }

    public function toggleStars(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login', ['intended' => url()->current()]));

            return;
        }

        $this->showStars = ! $this->showStars;
    }

    public function rate(int $value): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login', ['intended' => url()->current()]));

            return;
        }

        if ($value < 1 || $value > 5) {
            return;
        }

        $exists = Rating::query()
            ->where('user_id', auth()->id())
            ->where('content_id', $this->contentId)
            ->exists();

        if ($exists) {
            return;
        }

        Rating::query()->create([
            'user_id' => auth()->id(),
            'content_id' => $this->contentId,
            'rating' => $value,
            'rated_at' => now(),
        ]);

        $this->showStars = false;
    }

    public function removeRating(): void
    {
        if (! auth()->check()) {
            return;
        }

        Rating::query()
            ->where('user_id', auth()->id())
            ->where('content_id', $this->contentId)
            ->delete();
    }
};
?>

<div>
    @auth
    @php $hasRated = $this->userRating !== null; @endphp
    <div class="flex items-center gap-2 flex-wrap">
        @if($hasRated)
            {{-- <span class="text-sm text-amber-400 font-medium">تقييمك {{ number_format($this->userRating, 1) }}</span>
            <button type="button"
                    wire:click="removeRating"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition text-xs"
                    title="إلغاء التقييم">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button> --}}
        @elseif($showStars)
            <div class="flex gap-0.5" role="group" aria-label="التقييم من 1 إلى 5">
                @foreach([1, 2, 3, 4, 5] as $star)
                    <button type="button"
                            wire:click="rate({{ $star }})"
                            class="p-1 rounded transition text-slate-600 hover:text-amber-500/70"
                            title="{{ $star }} نجوم">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endforeach
            </div>
        @else
            <button type="button"
                    wire:click="toggleStars"
                    class="text-sm text-rose-500 hover:text-rose-400 font-medium transition">
                تقييم
            </button>
            @if($this->content && ($this->content->ratings_count ?? 0) > 0)
                <span class="text-sm text-slate-500">({{ number_format($this->content->ratings_count) }} تقييم)</span>
            @endif
        @endif
    </div>
    @else
    <p class="text-sm text-slate-500">
        <a href="{{ route('login', ['intended' => url()->current()]) }}" class="text-rose-500 hover:text-rose-400">سجّل الدخول</a> لتقييم هذا المحتوى
    </p>
    @endauth
</div>
