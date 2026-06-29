<?php

namespace CubeAgency\FilamentTranslations\Filament\Exports;

use CubeAgency\FilamentExcel\Exports\Columns\Column;
use CubeAgency\FilamentExcel\Exports\ExcelExport;
use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use Illuminate\Database\Query\Builder;

class TranslationsExport extends ExcelExport
{
    use UsesLocalization;

    protected function setUp(): void
    {
        $this->filename('translations');

        $this->columns(function (): array {
            $columns = [
                Column::make('namespace'),
                Column::make('group'),
                Column::make('item'),
            ];

            foreach ($this->languageRepository()->all() as $language) {
                $columns[] = Column::make($language->locale);
            }

            return $columns;
        });
    }

    public function query(): Builder
    {
        return $this->getTranslationsQuery()->orderBy('group');
    }
}
