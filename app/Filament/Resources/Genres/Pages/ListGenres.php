<?php

namespace App\Filament\Resources\Genres\Pages;

use App\Filament\Resources\Genres\GenreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ListGenres extends ListRecords
{
    protected static string $resource = GenreResource::class;

    public function getHeading(): string|Htmlable|null
    {
        return GenreResource::getNavigationLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth(Width::Prose),
        ];
    }
}
