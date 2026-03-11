<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\RelationManagers;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\ContentResource;
use Filament\Resources\RelationManagers\RelationManager;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';

    protected static ?string $relatedResource = ContentResource::class;

    protected static ?string $title = 'المحتويات';
}
