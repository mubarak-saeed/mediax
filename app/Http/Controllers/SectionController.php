<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\Genre;
use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * عرض صفحات الأقسام الرئيسية والأقسام الفرعية وقوائم المحتوى مع التصفية.
 */
class SectionController extends Controller
{
    /**
     * قائمة الأقسام الرئيسية مع عدد الأقسام الفرعية والمحتويات.
     */
    public function index(): View
    {

        // SubSection::query()->whereHas('section', fn(Builder $q) => $q->where('status', 'active'));
        $sections = Section::query()
            ->with(['subSections' => fn($q) => $q->orderBy('order')->withCount('contents')])
            ->withCount('contents')
            ->orderBy('order')
            ->where('status', 'active')
            ->get();

        return view('sections.index', [
            'sections' => $sections,
        ]);
    }

    /**
     * صفحة قسم رئيسي: عرض شبكة الأقسام الفرعية فقط (بدون قائمة محتوى).
     */
    public function show(Section $section): View
    {
        $section->load(['subSections' => fn($q) => $q->orderBy('order')->withCount('contents')]);

        return view('sections.show', [
            'section' => $section,
        ]);
    }

    /**
     * صفحة قسم فرعي: قائمة المحتويات مع تصفية (نوع/سنة) وترتيب وترقيم.
     */
    public function showSubsection(Request $request, Section $section, SubSection $subsection): View
    {
        if ($subsection->section_id !== $section->id) {
            abort(404);
        }

        $subsection->loadCount('contents');

        $query = $subsection->contents()
            ->with('genres')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        if ($subsection->type === ContentType::Series->value || $subsection->type === ContentType::MovieSerial->value) {
            $query->with(['seasons' => fn($q) => $q->withCount('episodes')])
                ->whereHas('episodes', fn($q) => $q->where('file_url', '!=', null));
        } else {
            $query->whereNotNull('file_url');
        }

        if ($request->filled('genre')) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', (int) $request->genre));
        }
        if ($request->filled('year')) {
            $query->where('release_year', (int) $request->year);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'year_desc' => $query->orderByDesc('release_year'),
            'year_asc' => $query->orderBy('release_year'),
            'views_desc' => $query->orderByDesc('views_count'),
            'views_asc' => $query->orderBy('views_count'),
            'rating_desc' => $query->orderByDesc('ratings_avg_rating'),
            default => $query->latest(),
        };

        $contents = $query->paginate(24)->withQueryString();

        $years = $subsection->contents()
            ->whereNotNull('release_year')
            ->distinct()
            ->pluck('release_year')
            ->sortDesc()
            ->take(30)
            ->values()
            ->all();

        $genres = Genre::query()
            ->whereHas('contents', fn($q) => $q->where('sub_section_id', $subsection->id))
            ->orderBy('name')
            ->get();

        return view('sections.show', [
            'section' => $section,
            'subsection' => $subsection,
            'contents' => $contents,
            'genres' => $genres,
            'years' => $years,
        ]);
    }
}
