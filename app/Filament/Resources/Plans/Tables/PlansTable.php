<?php

namespace App\Filament\Resources\Plans\Tables;

use App\Enums\AccessLevel;
use App\Enums\Currency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('السعر')
                    ->money(fn ($record) => $record->currency ?? 'usd')
                    ->sortable(),
                TextColumn::make('access_level')
                    ->label('مستوى الوصول')
                    ->badge()
                    ->color(fn (AccessLevel $state): string => match ($state) {
                        AccessLevel::Free => 'gray',
                        AccessLevel::Basic => 'info',
                        AccessLevel::Pro => 'warning',
                        AccessLevel::Premium => 'success',
                    })
                    ->formatStateUsing(fn (AccessLevel $state) => $state->getLabel()),
                TextColumn::make('interval_label')
                    ->label('الفترة')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('stripe_price_id')
                    ->label('Stripe')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! (empty($record->stripe_price_id) || empty($record->stripe_product_id))),
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('العملة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (Currency::tryFrom($state)?->getLabel() ?? strtoupper($state)) : '—')
                    ->toggleable(),
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
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('access_level')
                    ->label('مستوى الوصول')
                    ->options(AccessLevel::class)
                    ->native(false),
                TernaryFilter::make('is_subscribable')
                    ->label('مرتبط بـ Stripe')
                    ->placeholder('الكل')
                    ->trueLabel('مرتبط فقط')
                    ->falseLabel('غير مرتبط')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('stripe_price_id')->where('stripe_price_id', '!=', ''),
                        false: fn ($query) => $query->where(function ($q) {
                            $q->whereNull('stripe_price_id')->orWhere('stripe_price_id', '');
                        }),
                    ),
            ])
            ->groups([
                Group::make('access_level')->label('مستوى الوصول'),
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
