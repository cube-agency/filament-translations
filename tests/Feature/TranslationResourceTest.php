<?php

use CubeAgency\FilamentTranslations\Filament\Resources\Translations\Pages\ListTranslations;
use Livewire\Livewire;
use Waavi\Translation\Models\Language;
use Waavi\Translation\Models\Translation;

beforeEach(function () {
    Language::create(['locale' => 'en', 'name' => 'English']);
    Language::create(['locale' => 'lv', 'name' => 'Latvian']);
});

it('renders the list page', function () {
    Livewire::test(ListTranslations::class)->assertStatus(200);
});

it('shows a column per configured locale', function () {
    Livewire::test(ListTranslations::class)
        ->assertStatus(200)
        ->assertSee('EN')
        ->assertSee('LV');
});

it('lists translations pivoted by locale', function () {
    Translation::create([
        'locale' => 'en',
        'namespace' => '*',
        'group' => 'general',
        'item' => 'hello',
        'text' => 'Hello',
    ]);
    Translation::create([
        'locale' => 'lv',
        'namespace' => '*',
        'group' => 'general',
        'item' => 'hello',
        'text' => 'Sveiki',
    ]);

    Livewire::test(ListTranslations::class)
        ->assertStatus(200)
        ->assertSee('general')
        ->assertSee('hello')
        ->assertSee('Hello')
        ->assertSee('Sveiki');
});

it('searches translations by item', function () {
    Translation::create([
        'locale' => 'en',
        'namespace' => '*',
        'group' => 'general',
        'item' => 'hello',
        'text' => 'Hello',
    ]);
    Translation::create([
        'locale' => 'en',
        'namespace' => '*',
        'group' => 'general',
        'item' => 'goodbye',
        'text' => 'Goodbye',
    ]);

    Livewire::test(ListTranslations::class)
        ->set('tableSearch', 'goodbye')
        ->assertSee('Goodbye')
        ->assertDontSee('Hello');
});
