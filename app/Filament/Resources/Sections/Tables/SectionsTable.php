<?php

namespace App\Filament\Resources\Sections\Tables;

use App\Enums\ContentType;
use App\Enums\StatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم القسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->sortable()
                    ->badge(),
                TextColumn::make('sub_sections_count')
                    ->label('الأقسام الفرعية')
                    ->counts('subSections')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('نوع المحتوى')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? match (ContentType::tryFrom($state)) {
                        ContentType::Movie => 'info',
                        ContentType::Series => 'success',
                        ContentType::Book => 'warning',
                        default => 'gray',
                    } : 'gray')
                    ->formatStateUsing(fn (?string $state) => $state ? (ContentType::tryFrom($state)?->getLabel() ?? $state) : '—'),
                TextColumn::make('order')
                    ->label('الترتيب في الموقع')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->sinceTooltip(),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحذيث')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault()
                    ->sinceTooltip(),

            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع المحتوى')
                    ->options(function () {
                        $sectoinTypes = [
                            ContentType::Movie->value => ContentType::Movie->getLabel(),
                            ContentType::Series->value => ContentType::Series->getLabel(),
                            ContentType::Book->value => ContentType::Book->getLabel(),
                        ];

                        return $sectoinTypes;
                    })
                    ->native(false),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(StatusEnum::class)
                    ->native(false),
            ])
            ->heading('قائمة الأقسام الرئيسية')
            ->description('هنا يمكنك إدارة الأقسام الرئيسية في الموقع، يمكنك إضافة أقسام جديدة أو تعديل أو حذف الأقسام الحالية.')
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
