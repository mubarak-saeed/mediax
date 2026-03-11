<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Pages\CreateEpisode;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Pages\EditEpisode;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Schemas\EpisodeForm;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Tables\EpisodesTable;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\SeasonResource;
use App\Models\Episode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SeasonResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'الحلقات';

    protected static ?string $modelLabel = 'حلقة';

    protected static ?string $pluralModelLabel = 'الحلقات';

    public static function form(Schema $schema): Schema
    {
        return EpisodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EpisodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateEpisode::route('/create'),
            'edit' => EditEpisode::route('/{record}/edit'),
        ];
    }
}
