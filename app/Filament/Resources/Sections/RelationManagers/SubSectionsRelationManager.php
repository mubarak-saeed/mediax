<?php

namespace App\Filament\Resources\Sections\RelationManagers;

use App\Filament\Resources\Sections\Resources\SubSections\SubSectionResource;
use Filament\Resources\RelationManagers\RelationManager;

class SubSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subSections';

    protected static ?string $relatedResource = SubSectionResource::class;

    protected static ?string $title = 'الأقسام الفرعية';
}
