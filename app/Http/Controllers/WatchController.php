<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Episode;
use App\Services\ContentViewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchController extends Controller
{
    public function show(Request $request, Content $content): View|\Illuminate\Http\RedirectResponse
    {
        if (! in_array($content->type, [ContentType::Movie, ContentType::Series, ContentType::MovieSerial], true)) {
            return redirect()->route('content.show', $content);
        }

        if (in_array($content->type, [ContentType::Series, ContentType::MovieSerial], true)) {
            $content->load(['seasons' => fn ($q) => $q->orderByRaw('CAST(season AS UNSIGNED)')->with('episodes')]);
            $episodeId = $request->query('episode');
            if (! $episodeId) {
                $firstSeason = $content->seasons->sortBy(fn ($s) => (int) $s->season)->first();
                if (! $firstSeason) {
                    return redirect()->route('content.show', $content)
                        ->with('status', 'لا توجد مواسم أو حلقات متاحة للمشاهدة حالياً.');
                }

                return redirect()->route('seasons.show', [$content, $firstSeason])
                    ->with('status', 'اختر حلقة للمشاهدة.');
            }

            $episode = Episode::query()
                ->where('id', $episodeId)
                ->whereHas('season', fn ($q) => $q->where('content_id', $content->id))
                ->with('season')
                ->firstOrFail();

            if (! $episode->file_url) {
                return redirect()->route('content.show', $content)
                    ->with('status', 'الملف غير متوفر حالياً.');
            }

            $streamUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'stream.episode',
                now()->addHour(),
                ['episode' => $episode->id]
            );

            app(ContentViewService::class)->recordView($content, $episode);

            $allEpisodes = $content->seasons->sortBy(fn ($s) => (int) $s->season)
                ->flatMap(fn ($s) => $s->episodes->sortBy('episode_number')->values())
                ->values();
            $currentIndex = $allEpisodes->search(fn ($e) => $e->id === $episode->id);
            $nextEpisode = $currentIndex !== false && $currentIndex < $allEpisodes->count() - 1
                ? $allEpisodes[$currentIndex + 1]
                : null;

            return view('watch.show', [
                'content' => $content,
                'streamUrl' => $streamUrl,
                'episode' => $episode,
                'nextEpisode' => $nextEpisode,
                'allEpisodes' => $allEpisodes,
            ]);
        }

        if (! $content->file_url) {
            return redirect()->route('content.show', $content)
                ->with('status', 'الملف غير متوفر حالياً.');
        }

        $streamUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'stream',
            now()->addHour(),
            ['content' => $content->id]
        );

        app(ContentViewService::class)->recordView($content);

        return view('watch.show', [
            'content' => $content,
            'streamUrl' => $streamUrl,
            'episode' => null,
            'nextEpisode' => null,
            'allEpisodes' => collect(),
        ]);
    }
}
