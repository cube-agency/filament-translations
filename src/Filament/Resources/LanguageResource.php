<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources;

use CubeAgency\FilamentTranslations\Filament\Resources\LanguageResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Waavi\Translation\Models\Language;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationGroup = 'Localization';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('locale')
                    ->required()
                    ->unique(Language::class, 'locale', fn($record) => $record)
                    ->rules(['alpha', 'lowercase', 'size:2']),
                TextInput::make('name')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('locale'),
                TextColumn::make('name'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
