<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * بحث في عناوين ووصف المحتوى باستخدام Laravel Scout مع خيارات ترتيب.
 */
class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim($request->get('q', ''));
        $sort = $request->get('sort', 'latest');

        $contents = collect();
        if (strlen($q) >= 2) {
            $builder = Content::search($q)
                ->query(fn ($query) => $query
                    ->with(['genres', 'subsection.section'])
                    ->with(['seasons' => fn ($sq) => $sq->withCount('episodes')])
                    ->withCount('ratings')
                    ->where('file_url', '!=', null)
                    ->withAvg('ratings', 'rating')
                );

            match ($sort) {
                'oldest' => $builder->orderBy('created_at', 'asc'),
                'title' => $builder->orderBy('title', 'asc'),
                'title_desc' => $builder->orderBy('title', 'desc'),
                'year_desc' => $builder->orderBy('release_year', 'desc'),
                'year_asc' => $builder->orderBy('release_year', 'asc'),
                'views_desc' => $builder->orderBy('views_count', 'desc'),
                'views_asc' => $builder->orderBy('views_count', 'asc'),
                'rating_desc' => $builder->orderBy('ratings_avg_rating', 'desc'),
                default => $builder->orderBy('created_at', 'desc'),
            };

            $contents = $builder->paginate(24)->withQueryString();
        }

        return view('search.index', [
            'query' => $q,
            'contents' => $contents,
            'sort' => $sort,
        ]);
    }
}
