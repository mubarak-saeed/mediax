<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Season;
use Illuminate\View\View;

class SeasonController extends Controller
{
    public function show(Content $content, Season $season): View
    {
        if ($season->content_id !== $content->id) {
            abort(404);
        }

        $episodes = $season->episodes()->whereNotNull('file_url')->orderBy('episode_number')->get();

        return view('seasons.show', [
            'content' => $content,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }
}
