<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Tables;

use App\Enums\ContentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make()->disabled(function (Table $table) {
                    $episode_count = $table->getRelationship()->count();
                    $type = $table->getRelationship()->getParent()->content->type;
                    $true = $episode_count >= 1 && $type == ContentType::MovieSerial;

                    return $true;
                }),
            ])
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('البوستر')
                    ->circular(false)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('episode_number')
                    ->label('رقم الحلقة')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('المدة')
                    ->formatStateUsing(function ($state): string {
                        $hours = intdiv($state, 3600);
                        $minutes = intdiv($state % 3600, 60);
                        $seconds = $state % 60;
                        if (
                            $hours > 0 && $minutes > 0 && $seconds > 0
                        ) {
                            $duration = $hours . ' س ' . $minutes . ' د ' . $seconds . ' ث';
                        } elseif ($hours > 0 && $minutes > 0) {
                            $duration = $hours . ' س ' . $minutes . ' د';
                        } elseif ($hours > 0 && $seconds > 0) {
                            $duration = $hours . ' س ' . $seconds . ' ث';
                        } elseif ($minutes > 0 && $seconds > 0) {
                            $duration = $minutes . ' د ' . $seconds . ' ث';
                        } else {
                            $duration = $seconds . ' ث';
                        }

                        return $duration;
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->sinceTooltip(),
                TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault()
                    ->toggleable()
                    ->sinceTooltip(),
            ])
            ->defaultSort('episode_number')
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
