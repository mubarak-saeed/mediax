<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends PagesDashboard
{
    protected static bool $isDiscovered = false;

    /**
     * عدد أعمدة شبكة الويدجتات (متجاوب: شاشة صغيرة عمود واحد، متوسطة 2، كبيرة 3).
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'xl' => 2,
        ];
    }

    protected function getHeaderActions(): array
    {
        Artisan::call('stripe:sync');
        

        return [
            Action::make('viewWebSite')
                ->label('عرض المنصة')
                ->url('/')
                ->icon(Heroicon::ArrowLeftOnRectangle)
                ->iconPosition(IconPosition::After)
                ->color('gray')
                ->openUrlInNewTab(),
        ];
    }
}
