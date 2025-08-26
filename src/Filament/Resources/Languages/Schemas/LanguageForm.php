<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\Languages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Waavi\Translation\Models\Language;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('locale')
                    ->required()
                    ->unique(Language::class, 'locale', fn($record) => $record)
                    ->rules(['alpha', 'lowercase', 'size:2']),

                TextInput::make('name')
                    ->required()
            ]);
    }
}