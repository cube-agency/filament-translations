<?php

namespace CubeAgency\FilamentTranslations;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Waavi\Translation\Repositories\LanguageRepository;

class FilamentTranslationsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translations';

    public static string $viewNamespace = 'filament-translations';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales($this->getLocales());
        });
    }

    protected function getLocales(): array
    {
        return app(LanguageRepository::class)->all()->pluck('locale')->toArray();
    }
}
