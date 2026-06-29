<?php

use CubeAgency\FilamentTranslations\Filament\Exports\TranslationsExport;
use CubeAgency\FilamentTranslations\Filament\Imports\TranslationsImport;
use CubeAgency\FilamentTranslations\Filament\Resources\Translations\Pages\ListTranslations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Waavi\Translation\Models\Language;
use Waavi\Translation\Models\Translation;

beforeEach(function () {
    Language::create(['locale' => 'en', 'name' => 'English']);
    Language::create(['locale' => 'lv', 'name' => 'Latvian']);
});

function createTranslationsXlsx(array $rows): string
{
    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray($rows);

    $path = tempnam(sys_get_temp_dir(), 'filament-translations-test') . '.xlsx';

    (new Xlsx($spreadsheet))->save($path);

    return $path;
}

it('exports translations pivoted by locale', function () {
    Storage::fake('local');

    Translation::create(['locale' => 'en', 'namespace' => '*', 'group' => 'general', 'item' => 'hello', 'text' => 'Hello']);
    Translation::create(['locale' => 'lv', 'namespace' => '*', 'group' => 'general', 'item' => 'hello', 'text' => 'Sveiki']);

    TranslationsExport::make()->store('translations.xlsx', 'local');

    $sheet = IOFactory::load(Storage::disk('local')->path('translations.xlsx'))->getActiveSheet();

    expect($sheet->getCell('A1')->getValue())->toBe('Namespace')
        ->and($sheet->getCell('D1')->getValue())->toBe('En')
        ->and($sheet->getCell('E1')->getValue())->toBe('Lv')
        ->and($sheet->getCell('C2')->getValue())->toBe('hello')
        ->and($sheet->getCell('D2')->getValue())->toBe('Hello')
        ->and($sheet->getCell('E2')->getValue())->toBe('Sveiki');
});

it('imports translations creating and updating per locale', function () {
    Translation::create(['locale' => 'en', 'namespace' => '*', 'group' => 'general', 'item' => 'hello', 'text' => 'Old text']);

    $path = createTranslationsXlsx([
        ['namespace', 'group', 'item', 'en', 'lv'],
        ['*', 'general', 'hello', 'Hello', 'Sveiki'],
        ['*', 'general', 'goodbye', 'Goodbye', null],
    ]);

    TranslationsImport::make()->import($path);

    expect(Translation::where(['item' => 'hello', 'locale' => 'en'])->value('text'))->toBe('Hello')
        ->and(Translation::where(['item' => 'hello', 'locale' => 'lv'])->value('text'))->toBe('Sveiki')
        ->and(Translation::where(['item' => 'goodbye', 'locale' => 'en'])->value('text'))->toBe('Goodbye')
        ->and(Translation::where(['item' => 'goodbye', 'locale' => 'lv'])->exists())->toBeFalse();
});

it('imports a file through the page action without mapping', function () {
    Storage::fake('local');

    $path = createTranslationsXlsx([
        ['namespace', 'group', 'item', 'en', 'lv'],
        ['*', 'general', 'welcome', 'Welcome', 'Laipni lūdzam'],
    ]);

    $file = UploadedFile::fake()->createWithContent('translations.xlsx', file_get_contents($path));

    Livewire::test(ListTranslations::class)
        ->callAction('excelImport', ['file' => $file])
        ->assertNotified();

    expect(Translation::where(['item' => 'welcome', 'locale' => 'lv'])->value('text'))->toBe('Laipni lūdzam');
});

it('re-imports its own export through the page action', function () {
    Storage::fake('local');

    Translation::create(['locale' => 'en', 'namespace' => '*', 'group' => 'general', 'item' => 'hello', 'text' => 'Hello']);
    Translation::create(['locale' => 'lv', 'namespace' => '*', 'group' => 'general', 'item' => 'hello', 'text' => 'Sveiki']);

    TranslationsExport::make()->store('translations.xlsx', 'local');

    $file = UploadedFile::fake()->createWithContent(
        'translations.xlsx',
        Storage::disk('local')->get('translations.xlsx'),
    );

    Translation::query()->delete();

    Livewire::test(ListTranslations::class)
        ->callAction('excelImport', ['file' => $file])
        ->assertNotified();

    expect(Translation::count())->toBe(2)
        ->and(Translation::where(['item' => 'hello', 'locale' => 'lv'])->value('text'))->toBe('Sveiki');
});

it('skips rows missing required key columns and reports them', function () {
    $path = createTranslationsXlsx([
        ['namespace', 'group', 'item', 'en'],
        ['*', 'general', 'valid', 'Valid'],
        ['*', '', 'broken', 'Broken'],
    ]);

    $import = TranslationsImport::make();
    $import->import($path);

    expect(Translation::where('item', 'valid')->exists())->toBeTrue()
        ->and(Translation::where('item', 'broken')->exists())->toBeFalse()
        ->and($import->getGroupedFailedRows())->toHaveCount(1);
});
