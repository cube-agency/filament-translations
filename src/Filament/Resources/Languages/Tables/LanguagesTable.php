<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\Languages\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('locale'),
                TextColumn::make('name'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}