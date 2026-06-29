<?php

use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\CreateLanguage;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\EditLanguage;
use CubeAgency\FilamentTranslations\Filament\Resources\Languages\Pages\ListLanguages;
use Livewire\Livewire;
use Waavi\Translation\Models\Language;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

it('renders the list page', function () {
    Livewire::test(ListLanguages::class)->assertStatus(200);
});

it('lists existing languages', function () {
    $language = Language::create(['locale' => 'en', 'name' => 'English']);

    Livewire::test(ListLanguages::class)
        ->assertStatus(200)
        ->assertCanSeeTableRecords([$language])
        ->assertSee('English');
});

it('can create a language', function () {
    Livewire::test(CreateLanguage::class)
        ->fillForm([
            'locale' => 'lv',
            'name' => 'Latvian',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Language::class, [
        'locale' => 'lv',
        'name' => 'Latvian',
    ]);
});

it('validates that locale and name are required', function () {
    Livewire::test(CreateLanguage::class)
        ->fillForm([
            'locale' => null,
            'name' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['locale', 'name']);
});

it('validates the locale format', function () {
    Livewire::test(CreateLanguage::class)
        ->fillForm([
            'locale' => 'ENG',
            'name' => 'English',
        ])
        ->call('create')
        ->assertHasFormErrors(['locale']);
});

it('rejects a duplicate locale', function () {
    Language::create(['locale' => 'en', 'name' => 'English']);

    Livewire::test(CreateLanguage::class)
        ->fillForm([
            'locale' => 'en',
            'name' => 'English (US)',
        ])
        ->call('create')
        ->assertHasFormErrors(['locale']);
});

it('can edit a language', function () {
    $language = Language::create(['locale' => 'lv', 'name' => 'Latvian']);

    Livewire::test(EditLanguage::class, ['record' => $language->getRouteKey()])
        ->fillForm(['name' => 'Latviešu'])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Language::class, [
        'id' => $language->id,
        'name' => 'Latviešu',
    ]);
});

it('can delete a language from the edit page', function () {
    $language = Language::create(['locale' => 'lv', 'name' => 'Latvian']);

    Livewire::test(EditLanguage::class, ['record' => $language->getRouteKey()])
        ->callAction('delete');

    assertSoftDeleted(Language::class, ['id' => $language->id]);
});
