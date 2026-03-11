<?php

namespace App\Filament\Resources\Sections\Resources\SubSections;

use App\Filament\Resources\Sections\Resources\SubSections\Pages\CreateSubSection;
use App\Filament\Resources\Sections\Resources\SubSections\Pages\EditSubSection;
use App\Filament\Resources\Sections\Resources\SubSections\RelationManagers\ContentsRelationManager;
use App\Filament\Resources\Sections\Resources\SubSections\Schemas\SubSectionForm;
use App\Filament\Resources\Sections\Resources\SubSections\Tables\SubSectionsTable;
use App\Filament\Resources\Sections\SectionResource;
use App\Models\SubSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubSectionResource extends Resource
{
    protected static ?string $model = SubSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SectionResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'الأقسام الفرعية';

    protected static ?string $modelLabel = 'قسم فرعي';

    protected static ?string $pluralModelLabel = 'أقسام فرعية';

    public static function form(Schema $schema): Schema
    {
        return SubSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'contents' => ContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateSubSection::route('/create'),
            'edit' => EditSubSection::route('/{record}/edit'),
        ];
    }
}
