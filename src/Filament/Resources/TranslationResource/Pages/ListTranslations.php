<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\TranslationResource\Pages;

use CubeAgency\FilamentTranslations\Filament\Exports\TranslationsExport;
use CubeAgency\FilamentTranslations\Filament\Imports\TranslationsImport;
use CubeAgency\FilamentTranslations\Filament\Resources\TranslationResource;
use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use Waavi\Translation\Facades\TranslationCache;
use Waavi\Translation\Models\Translation;

class ListTranslations extends ListRecords
{
    use UsesLocalization;

    protected static string $resource = TranslationResource::class;

    protected static string $view = 'filament-translations::list-translations';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public int|string $perPage = 10;
    public $tableSearch = '';

    public function getActions(): array
    {
        return [
            ExcelImportAction::make()
                ->use(TranslationsImport::class),
            ExportAction::make()
                ->exports([
                    TranslationsExport::make()
                ])
        ];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->fillForm(fn(array $data, array $arguments) => $this->fillForm($arguments))
            ->form(function () {
                $schema = [
                    Hidden::make('namespace'),
                    Hidden::make('group'),
                    Hidden::make('item'),
                ];

                foreach ($this->languageRepository()->all() as $language) {
                    $schema[] = Textarea::make($language->locale)
                        ->required();
                }

                return $schema;
            })
            ->action(fn(array $data) => $this->updateTranslations($data));
    }

    public function updatedTableSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    protected function getViewData(): array
    {
        return [
            'translations' => $this->getTranslations(),
            'columns' => $this->getColumns()
        ];
    }

    protected function getTranslations(): LengthAwarePaginator
    {
        $languages = $this->languageRepository()->all();
        $translationsQuery = $this->getTranslationsQuery();

        if ($this->tableSearch) {
            $translationsQuery->where('d1.group', 'LIKE', '%' . $this->tableSearch . '%');
            $translationsQuery->orWhere('d1.namespace', 'LIKE', '%' . $this->tableSearch . '%');
            $translationsQuery->orWhere('d1.item', 'LIKE', '%' . $this->tableSearch . '%');

            foreach ($languages as $language) {
                $translationsQuery->orWhere('l_' . $language->locale . '.text', 'LIKE', '%' . $this->tableSearch . '%');
            }
        }

        return $translationsQuery->paginate($this->perPage);
    }

    protected function getColumns(): array
    {
        $columns = [
            'namespace',
            'group',
            'item'
        ];

        foreach ($this->languageRepository()->all() as $language) {
            $columns[] = $language->locale;
        }

        return $columns;
    }

    protected function fillForm(array $arguments): array
    {
        $translationData = $arguments['translation'] ?? [];
        $namespace = $translationData['namespace'];
        $group = str_replace('.', '/', $translationData['group']);
        $item = $translationData['item'];
        $translationKey = $namespace . '::' . $group . '.' . $item;

        foreach ($this->languageRepository()->all() as $language) {
            $locale = $language->locale;

            $translation = $this->translationRepository()->findByCode(
                $locale,
                $namespace,
                $group,
                $item
            );

            if (!$translation) {
                $translation = new Translation([
                    'locale' => $locale,
                    'namespace' => $namespace,
                    'group' => $group,
                    'item' => $item,
                    'text' => $translationKey,
                ]);
                $translation->save();
            }

            $translationData[$locale] = $translation->text;
        }

        return $translationData;
    }

    protected function updateTranslations(array $data): void
    {
        foreach ($this->languageRepository()->all() as $language) {
            $locale = $language->locale;

            $translation = $this->translationRepository()->findByCode(
                $locale,
                $data['namespace'],
                $data['group'],
                $data['item']
            );

            $this->translationRepository()->updateAndLock(
                $translation->id,
                $data[$locale]
            );

            TranslationCache::flush($locale, $data['group'], $data['namespace']);
        }
    }
}
