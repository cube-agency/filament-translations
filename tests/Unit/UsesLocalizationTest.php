<?php

use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use Waavi\Translation\Models\Language;
use Waavi\Translation\Models\Translation;

function localizationHarness(): object
{
    return new class
    {
        use UsesLocalization {
            getTranslationsQuery as public;
        }
    };
}

beforeEach(function () {
    Language::create(['locale' => 'en', 'name' => 'English']);
    Language::create(['locale' => 'lv', 'name' => 'Latvian']);
});

it('pivots translations into one row with a column per locale', function () {
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

    $rows = localizationHarness()->getTranslationsQuery()->get();

    expect($rows)->toHaveCount(1);

    $row = (array) $rows->first();

    expect($row['namespace'])->toBe('*')
        ->and($row['group'])->toBe('general')
        ->and($row['item'])->toBe('hello')
        ->and($row['en'])->toBe('Hello')
        ->and($row['lv'])->toBe('Sveiki');
});

it('leaves a missing translation null for that locale', function () {
    Translation::create([
        'locale' => 'en',
        'namespace' => '*',
        'group' => 'general',
        'item' => 'hello',
        'text' => 'Hello',
    ]);

    $row = (array) localizationHarness()->getTranslationsQuery()->get()->first();

    expect($row['en'])->toBe('Hello')
        ->and($row['lv'])->toBeNull();
});
