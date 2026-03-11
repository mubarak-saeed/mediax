<?php

use App\Models\Favorite;
use Livewire\Component;

new class extends Component
{
    public int $contentId;

    public ?int $seasonId = null;

    public ?int $episodeId = null;

    public function mount(int $contentId, ?int $seasonId = null, ?int $episodeId = null): void
    {
        $this->contentId = $contentId;
        $this->seasonId = $seasonId;
        $this->episodeId = $episodeId;
    }

    public function getIsFavoritedProperty(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $q = Favorite::query()
            ->where('user_id', auth()->id())
            ->where('content_id', $this->contentId);

        if ($this->episodeId !== null) {
            $q->where('episode_id', $this->episodeId);
        } elseif ($this->seasonId !== null) {
            $q->where('season_id', $this->seasonId)->whereNull('episode_id');
        } else {
            $q->whereNull('season_id')->whereNull('episode_id');
        }

        return $q->exists();
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));

            return;
        }

        $attrs = [
            'user_id' => auth()->id(),
            'content_id' => $this->contentId,
            'season_id' => $this->seasonId,
            'episode_id' => $this->episodeId,
        ];

        $exists = Favorite::query()->where($attrs)->exists();

        if ($exists) {
            Favorite::query()->where($attrs)->delete();
        } else {
            Favorite::query()->create($attrs);
        }
    }
};
?>

<div>
    @auth
    <button type="button"
            wire:click="toggle"
            class="p-2 rounded-full {{ $this->isFavorited ? 'bg-rose-600 text-white' : 'bg-slate-700/80 text-slate-400 hover:bg-slate-600 hover:text-white' }} transition"
            title="{{ $this->isFavorited ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}">
        <svg class="w-5 h-5" fill="{{ $this->isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>
    @endauth
</div>
