<?php

namespace CubeAgency\FilamentTranslations\Tests;

use BezhanSalleh\LanguageSwitch\LanguageSwitchServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use CubeAgency\FilamentExcel\FilamentExcelServiceProvider;
use CubeAgency\FilamentTranslations\FilamentTranslationsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\LivewireServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Waavi\Translation\TranslationServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Livewire v4 renders share an "errors" ViewErrorBag with views. Outside an
        // HTTP request the bag is never seeded, so seed an empty one for the tests.
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag));

        // When testing a Livewire component directly (no HTTP request), Filament's panel
        // middleware never runs, so it does not know which panel is active. Set it manually.
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            NotificationsServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            TranslationServiceProvider::class,
            LanguageSwitchServiceProvider::class,
            ExcelServiceProvider::class,
            FilamentExcelServiceProvider::class,
            FilamentTranslationsServiceProvider::class,
            AdminPanelProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:j4TkRHy8hbJCJ255PmYRqn5pvxrhf3QKvJcrBj0M/gY=');
        config()->set('database.default', 'testing');
    }

    protected function defineDatabaseMigrations(): void
    {
        // The Waavi translation package registers its languages/translations
        // migrations via loadMigrationsFrom(), so a plain migrate runs them.
        $this->artisan('migrate')->run();
    }
}
