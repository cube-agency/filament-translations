<?php

namespace CubeAgency\FilamentTranslations\Tests;

use CubeAgency\FilamentTranslations\FilamentTranslationsPlugin;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->plugin(FilamentTranslationsPlugin::make());
    }
}
