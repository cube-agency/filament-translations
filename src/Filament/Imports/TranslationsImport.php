<?php

namespace CubeAgency\FilamentTranslations\Filament\Imports;

use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waavi\Translation\Facades\TranslationCache;

class TranslationsImport implements ToArray, WithHeadingRow
{
    use UsesLocalization;

    public function array(array $array)
    {
        foreach ($array as $item) {
            $namespace = $item['namespace'];
            $group = $item['group'];

            foreach ($this->languageRepository()->all() as $language) {
                $text = $language->locale;

                if (!isset($item[$text])) {
                    continue;
                }

                $translation = $this->translationRepository()->findByCode(
                    $language->locale,
                    $namespace,
                    $group,
                    $item['item']
                );

                if (!$translation) {
                    $this->translationRepository()->create([
                        'locale' => $language->locale,
                        'namespace' => $namespace,
                        'group' => $group,
                        'item' => $item['item'],
                        'text' => $item[$text] ?? ''
                    ]);

                    continue;
                }

                $this->translationRepository()->updateAndLock(
                    $translation->id,
                    $item[$text] ?? ''
                );
            }
        }

        TranslationCache::flushAll();
    }
}
