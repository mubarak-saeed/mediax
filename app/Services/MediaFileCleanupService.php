<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Support\Facades\Storage;

/**
 * حذف ملفات الوسائط من التخزين عند الحذف أو الاستبدال.
 * يدعم حذف المجلدات بالكامل حسب الهيكل: uploads/المسلسلات|الأفلام|الكتب/{id}-{slug}/...
 */
class MediaFileCleanupService
{
    public $posterFolder = 'البوسترات';

    protected $filesFolder = 'الملفات';

    /**
     * حذف poster من القرص public (إن كان مساراً محلياً وليس رابطاً خارجياً).
     */
    public function deletePosterIfLocal(?string $path): bool
    {
        if (blank($path) || str_starts_with($path, 'http')) {
            return false;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * حذف ملف من القرص local.
     */
    public function deleteLocalFile(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->delete($path);
        }

        return false;
    }

    /**
     * حذف مجلد بالكامل من القرص.
     */
    public function deleteFolder(string $disk, string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->deleteDirectory($path);
        }

        return false;
    }

    /**
     * حذف مجلد المحتوى بالكامل (poster + ملفات) عند حذف المحتوى.
     */
    public function deleteContentFolder(Content $content): void
    {
        $posterContentFolder = $this->posterFolder.'/'.$content->subsection->section->name.'/'.$content->subsection->name.'/'.$content->title;
        $filesContentFolder = $this->filesFolder.'/'.$content->subsection->section->name.'/'.$content->subsection->name.'/'.$content->title;
        $this->deleteFolder('public', $posterContentFolder);
        $this->deleteFolder('local', $filesContentFolder);
    }

    /**
     * حذف مجلد الموسم بالكامل (poster + حلقات) عند حذف الموسم.
     */
    public function deleteSeasonFolder(Season $season): void
    {
        $posterSeasonFolder = $this->posterFolder.'/'.$season->content->subsection->section->name.'/'.$season->content->subsection->name.'/'.$season->content->title.'/'.$season->title;
        $this->deleteFolder('public', $posterSeasonFolder);
        $filesSeasonFolder = $this->filesFolder.'/'.$season->content->subsection->section->name.'/'.$season->content->subsection->name.'/'.$season->content->title.'/'.$season->title;
        $this->deleteFolder('local', $filesSeasonFolder);
    }

    /**
     * حذف ملفات الحلقة (للتوافق - يُفضّل deleteEpisodeFolder).
     */
    public function deleteEpisodeFiles(Episode $episode): void
    {
        $this->deletePosterIfLocal($episode->poster_url);
        $this->deleteLocalFile($episode->file_url);
    }

    /**
     * حذف poster الموسم (للتوافق - يُفضّل deleteSeasonFolder).
     */
    public function deleteSeasonPoster(Season $season): void
    {
        $this->deletePosterIfLocal($season->poster_url);
    }

    public function deleteContentPoster(Content $content): void
    {
        $this->deletePosterIfLocal($content->poster_url);
    }

    public function deleteContentFile(Content $content): void
    {
        $this->deleteLocalFile($content->file_url);
    }
}
