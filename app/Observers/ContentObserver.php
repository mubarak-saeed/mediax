<?php

namespace App\Observers;

use App\Enums\ContentType;
use App\Models\Content;
use App\Services\MediaFileCleanupService;
use App\Services\MediaMetadataService;
use Illuminate\Support\Facades\Storage;

class ContentObserver
{
    public function __construct(
        protected MediaFileCleanupService $cleanup,
        protected MediaMetadataService $mediaMetadata
    ) {}

    public function deleting(Content $content): void
    {
        $this->cleanup->deleteContentFolder($content);
    }

    public function updating(Content $content): void
    {
        $this->deleteReplacedFiles($content);
    }

    public function created(Content $content): void
    {
        // مزامنة المدة وحجم الملف بعد الإنشاء الأول
        $this->syncDurationFromFile($content);

        if ($content->isDirty(['duration', 'file_size'])) {
            $content->saveQuietly();
        }
    }

    public function updated(Content $content): void
    {
        // ضمان مزامنة المدة وحجم الملف بعد أي تحديث
        $this->syncDurationFromFile($content);

        if ($content->isDirty(['duration', 'file_size'])) {
            $content->saveQuietly();
        }
    }

    private function deleteReplacedFiles(Content $content): void
    {
        $posterOld = $content->getOriginal('poster_url');
        $posterNew = $content->poster_url;
        if (filled($posterOld) && $posterOld !== $posterNew && ! str_starts_with($posterOld, 'http') && ! str_starts_with($posterOld, 'images')) {
            $this->cleanup->deletePosterIfLocal($posterOld);
        }

        $fileOld = $content->getOriginal('file_url');
        $fileNew = $content->file_url;
        if (filled($fileOld) && $fileOld !== $fileNew) {
            $this->cleanup->deleteLocalFile($fileOld);
        }
    }

    /**
     * ضبط حقول المدة وحجم الملف تلقائياً من ملف المحتوى إن وجد:
     * - للأفلام: قراءة مدة الفيديو بالثواني وتخزينها في `duration`، وتخزين حجم الملف في `file_size`.
     * - للمحتوى المقروء (الكتب): تخزين حجم الملف في `file_size` فقط.
     */
    private function syncDurationFromFile(Content $content): void
    {
        if (blank($content->file_url)) {
            return;
        }

        // في النموذج نرفع الملف على قرص local
        $disk = 'local';

        if (! Storage::disk($disk)->exists($content->file_url)) {
            return;
        }

        $absolutePath = Storage::disk($disk)->path($content->file_url);
        $bytes = Storage::disk($disk)->size($content->file_url);
        $content->file_size = $bytes;

        if ($content->type === ContentType::Book) {
            // للمحتوى المقروء: لا نستخدم duration، فقط file_size
            $content->duration = null;

            return;
        }

        // للأفلام: نقرأ مدة الفيديو بالثواني إن أمكن
        if (in_array($content->type, [ContentType::Movie, ContentType::MovieSerial], true)) {
            $seconds = $this->mediaMetadata->getVideoDurationMinutes($absolutePath);

            if ($seconds !== null && $seconds > 0) {
                $content->duration = $seconds;
            }
        }
    }
}
