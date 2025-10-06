<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages;

use CubeAgency\FilamentTranslations\Filament\Resources\LanguageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLanguages extends ListRecords
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
