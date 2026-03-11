<?php

namespace App\Filament\Widgets;

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Payment;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class MediaxStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalRevenue = Payment::query()
            ->where('payment_status', 'completed')
            ->sum('amount');

        $totalViews = Content::query()->sum('views_count');
        $moviesCount = Content::query()->where('type', ContentType::Movie)->count();
        $seriesCount = Content::query()->where('type', ContentType::Series)->count();
        $booksCount = Content::query()->where('type', ContentType::Book)->count();

        return [
            Stat::make('إجمالي المستخدمين', Number::format(User::query()->count()))
                ->description('المستخدمون المسجلون')
                ->descriptionIcon('heroicon-o-users', IconPosition::Before)
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->url(\App\Filament\Resources\Users\UserResource::getUrl('index')),
            Stat::make('إجمالي المحتوى', Number::format(Content::query()->count()))
                ->description("أفلام: {$moviesCount} | مسلسلات: {$seriesCount} | كتب: {$booksCount}")
                ->descriptionIcon('heroicon-o-film', IconPosition::Before)
                ->icon('heroicon-o-squares-2x2')
                ->color('info')
                ->url(\App\Filament\Resources\Sections\SectionResource::getUrl('index')),
            Stat::make('إجمالي الإيرادات', Number::currency($totalRevenue, 'USD'))
                ->description('من المدفوعات المكتملة')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->url(\App\Filament\Resources\Payments\PaymentResource::getUrl('index')),
            Stat::make('الأقسام', Number::format(Section::query()->count()))
                ->description(SubSection::query()->count().' قسم فرعي')
                ->descriptionIcon('heroicon-o-folder', IconPosition::Before)
                ->icon('heroicon-o-squares-plus')
                ->color('gray')
                ->url(\App\Filament\Resources\Sections\SectionResource::getUrl('index')),
        ];
    }
}
