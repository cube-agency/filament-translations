<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources;

use CubeAgency\FilamentTranslations\Filament\Resources\TranslationResource\Pages;
use Filament\Resources\Resource;
use Waavi\Translation\Models\Translation;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Localization';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
        ];
    }
}
