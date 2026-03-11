<?php

namespace App\Observers;

use App\Models\Season;
use App\Services\MediaFileCleanupService;

class SeasonObserver
{
    public function __construct(
        protected MediaFileCleanupService $cleanup
    ) {}

    public function deleting(Season $season): void
    {
        $this->cleanup->deleteSeasonFolder($season);
    }

    public function updating(Season $season): void
    {

        $posterOld = $season->getOriginal('poster_url');
        $posterNew = $season->poster_url;
        if (filled($posterOld) && $posterOld !== $posterNew && ! str_starts_with($posterOld, 'http')) {
            $this->cleanup->deletePosterIfLocal($posterOld);
        }
    }
}
