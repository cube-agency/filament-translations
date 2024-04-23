<?php

namespace CubeAgency\FilamentTranslations\Filament\Exports;

use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TranslationsExport extends ExcelExport
{
    use UsesLocalization;

    public function setUp(): void
    {
        $columns = [
            Column::make('namespace'),
            Column::make('group'),
            Column::make('item'),
        ];

        foreach ($this->languageRepository()->all() as $language) {
            $columns[] = Column::make($language->locale);
        }

        $this->withColumns($columns);
    }

    public function query()
    {
        $query = $this->getTranslationsQuery();

        if ($this->isQueued()) {
            $this->livewire = null;
        }

        return $query->orderBy('group');
    }
}
