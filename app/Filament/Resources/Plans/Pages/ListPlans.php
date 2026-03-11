<?php

namespace App\Filament\Resources\Plans\Pages;

use App\Filament\Resources\Plans\PlanResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        Artisan::call('plans:sync-stripe');

        return [
            Action::make('syncStripe')
                ->label('مزامنة من Stripe')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    $msm = Artisan::call('plans:sync-stripe');
                    $output = Artisan::output();
                    $msm == 0 ? Notification::make()
                        ->title('تمت المزامنة')
                        ->body($output)
                        ->success()
                        ->send() : Notification::make()
                        ->title('حدث خطأ ما!')
                        // ->body('تأكد من الإتصال ب الإنترنت')
                        ->warning()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
