<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class MonthlyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $revenueThisMonth = Payment::query()
            ->where('payment_status', 'completed')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $newUsersThisMonth = User::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $paymentsCountThisMonth = Payment::query()
            ->where('payment_status', 'completed')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->count();

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $revenueLastMonth = Payment::query()
            ->where('payment_status', 'completed')
            ->whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        return [
            Stat::make('إيرادات الشهر', Number::currency($revenueThisMonth, 'USD'))
                ->description($revenueTrend >= 0 ? "+{$revenueTrend}% عن الشهر الماضي" : "{$revenueTrend}% عن الشهر الماضي")
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down', IconPosition::Before)
                ->icon('heroicon-o-currency-dollar')
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),
            Stat::make('مستخدمون جدد هذا الشهر', Number::format($newUsersThisMonth))
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-calendar', IconPosition::Before)
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->url(\App\Filament\Resources\Users\UserResource::getUrl('index')),
            Stat::make('مدفوعات هذا الشهر', Number::format($paymentsCountThisMonth))
                ->description('عملية دفع مكتملة')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->url(\App\Filament\Resources\Payments\PaymentResource::getUrl('index')),
        ];
    }
}
