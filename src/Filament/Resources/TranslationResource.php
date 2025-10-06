<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources;

use BackedEnum;
use CubeAgency\FilamentTranslations\Filament\Resources\Translations\Pages\ListTranslations;
use Filament\Resources\Resource;
use UnitEnum;
use Waavi\Translation\Models\Translation;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | UnitEnum | null $navigationGroup = 'Localization';

    public static function getPages(): array
    {
        return [
            'index' => ListTranslations::route('/'),
        ];
    }
}
