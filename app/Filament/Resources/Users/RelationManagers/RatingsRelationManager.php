<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $title = 'التقييمات';

    protected static ?string $recordTitleAttribute = 'id';

    public function schema(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('content.title')
                    ->label('المحتوى')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('التقييم')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state.'/5')
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'info',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('review')
                    ->label('المراجعة')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('rated_at')
                    ->label('تاريخ التقييم')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('rated_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
