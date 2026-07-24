<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Tables;

use App\Enums\AccessLevel;
use App\Enums\ContentType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(function ($query) {
            //     // if (!auth('web')->user()->isSuperAdmin())
            //         $query->latest();
            // })
            ->recordTitleAttribute('title')
            ->headerActions([
                Action::make('viewOnSite')
                    ->label('عرض في الموقع')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(function (RelationManager $livewire) {
                        $record = $livewire->getOwnerRecord();

                        return url()->route('sections.subsection.show', [$record->section, $record]);
                    })
                    ->openUrlInNewTab()
                    ->color('gray'),
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['created_by'] = auth('web')->id();

                        return $data;
                    })
                    ->label(function (Table $table): string {
                        $record = $table->getRelationship()->getParent()->getOriginal();
                        $type = $record['type'];
                        $m = ContentType::class;
                        if ($type === ContentType::Series->value) {
                            return 'إنشاء '.ContentType::Series->getLabel();
                        }
                        if ($type === ContentType::Book->value) {
                            return 'إنشاء '.ContentType::Book->getLabel();
                        }
                        if ($type === ContentType::MovieSerial->value) {
                            return 'إنشاء '.ContentType::MovieSerial->getLabel();
                        }
                        if ($type === ContentType::Movie->value) {
                            return 'إنشاء '.ContentType::Movie->getLabel();
                        }

                        return 'إنشاء محتوى';
                    }),
            ])
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('الغلاف')
                    ->circular(false),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column): ?string => strlen((string) $column->getState()) > 40 ? $column->getState() : null),
                TextColumn::make('type')
                    ->toggledHiddenByDefault()
                    ->label('النوع')
                    ->badge()
                    ->color(fn (ContentType $state): string => match ($state) {
                        ContentType::Movie => 'info',
                        ContentType::Series => 'success',
                        ContentType::Book => 'warning',
                        ContentType::MovieSerial => 'info',
                    })
                    ->formatStateUsing(fn (ContentType $state) => $state->getLabel()),
                TextColumn::make('language')
                    ->label('اللغة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('release_year')
                    ->label('سنة الإصدار')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('access_level_required')
                    ->label('مستوى الوصول')
                    ->badge()
                    ->color(fn (AccessLevel $state): string => match ($state) {
                        AccessLevel::Free => 'gray',
                        AccessLevel::Basic => 'info',
                        AccessLevel::Pro => 'warning',
                        AccessLevel::Premium => 'success',
                    })
                    ->formatStateUsing(fn (AccessLevel $state) => $state->getLabel()),
                IconColumn::make('is_featured')
                    ->label('مميز')
                    ->disabledClick()
                    ->boolean()
                    ->tooltip('محتوى مميز'),
                TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('أنشئ بواسطة')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->sinceTooltip(),
            ])
            ->filters([

                SelectFilter::make('access_level_required')
                    ->label('مستوى الوصول')
                    ->options(AccessLevel::class)
                    ->native(false),
                TernaryFilter::make('is_featured')
                    ->label('محتوى مميز')
                    ->placeholder('الكل')
                    ->trueLabel('مميز فقط')
                    ->falseLabel('غير مميز'),
            ])
            ->groups([
                \Filament\Tables\Grouping\Group::make('type')->label('النوع'),
                \Filament\Tables\Grouping\Group::make('access_level_required')->label('مستوى الوصول'),
            ])
            ->recordActions([

                ViewAction::make()
                    ->label('عرض في الموقع')
                    ->url(fn ($record) => route('content.show', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
