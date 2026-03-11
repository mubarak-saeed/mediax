<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('البوستر')
                    ->circular(false)
                    ->toggleable(),
                TextColumn::make('season')
                    ->label('الموسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('episodes_count')
                    ->label('عدد الحلقات')
                    ->counts('episodes')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->sinceTooltip(),
            ])
            ->defaultSort('season')
            ->recordActions([

                EditAction::make(),
                DeleteAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
