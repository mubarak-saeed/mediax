<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EpisodeController extends Controller
{
    public function show(Content $content, Season $season, Episode $episode): View
    {
        if ($season->content_id !== $content->id || $episode->season_id !== $season->id) {
            abort(404);
        }

        $episode->load('season');

        $streamUrl = null;
        $downloadUrl = null;
        $user = auth('web')->user();
        if ($user && $episode->file_url && $user->canAccessContent($content)) {
            $streamUrl = URL::temporarySignedRoute('stream.episode', now()->addHour(), ['episode' => $episode->id]);
            if ($user->canDownload()) {
                $downloadUrl = $streamUrl;
            }
        }

        return view('episodes.show', [
            'content' => $content,
            'season' => $season,
            'episode' => $episode,
            'streamUrl' => $streamUrl,
            'downloadUrl' => $downloadUrl,
        ]);
    }
}
