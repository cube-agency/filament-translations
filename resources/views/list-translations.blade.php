<x-filament-panels::page>
    <div
        class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">
            <div class="fi-ta-header-toolbar flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6">
                <div class="flex shrink-0 items-center gap-x-4"></div>
                <div class="ms-auto flex items-center gap-x-4">
                    <x-filament-tables::search-field
                        :debounce="'500ms'"
                        wire-model="tableSearch"
                    />
                </div>
            </div>
        </div>

        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
            <thead class="divide-y divide-gray-200 dark:divide-white/5">
            <tr class="bg-gray-50 dark:bg-white/5">
                @foreach ($columns as $column)
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 w-1">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                {{ Illuminate\Support\Str::of($column)->snake() }}
                            </span>
                        </span>
                    </th>
                @endforeach
                <th class="w-1"></th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
            @forelse ($translations as $translation)
                <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                    @foreach ($columns as $column)
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                            <div class="fi-ta-col-wrp">
                                <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div class="flex max-w-max">
                                                <div class="fi-ta-text-item inline-flex items-center gap-1.5">
                                                <span
                                                    class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white whitespace-normal"
                                                    style="">
                                                    {{ $translation->{$column} }}
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endforeach

                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                        <div class="whitespace-nowrap px-3 py-4">
                            <div class="fi-ta-actions flex shrink-0 items-center gap-3 justify-end">
                                {{ ($this->editAction)(['translation' => $translation]) }}
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}">
                        <x-filament-tables::empty-state
                            :heading="__('filament-tables::table.empty.heading')"
                            icon="heroicon-o-x-mark"
                        />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="p-3">
            <x-filament::pagination
                :paginator="$translations"
                :page-options="[10, 30, 50, 100]"
                :current-page-option-property="'perPage'"
            />
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
