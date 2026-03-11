<?php

namespace App\Filament\Resources\Genres;

use App\Filament\Resources\Genres\Pages\ListGenres;
use App\Filament\Resources\Genres\Schemas\GenreForm;
use App\Filament\Resources\Genres\Tables\GenresTable;
use App\Models\Genre;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GenreResource extends Resource
{
    protected static ?string $model = Genre::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'إدارة المحتوى';

    protected static ?string $navigationLabel = 'الأنواع الفنية';

    protected static ?string $modelLabel = 'نوع فني';

    protected static ?string $pluralModelLabel = 'أنواع فنية';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return GenreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GenresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return parent::getEloquentQuery()->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGenres::route('/'),
        ];
    }
}
