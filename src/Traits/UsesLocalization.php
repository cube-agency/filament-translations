<?php

namespace CubeAgency\FilamentTranslations\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Waavi\Translation\Models\Translation;
use Waavi\Translation\Repositories\LanguageRepository;
use Waavi\Translation\Repositories\TranslationRepository;

trait UsesLocalization
{
    protected function getTranslationsQuery(): Builder
    {
        $languages = $this->languageRepository()->all();
        $allItems = Translation::query()->distinct()->select('item', 'group', 'namespace');

        $translationsQuery = DB::table(DB::raw('(' . $allItems->toSql() . ') as d1'));
        $translationsQuery->addSelect('d1.*');

        $translationsTableName = (new Translation())->getTable();

        foreach ($languages as $language) {
            $locale = $language->locale;
            $joinAlias = 'l_' . $locale;

            $translationsQuery->addSelect($joinAlias . '.text AS ' . $locale);
            $translationsQuery->leftJoin(
                $translationsTableName . ' as l_' . $locale,
                function (JoinClause $join) use ($joinAlias, $locale) {
                    $join
                        ->on($joinAlias . '.group', '=', 'd1.group')
                        ->on($joinAlias . '.item', '=', 'd1.item')
                        ->on($joinAlias . '.locale', '=', DB::raw('\'' . $locale . '\''));
                }
            );
        }

        return $translationsQuery;
    }

    protected function languageRepository(): LanguageRepository
    {
        return app(LanguageRepository::class);
    }

    protected function translationRepository(): TranslationRepository
    {
        return app(TranslationRepository::class);
    }
}
