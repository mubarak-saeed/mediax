<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Pages;

use App\Enums\ContentType;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\ContentResource;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\RelationManagers\RatingsRelationManager;
use App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\RelationManagers\SeasonsRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewOnSite')
                ->label('عرض في الموقع')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => route('content.show', $this->record))
                ->openUrlInNewTab()
                ->color('gray'),
            DeleteAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {

        return $this->getRecord()->type == ContentType::Book || $this->getRecord()->type == ContentType::Movie ? [
            RatingsRelationManager::class,
        ] : [
            SeasonsRelationManager::class,
            RatingsRelationManager::class,

        ];
    }
}
