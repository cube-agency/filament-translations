<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources;

use BackedEnum;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\CreateLanguage;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\EditLanguage;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\ListLanguages;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Schemas\LanguageForm;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Tables\LanguagesTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;
use Waavi\Translation\Models\Language;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-language';
    protected static string | UnitEnum | null $navigationGroup = 'Localization';

    public static function form(Schema $schema): Schema
    {
        return LanguageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
            'create' => CreateLanguage::route('/create'),
            'edit' => EditLanguage::route('/{record}/edit'),
        ];
    }
}
