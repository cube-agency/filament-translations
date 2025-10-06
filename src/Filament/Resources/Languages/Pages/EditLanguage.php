<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages;

use CubeAgency\FilamentTranslations\Filament\Resources\LanguageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
