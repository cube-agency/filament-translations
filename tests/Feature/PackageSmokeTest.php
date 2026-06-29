<?php

use CubeAgency\FilamentTranslations\Filament\Resources\LanguageResource;
use CubeAgency\FilamentTranslations\Filament\Resources\TranslationResource;
use CubeAgency\FilamentTranslations\FilamentTranslationsPlugin;
use Illuminate\Support\Facades\Schema;
use Waavi\Translation\Models\Language;
use Waavi\Translation\Models\Translation;

it('exposes a plugin with the expected id', function () {
    expect(FilamentTranslationsPlugin::make()->getId())->toBe('filament-translations');
});

it('creates the translation tables from the migrations', function () {
    expect(Schema::hasTable('translator_languages'))->toBeTrue()
        ->and(Schema::hasColumns('translator_languages', ['locale', 'name']))->toBeTrue()
        ->and(Schema::hasTable('translator_translations'))->toBeTrue()
        ->and(Schema::hasColumns('translator_translations', ['locale', 'namespace', 'group', 'item', 'text']))->toBeTrue();
});

it('points the resources at the Waavi models', function () {
    expect(LanguageResource::getModel())->toBe(Language::class)
        ->and(TranslationResource::getModel())->toBe(Translation::class);
});
