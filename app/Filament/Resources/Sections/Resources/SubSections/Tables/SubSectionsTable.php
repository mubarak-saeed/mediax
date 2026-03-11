<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Tables;

use App\Enums\ContentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([

                TextColumn::make('name')
                    ->label('اسم القسم الفرعي')
                    ->searchable()

                    ->sortable(),
                TextColumn::make('contents_count')
                    ->label('المحتويات')
                    ->counts('contents')
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('الرابط')
                    ->toggledHiddenByDefault(),
                TextColumn::make('type')
                    ->label('نوع الفلم')
                    ->badge()
                    ->visible(function ($livewire): bool {
                        $section = $livewire->getOwnerRecord();

                        return $section->type === ContentType::Movie->value;
                    })
                    ->color(fn (?string $state): string => $state ? match (\App\Enums\ContentType::tryFrom($state)) {
                        ContentType::Movie => 'info',
                        ContentType::Series => 'success',
                        ContentType::Book => 'warning',
                        ContentType::MovieSerial => 'gray',
                        default => 'gray',
                    } : 'gray')
                    ->formatStateUsing(fn (?string $state) => $state ? (\App\Enums\ContentType::tryFrom($state)?->getLabel() ?? $state) : '—'),
                TextColumn::make('order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->sinceTooltip(),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->sinceTooltip(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع الفلم')
                    ->visible(function ($livewire): bool {
                        $section = $livewire->getOwnerRecord();

                        return $section->type === ContentType::Movie->value;
                    })
                    ->options([
                        ContentType::Movie->value => ContentType::Movie->getLabel(),
                        ContentType::MovieSerial->value => ContentType::MovieSerial->getLabel(),
                    ])
                    ->native(false),
            ])

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
