<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

/**
 * عرض صفحة محتوى واحد (فيلم/مسلسل/كتاب) مع المحتوى المرتبط ورابط التحميل للمشتركين.
 */
class ContentController extends Controller
{
    /**
     * عرض تفاصيل المحتوى مع المحتوى المرتبط ورابط التحميل (للمشترك/الأدمن).
     */
    public function show(Content $content): View
    {
        $content->load([
            'genres',
            'subsection.section',
            'seasons' => fn($query) => $query->withCount('episodes'),
        ]);
        $content->loadCount('ratings');
        $content->loadAvg('ratings', 'rating');

        $related = $this->getRelatedContents($content);

        $downloadUrl = null;
        $user = auth('web')->user();
        if ($user && $content->file_url && $user->canAccessContent($content) && $user->canDownload()) {
            $downloadUrl = URL::temporarySignedRoute('stream', now()->addHour(), ['content' => $content->id]);
        }

        return view('content.show', [
            'content' => $content,
            'related' => $related,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    /**
     * جلب محتويات مرتبطة (نفس القسم الفرعي/القسم/الأنواع) بحد أقصى 8.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Content>
     */
    private function getRelatedContents(Content $content): \Illuminate\Database\Eloquent\Collection
    {
        $subSectionId = $content->sub_section_id;
        $sectionId = $content->subsection?->section_id;
        $genreIds = $content->genres->pluck('id');
        $isSeries = in_array($content->type->value, ['series', 'movieserial'], true);

        $query = Content::query()

            ->where('id', '!=', $content->id)
            ->where('type', $content->type)
            ->with(['genres', 'subsection.section'])
            ->with($isSeries ? ['seasons' => fn($q) => $q->withCount('episodes')] : [])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        if ($isSeries && $subSectionId) {
            $query->where('sub_section_id', $subSectionId);
        } else {
            $hasCriteria = $subSectionId || $sectionId || $genreIds->isNotEmpty();
            if ($hasCriteria) {
                $query->where(function ($q) use ($subSectionId, $sectionId, $genreIds): void {
                    if ($subSectionId) {
                        $q->where('sub_section_id', $subSectionId);
                    }
                    if ($sectionId) {
                        $q->orWhereHas('subsection', fn($q2) => $q2->where('section_id', $sectionId));
                    }
                    if ($genreIds->isNotEmpty()) {
                        $q->orWhereHas('genres', fn($q2) => $q2->whereIn('genres.id', $genreIds));
                    }
                });
            }
        }

        if ($isSeries) {
            $query->whereHas('episodes', fn($q) => $q->whereNotNull('file_url'));
        } else {
            $query->whereNotNull('file_url');
        }

        return $query->latest()->limit(8)->get();
    }
}
