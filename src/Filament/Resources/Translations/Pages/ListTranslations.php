<?php

namespace CubeAgency\FilamentTranslations\Filament\Resources\Translations\Pages;

use BackedEnum;
use CubeAgency\FilamentTranslations\Filament\Exports\TranslationsExport;
use CubeAgency\FilamentTranslations\Filament\Imports\TranslationsImport;
use CubeAgency\FilamentTranslations\Filament\Resources\TranslationResource;
use CubeAgency\FilamentTranslations\Traits\UsesLocalization;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use Waavi\Translation\Facades\TranslationCache;
use Waavi\Translation\Models\Translation;

class ListTranslations extends ListRecords
{
    use InteractsWithTable;
    use UsesLocalization;

    protected static string $resource = TranslationResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('namespace'),
                TextColumn::make('group'),
                TextColumn::make('item'),
                ...$this->getLanguageColumns(),
            ])
            ->records(function (string $search = null, int $page, int $recordsPerPage): LengthAwarePaginator {
                $query = $this->getTranslationsQuery();

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('d1.item', 'like', "%$search%")
                            ->orWhere('d1.group', 'like', "%$search%")
                            ->orWhere('d1.namespace', 'like', "%$search%");

                        foreach ($this->languageRepository()->all() as $language) {
                            $q->orWhere('l_' . $language->locale . '.text', 'like', "%$search%");
                        }
                    });
                }

                $total = $query->count();
                $results = $query
                    ->forPage($page, $recordsPerPage)
                    ->get()
                    ->map(fn($result) => (array)$result);

                return new LengthAwarePaginator(
                    $results->toArray(),
                    $total,
                    $recordsPerPage,
                    $page
                );
            })
            ->recordActions([
                $this->editAction()
            ])
            ->searchable();
    }

    protected function getLanguageColumns(): array
    {
        $languages = $this->languageRepository()->all();
        $columns = [];

        foreach ($languages as $language) {
            $columns[] = TextColumn::make($language->locale)
                ->label(strtoupper($language->locale))
                ->state(function (array $record) use ($languages, $language): ?string {
                    $limit = 100 / count($languages);

                    return Str::limit($record[$language->locale] ?? '', $limit);
                });
        }

        return $columns;
    }

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
            ->fillForm(fn(array $record) => $this->fillForm($record))
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

    protected function fillForm(array $record): array
    {
        $translationData = $record;
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
