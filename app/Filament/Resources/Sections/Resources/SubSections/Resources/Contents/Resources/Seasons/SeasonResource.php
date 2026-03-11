<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\ContentResource;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Pages\EditSeason;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\RelationManagers\EpisodesRelationManager;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Schemas\SeasonForm;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Tables\SeasonsTable;
use App\Models\Season;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = ContentResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'المواسم';

    protected static ?string $modelLabel = 'موسم';

    protected static ?string $pluralModelLabel = 'مواسم';

    public static function form(Schema $schema): Schema
    {
        return SeasonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeasonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'episodes' => EpisodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditSeason::route('/{record}/edit'),
        ];
    }
}
