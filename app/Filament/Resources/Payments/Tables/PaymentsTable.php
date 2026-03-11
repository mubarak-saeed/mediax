<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')
                    ->label('رقم المعاملة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم المعاملة')
                    ->copyMessageDuration(1500),
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan.name')
                    ->label('الخطة')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(fn ($record) => $record->currency ?? 'usd')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Pending => 'warning',
                        PaymentStatus::Completed => 'success',
                        PaymentStatus::Failed => 'danger',
                    })
                    ->formatStateUsing(fn (PaymentStatus $state) => $state->getLabel()),
                TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->dateTime()
                    ->sortable()
                    ->sinceTooltip(),
                TextColumn::make('currency')
                    ->label('العملة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? strtoupper($state) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn (?string $state) => $state ?: '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحذيث')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault()
                    ->sinceTooltip(),
            ])
            ->defaultSort('paid_at', 'desc')
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('الحالة')
                    ->options(PaymentStatus::class)
                    ->native(false),
                SelectFilter::make('user_id')
                    ->label('المستخدم')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('plan_id')
                    ->label('الخطة')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->groups([
                Group::make('payment_status')->label('حالة الدفع'),
            ]);
    }
}
