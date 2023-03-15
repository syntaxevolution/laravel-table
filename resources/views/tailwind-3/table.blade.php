<div wire:init="init">
    @if($initialized)
        @if($orderColumn)
            <div class="relative px-3 py-3 mb-4 border rounded bg-teal-200 border-teal-300 text-teal-800" role="alert">
                {{ __('You can rearrange the order of the items in this list using a drag and drop action.') }}
            </div>
        @endif
        <div class="block w-full overflow-auto scrolling-touch">
            <table class="w-full max-w-full mb-4 bg-transparent table-borderless">
                {{-- Table header--}}
                <thead>
                {{-- Filters --}}
                @if($filtersArray)
                    <tr>
                        <td class="px-0 pb-0"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                            <div class="flex flex-wrap items-center justify-end mt-n2">
                                <div class="text-gray-600 mt-2">
                                    {!! config('laravel-table.icon.filter') !!}
                                </div>
                                @foreach($filtersArray as $filterArray)
                                    @unless($resetFilters)
                                        <div wire:ignore>
                                            @endif
                                            {!! Okipa\LaravelTable\Abstracts\AbstractFilter::make($filterArray)->render() !!}
                                            @unless($resetFilters)
                                        </div>
                                    @endif
                                @endforeach
                                @if(collect($this->selectedFilters)->filter(fn(mixed $filter) => isset($filter) && $filter !== '' && $filter !== [])->isNotEmpty())
                                    <a wire:click.prevent="resetFilters()"
                                       class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline text-gray-600 border-gray-600 hover:bg-gray-600 hover:text-white bg-white hover:bg-gray-700 ms-3 mt-2"
                                       title="{{ __('Reset filters') }}"
                                       data-bs-toggle="tooltip">
                                        {!! config('laravel-table.icon.reset') !!}
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                {{-- Search/Number of rows per page/Head action --}}
                <tr>
                    <td class="px-0"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                        <div class="flex flex-col xl:flex-row">
                            {{-- Search --}}
                            <div class="flex-fill">
                                @if($searchableLabels)
                                    <div class="flex-fill xl:pe-4 py-1">
                                        <form wire:submit.prevent="$refresh">
                                            <div class="relative flex items-stretch w-full">
                                                    <span id="search-for-rows" class="input-group-text">
                                                        {!! config('laravel-table.icon.search') !!}
                                                    </span>
                                                <input wire:model.defer="searchBy"
                                                       class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded"
                                                       placeholder="{{ __('Search by:') }} {{ $searchableLabels }}"
                                                       aria-label="{{ __('Search by:') }} {{ $searchableLabels }}"
                                                       aria-describedby="search-for-rows">
                                                <span class="input-group-text">
                                                        <button class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  font-normal text-blue-700 bg-transparent link-primary p-0"
                                                                type="submit"
                                                                title="{{ __('Search by:') }} {{ $searchableLabels }}">
                                                            {!! config('laravel-table.icon.validate') !!}
                                                        </button>
                                                    </span>
                                                @if($searchBy)
                                                    <span class="input-group-text">
                                                            <a wire:click.prevent="$set('searchBy', ''), $refresh"
                                                               class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  font-normal text-blue-700 bg-transparent link-secondary p-0"
                                                               title="{{ __('Reset research') }}">
                                                                {!! config('laravel-table.icon.reset') !!}
                                                            </a>
                                                        </span>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                {{-- Number of rows per page --}}
                                @if($numberOfRowsPerPageChoiceEnabled)
                                    <div wire:ignore @class(['px-xl-3' => $headActionArray, 'ps-xl-3' => ! $headActionArray, 'py-1'])>
                                        <div class="relative flex items-stretch w-full">
                                                <span id="rows-number-per-page-icon" class="input-group-text text-gray-600">
                                                    {!! config('laravel-table.icon.rows_number') !!}
                                                </span>
                                            <select wire:change="changeNumberOfRowsPerPage($event.target.value)" class="form-select" {!! (new \Illuminate\View\ComponentAttributeBag())->merge([
                                                    'placeholder' => __('Number of rows per page'),
                                                    'aria-label' => __('Number of rows per page'),
                                                    'aria-describedby' => 'rows-number-per-page-icon',
                                                    ...config('laravel-table.html_select_components_attributes'),
                                                ])->toHtml() !!}>
                                                <option wire:key="rows-number-per-page-option-placeholder" value="" disabled>{{ __('Number of rows per page') }}</option>
                                                @foreach($numberOfRowsPerPageOptions as $numberOfRowsPerPageOption)
                                                    <option wire:key="rows-number-per-page-option-{{ $numberOfRowsPerPageOption }}" value="{{ $numberOfRowsPerPageOption }}"{{ $numberOfRowsPerPageOption === $numberOfRowsPerPage ? ' selected' : null}}>
                                                        {{ $numberOfRowsPerPageOption }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                {{-- Head action --}}
                                @if($headActionArray)
                                    <div class="flex items-center ps-3 py-1">
                                        {{ Okipa\LaravelTable\Abstracts\AbstractHeadAction::make($headActionArray)->render() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                {{-- Column headings --}}
                <tr class="bg-gray-100 border-t border-b">
                    {{-- Bulk actions --}}
                    @if($tableBulkActionsArray)
                        <th wire:key="bulk-actions" class="align-middle" scope="col">
                            <div class="flex items-center">
                                {{-- Bulk actions select all --}}
                                <input wire:model="selectAll" class="me-1" type="checkbox" aria-label="Check all displayed lines">
                                {{-- Bulk actions dropdown --}}
                                <div class="relative" title="{{ __('Bulk Actions') }}" data-bs-toggle="tooltip">
                                    <a id="bulk-actions-dropdown"
                                       class=" inline-block w-0 h-0 ml-1 align border-b-0 border-t-1 border-r-1 border-l-1"
                                       type="button"
                                       data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                    </a>
                                    <ul class=" absolute left-0 z-50 float-left hidden list-reset	 py-2 mt-1 text-base bg-white border border-gray-300 rounded" aria-labelledby="bulk-actions-dropdown">
                                        @foreach($tableBulkActionsArray as $bulkActionArray)
                                            {{ Okipa\LaravelTable\Abstracts\AbstractBulkAction::make($bulkActionArray)->render() }}
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </th>
                    @endif
                    {{-- Sorting/Column titles --}}
                    @foreach($columns as $column)
                        <th wire:key="column-{{ Str::of($column->getAttribute())->snake('-')->slug() }}" class="align-middle" scope="col">
                            @if($column->isSortable($orderColumn))
                                @if($sortBy === $column->getAttribute())
                                    <a wire:click.prevent="sortBy('{{ $column->getAttribute() }}')"
                                       class="flex items-center"
                                       href=""
                                       title="{{ $sortDir === 'asc' ? __('Sort descending') : __('Sort ascending') }}"
                                       data-bs-toggle="tooltip">
                                        {!! $sortDir === 'asc'
                                            ? config('laravel-table.icon.sort_desc')
                                            : config('laravel-table.icon.sort_asc') !!}
                                        <span class="ms-2">{{ $column->getTitle() }}</span>
                                    </a>
                                @else
                                    <a wire:click.prevent="sortBy('{{ $column->getAttribute() }}')"
                                       class="flex items-center"
                                       href=""
                                       title="{{ __('Sort ascending') }}"
                                       data-bs-toggle="tooltip">
                                        {!! config('laravel-table.icon.sort') !!}
                                        <span class="ms-2">{{ $column->getTitle() }}</span>
                                    </a>
                                @endif
                            @else
                                {{ $column->getTitle() }}
                            @endif
                        </th>
                    @endforeach
                    {{-- Row actions --}}
                    @if($tableRowActionsArray)
                        <th wire:key="row-actions" class="align-middle text-end" scope="col">
                            {{ __('Actions') }}
                        </th>
                    @endif
                </tr>
                </thead>
                {{-- Table body--}}
                <tbody{!! $orderColumn ? ' wire:sortable="reorder"' : null !!}>
                {{-- Rows --}}
                @forelse($rows as $model)
                    <tr wire:key="row-{{ $model->getKey() }}"{!! $orderColumn ? ' wire:sortable.item="' . $model->getKey() . '"' : null !!} @class(array_merge(Arr::get($tableRowClass, $model->laravel_table_unique_identifier, []), ['border-bottom']))>
                        {{-- Row bulk action selector --}}
                        @if($tableBulkActionsArray)
                            <td class="align-middle">
                                <input wire:model="selectedModelKeys" type="checkbox" value="{{ $model->getKey() }}" aria-label="Check line {{ $model->getKey() }}">
                            </td>
                        @endif
                        {{-- Row columns values --}}
                        @foreach($columns as $column)
                            @if($loop->first)
                                <th wire:key="cell-{{ Str::of($column->getAttribute())->snake('-')->slug() }}-{{ $model->getKey() }}"{!! $orderColumn ? ' wire:sortable.handle style="cursor: move;"' : null !!} class="align-middle" scope="row">
                                    {!! $orderColumn ? '<span class="me-2">' . config('laravel-table.icon.drag_drop') . '</span>' : null !!}{{ $column->getValue($model, $tableColumnActionsArray) }}
                                </th>
                            @else
                                <td wire:key="cell-{{ Str::of($column->getAttribute())->snake('-')->slug() }}-{{ $model->getKey() }}" class="align-middle">
                                    {{ $column->getValue($model, $tableColumnActionsArray) }}
                                </td>
                            @endif
                        @endforeach
                        {{-- Row actions --}}
                        @if($tableRowActionsArray)
                            <td class="align-middle text-end">
                                <div class="flex items-center justify-end">
                                    @if($rowActionsArray = Okipa\LaravelTable\Abstracts\AbstractRowAction::retrieve($tableRowActionsArray, $model->getKey()))
                                        @foreach($rowActionsArray as $rowActionArray)
                                            {{ Okipa\LaravelTable\Abstracts\AbstractRowAction::make($rowActionArray)->render($model) }}
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr class="border-b">
                        <th class="fw-normal text-center align-middle p-6" scope="row"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                                <span class="text-teal-500">
                                    {!! config('laravel-table.icon.info') !!}
                                </span>
                            {{ __('No results were found.') }}
                        </th>
                    </tr>
                @endforelse
                </tbody>
                {{-- Table footer--}}
                <tfoot class="bg-gray-100">
                {{-- Results --}}
                @foreach($results as $result)
                    <tr wire:key="result-{{ Str::of($result->getTitle())->snake('-')->slug() }}" class="border-b">
                        <td class="align-middle fw-bold"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                            <div class="flex flex-wrap justify-between">
                                <div class="px-2 py-1">{{ $result->getTitle() }}</div>
                                <div class="px-2 py-1">{{ $result->getValue() }}</div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="align-middle"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                        <div class="flex flex-wrap justify-between">
                            <div class="flex items-center p-2">
                                <div wire:key="navigation-status">{!! $navigationStatus !!}</div>
                            </div>
                            <div class="flex items-center mb-n3 p-2">
                                {!! $rows->links() !!}
                            </div>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="flex items-center py-3">
            <div class="spinner-border text-gray-900 me-3" role="status">
                <span class="visually-hidden">{{ __('Loading in progress...') }}</span>
            </div>
            {{ __('Loading in progress...') }}
        </div>
    @endif
</div>
