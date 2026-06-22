@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('contact::contact_submissions.contact_submissions'))

    <li class="breadcrumb-item active">
        {{ trans('contact::contact_submissions.contact_submissions') }}
    </li>
@endcomponent

@section('content')
    <div class="card card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                {{ Form::select(
                    'type',
                    trans('contact::contact_submissions.table.type'),
                    $errors,
                    $types,
                    $selectedFilters['type'] ?? null,
                    ['class' => 'js-filter']
                ) }}
            </div>

            <div class="col-md-3">
                {{ Form::select(
                    'read',
                    trans('contact::contact_submissions.table.read_status'),
                    $errors,
                    $readStatuses,
                    $selectedFilters['read'] ?? null,
                    ['class' => 'js-filter']
                ) }}
            </div>

            <div class="col-md-3">
                {{ Form::select(
                    'processed',
                    trans('contact::contact_submissions.table.processed_status'),
                    $errors,
                    $processedStatuses,
                    $selectedFilters['processed'] ?? null,
                    ['class' => 'js-filter']
                ) }}
            </div>
        </div>

        <div class="box-body index-table" id="contact-submissions-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')

                        <th>{{ trans('admin::admin.table.id') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.type') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.customer') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.contacts') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.source_url') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.read_status') }}</th>
                        <th>{{ trans('contact::contact_submissions.table.processed_status') }}</th>
                        <th data-sort>{{ trans('admin::admin.table.date') }}</th>
                    </tr>
                @endslot
            @endcomponent
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        const tableSelector = '#contact-submissions-table .table';
        const filterNames = ['type', 'read', 'processed'];

        function getUrlParams() {
            return new URLSearchParams(window.location.search);
        }

        function getFilterValue(name) {
            const $select = $('select.js-filter[name="' + name + '"]');
            const selectValue = $select.val();

            if (selectValue !== null && selectValue !== '') {
                return selectValue;
            }

            const urlValue = getUrlParams().get(name);

            if (urlValue !== null && urlValue !== '') {
                return urlValue;
            }

            return null;
        }

        function getFilterParams() {
            const params = new URLSearchParams();

            filterNames.forEach(function (name) {
                const value = getFilterValue(name);

                if (value !== null && value !== '') {
                    params.set(name, value);
                }
            });

            return params;
        }

        function syncFiltersFromUrlToSelects() {
            const params = getUrlParams();

            filterNames.forEach(function (name) {
                const value = params.get(name);

                if (value === null) {
                    return;
                }

                const $select = $('select.js-filter[name="' + name + '"]');

                $select.val(value);

                /**
                 * Учитываем Choices.js.
                 * Важно: читаем и меняем оригинальный select,
                 * а не .choices__list / .choices__list--dropdown.
                 */
                if ($select[0] && $select[0].choices) {
                    $select[0].choices.setChoiceByValue(value);
                }
            });
        }

        function syncUrlFromFilters() {
            const params = getFilterParams();
            const queryString = params.toString();

            const url = queryString
                ? window.location.pathname + '?' + queryString
                : window.location.pathname;

            window.history.replaceState({}, '', url);
        }

        function appendFiltersToRowUrls() {
            const queryString = getFilterParams().toString();

            $(tableSelector).find('tbody tr.clickable-row').each(function () {
                const currentHref = $(this).data('href');

                if (!currentHref) {
                    return;
                }

                const baseHref = String(currentHref).split('?')[0];
                const newHref = queryString ? baseHref + '?' + queryString : baseHref;

                $(this).data('href', newHref);
            });
        }

        syncFiltersFromUrlToSelects();

        $('.js-filter').on('change', function () {
            syncUrlFromFilters();
        });

        DataTable.set(tableSelector, {
            routePrefix: 'contact-submissions',
            routes: {
                table: 'table',
                show: 'show',
                destroy: 'destroy',
            }
        });

        new DataTable(tableSelector, {
            ajax: {
                data: function (data) {
                    filterNames.forEach(function (name) {
                        const value = getFilterValue(name);

                        if (value !== null && value !== '') {
                            data[name] = value;
                        }
                    });
                }
            },

            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'type', name: 'type' },
                { data: 'customer', name: 'name' },
                { data: 'contacts', orderable: false, searchable: false },
                { data: 'source_url', name: 'source_url', orderable: false },
                { data: 'read_status', name: 'read_at', searchable: false },
                { data: 'processed_status', name: 'processed_at', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });

        $(tableSelector).on('draw.dt', function () {
            appendFiltersToRowUrls();
        });
    </script>
@endpush
