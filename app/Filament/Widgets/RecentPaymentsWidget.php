<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;

class RecentPaymentsWidget extends BaseTableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['user', 'plan'])
                    ->latest('paid_at')
            )
            ->heading('أحدث المدفوعات')
            ->description('آخر العمليات المالية المكتملة')
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable(),
                TextColumn::make('plan.name')
                    ->label('الخطة'),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(fn (Payment $record): string => $record->currency ?? 'usd'),
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
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')->label('الحالة')->options(PaymentStatus::class),
                SelectFilter::make('plan.name')->label('الخطة')->relationship('plan', 'name'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->defaultSort('paid_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->headerActions([
                Action::make('viewAll')
                    ->label('عرض الكل')
                    ->url(PaymentResource::getUrl('index'))
                    ->icon('heroicon-o-banknotes'),
            ]);
    }
}
