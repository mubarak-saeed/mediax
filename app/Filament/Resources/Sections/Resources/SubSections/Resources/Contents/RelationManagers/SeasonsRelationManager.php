<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\RelationManagers;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\SeasonResource;
use Filament\Resources\RelationManagers\RelationManager;

class SeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';

    protected static ?string $relatedResource = SeasonResource::class;

    protected static ?string $title = 'المواسم';
}
