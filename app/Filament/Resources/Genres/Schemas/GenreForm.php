<?php

namespace App\Filament\Resources\Genres\Schemas;

use App\Models\Genre;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GenreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات النوع الفني')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->unique(table: Genre::class, column: 'name', ignoreRecord: true)
                            ->placeholder('مثال: أكشن، دراما، كوميدي'),
                    ]),
            ]);
    }
}
