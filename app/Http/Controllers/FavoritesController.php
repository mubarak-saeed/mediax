<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * عرض صفحة المفضلة مع التصفيات (نوع: الكل / محتوى / موسم / حلقة، ترتيب: الأحدث / الأقدم).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $typeFilter = $request->query('type', 'content'); // all | content | season | episode
        $sort = $request->query('sort', 'newest');   // newest | oldest

        $query = $user->favorites()
            ->with([
                'content' => fn ($q) => $q->with('genres')->with(['seasons' => fn ($s) => $s->withCount('episodes')])->withCount('ratings')->withAvg('ratings', 'rating'),
                'season' => fn ($q) => $q->withCount('episodes'),
                'episode',
            ]);

        if ($typeFilter === 'content') {
            $query->whereNull('season_id')->whereNull('episode_id');
        } elseif ($typeFilter === 'season') {
            $query->whereNotNull('season_id')->whereNull('episode_id');
        } elseif ($typeFilter === 'episode') {
            $query->whereNotNull('episode_id');
        }

        $favorites = $sort === 'oldest'
            ? $query->oldest()->get()
            : $query->latest()->get();

        return view('favorites.index', [
            'favorites' => $favorites,
            'typeFilter' => $typeFilter,
            'sort' => $sort,
        ]);
    }
}
