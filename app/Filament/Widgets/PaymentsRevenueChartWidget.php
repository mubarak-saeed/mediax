<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentsRevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'إيرادات المدفوعات (آخر 6 أشهر)';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $labels[] = $date->translatedFormat('M Y');

            $data[] = (float) Payment::query()
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->where('payment_status', 'completed')
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي الإيرادات',
                    'data' => $data,
                    'fill' => true,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
