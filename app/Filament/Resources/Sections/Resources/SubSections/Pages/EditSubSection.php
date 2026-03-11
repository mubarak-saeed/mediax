<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Pages;

use App\Filament\Resources\Sections\Resources\SubSections\SubSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubSection extends EditRecord
{
    protected static string $resource = SubSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
