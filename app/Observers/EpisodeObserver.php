<?php

namespace App\Observers;

use App\Models\Episode;
use App\Services\MediaFileCleanupService;
use App\Services\MediaMetadataService;
use Illuminate\Support\Facades\Storage;

class EpisodeObserver
{
    public function __construct(
        protected MediaFileCleanupService $cleanup,
        protected MediaMetadataService $mediaMetadata
    ) {}

    public function creating(Episode $episode): void
    {
        $this->syncDurationFromFile($episode);
    }

    public function deleting(Episode $episode): void
    {
        if (! str_starts_with($episode->poster_url, 'images')) {

            $this->cleanup->deleteEpisodeFiles($episode);
        }
    }

    public function updating(Episode $episode): void
    {
        $posterOld = $episode->getOriginal('poster_url');
        $posterNew = $episode->poster_url;
        if (filled($posterOld) && $posterOld !== $posterNew && ! str_starts_with($posterOld, 'http') && ! str_starts_with($posterOld, 'images')) {
            $this->cleanup->deletePosterIfLocal($posterOld);
        }

        $fileOld = $episode->getOriginal('file_url');
        $fileNew = $episode->file_url;
        if (filled($fileOld) && $fileOld !== $fileNew) {
            $this->cleanup->deleteLocalFile($fileOld);
        }

        $this->syncDurationFromFile($episode);
    }

    private function syncDurationFromFile(Episode $episode): void
    {
        if (blank($episode->file_url)) {
            return;
        }

        $disk = 'local';

        if (! Storage::disk($disk)->exists($episode->file_url)) {
            return;
        }

        $absolutePath = Storage::disk($disk)->path($episode->file_url);
        $bytes = Storage::disk($disk)->size($episode->file_url);

        $episode->file_size = $bytes;

        $seconds = $this->mediaMetadata->getVideoDurationMinutes($absolutePath);

        if ($seconds !== null && $seconds > 0) {
            $episode->duration = $seconds;
        }
    }
}
