<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents;

use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Pages\CreateContent;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Pages\EditContent;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Schemas\ContentForm;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Tables\ContentsTable;
use App\Filament\Resources\Sections\Resources\SubSections\SubSectionResource;
use App\Models\Content;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static ?string $parentResource = SubSectionResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'المحتويات';

    protected static ?string $modelLabel = 'محتوى';

    protected static ?string $pluralModelLabel = 'محتويات';

    public static function form(Schema $schema): Schema
    {
        return ContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateContent::route('/create'),
            'edit' => EditContent::route('/{record}/edit'),
        ];
    }
}
