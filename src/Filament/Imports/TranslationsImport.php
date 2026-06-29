<?php

namespace CubeAgency\FilamentTranslations\Filament\Imports;

use CubeAgency\FilamentExcel\Imports\Columns\ImportColumn;
use CubeAgency\FilamentExcel\Imports\ExcelImport;
use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use Waavi\Translation\Facades\TranslationCache;

class TranslationsImport extends ExcelImport
{
    use UsesLocalization;

    protected function setUp(): void
    {
        $this->columns(function (): array {
            $columns = [
                ImportColumn::make('namespace')->required(),
                ImportColumn::make('group')->required(),
                ImportColumn::make('item')->required(),
            ];

            foreach ($this->languageRepository()->all() as $language) {
                $columns[] = ImportColumn::make($language->locale);
            }

            return $columns;
        });

        $this->saveUsing(fn (array $data) => $this->importRow($data));

        $this->afterImport(fn () => TranslationCache::flushAll());
    }

    protected function importRow(array $data): void
    {
        foreach ($this->languageRepository()->all() as $language) {
            $locale = $language->locale;

            if (! array_key_exists($locale, $data) || $data[$locale] === null) {
                continue;
            }

            $translation = $this->translationRepository()->findByCode(
                $locale,
                $data['namespace'],
                $data['group'],
                $data['item'],
            );

            if (! $translation) {
                $this->translationRepository()->create([
                    'locale' => $locale,
                    'namespace' => $data['namespace'],
                    'group' => $data['group'],
                    'item' => $data['item'],
                    'text' => $data[$locale],
                ]);

                continue;
            }

            $this->translationRepository()->updateAndLock(
                $translation->id,
                $data[$locale],
            );
        }
    }
}
