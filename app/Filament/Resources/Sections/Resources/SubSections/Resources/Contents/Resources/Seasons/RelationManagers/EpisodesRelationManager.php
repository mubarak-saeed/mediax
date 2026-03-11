<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\RelationManagers;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\EpisodeResource;
use Filament\Resources\RelationManagers\RelationManager;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';

    protected static ?string $relatedResource = EpisodeResource::class;

    protected static ?string $title = 'الحلقات';
}
