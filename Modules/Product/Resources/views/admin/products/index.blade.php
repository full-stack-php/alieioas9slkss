@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('product::products.products'))

    <li class="breadcrumb-item active">{{ trans('product::products.products') }}</li>
@endcomponent

@section('content')
    <div class="card card-body">
        <div class="card mb-3">
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-3">
                        {{ Form::select(
                            'page_filter_category_id',
                            trans('product::attributes.categories'),
                            $errors,
                            $categories,
                            $selectedFilters['page_filter_category_id'] ?? null,
                            ['class' => 'js-filter']
                        ) }}
                    </div>

                    <div class="col-md-3">
                        {{ Form::select(
                            'page_filter_brand_id',
                            trans('product::attributes.brand_id'),
                            $errors,
                            $brands,
                            $selectedFilters['page_filter_brand_id'] ?? null,
                            ['class' => 'js-filter']
                        ) }}
                    </div>

                    <div class="col-md-3">
                        {{ Form::select(
                            'page_filter_is_active',
                            trans('product::attributes.is_active'),
                            $errors,
                            $statuses,
                            $selectedFilters['page_filter_is_active'] ?? null,
                            ['class' => 'js-filter']
                        ) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="box-body index-table" id="products-table">
            @component('admin::components.table')
                @slot('thead')
                    @include('product::admin.products.partials.thead', ['name' => 'products-index'])
                @endslot
            @endcomponent
        </div>
    </div>
@endsection

@if (session()->has('exit_flash'))
    @push('notifications')
        <div class="alert alert-success fade in alert-dismissible clearfix">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 6.49 17.51 2 12 2ZM11.25 8C11.25 7.59 11.59 7.25 12 7.25C12.41 7.25 12.75 7.59 12.75 8V13C12.75 13.41 12.41 13.75 12 13.75C11.59 13.75 11.25 13.41 11.25 13V8ZM12.92 16.38C12.87 16.51 12.8 16.61 12.71 16.71C12.61 16.8 12.5 16.87 12.38 16.92C12.26 16.97 12.13 17 12 17C11.87 17 11.74 16.97 11.62 16.92C11.5 16.87 11.39 16.8 11.29 16.71C11.2 16.61 11.13 16.51 11.08 16.38C11.03 16.26 11 16.13 11 16C11 15.87 11.03 15.74 11.08 15.62C11.13 15.5 11.2 15.39 11.29 15.29C11.39 15.2 11.5 15.13 11.62 15.08C11.86 14.98 12.14 14.98 12.38 15.08C12.5 15.13 12.61 15.2 12.71 15.29C12.8 15.39 12.87 15.5 12.92 15.62C12.97 15.74 13 15.87 13 16C13 16.13 12.97 16.26 12.92 16.38Z" fill="#555555"/>
            </svg>

            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5.00082 14.9995L14.9999 5.00041" stroke="#555555" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14.9999 14.9996L5.00082 5.00049" stroke="#555555" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <span class="alert-text">{{ session('exit_flash') }}</span>
        </div>
    @endpush
@endif

@push('scripts')
    <script type="module">
        const tableSelector = '#products-table .table';

        const filterNames = [
            'page_filter_category_id',
            'page_filter_brand_id',
            'page_filter_is_active',
        ];

        function getUrlParams() {
            return new URLSearchParams(window.location.search);
        }

        function getFilterValue(name) {
            const value = $('select.js-filter[name="' + name + '"]').val();

            if (value === undefined || value === null || value === '') {
                return null;
            }

            return value;
        }

        function getFilterParams() {
            const params = new URLSearchParams();

            filterNames.forEach(function (name) {
                const value = getFilterValue(name);

                if (value !== null) {
                    params.set(name, value);
                }
            });

            return params;
        }

        function syncFiltersFromUrlToSelects() {
            const params = getUrlParams();

            filterNames.forEach(function (name) {
                const value = params.get(name);
                const $select = $('select.js-filter[name="' + name + '"]');

                if (value === null || value === '') {
                    $select.val('');

                    if ($select[0] && $select[0].choices && typeof $select[0].choices.setChoiceByValue === 'function') {
                        $select[0].choices.setChoiceByValue('');
                    }

                    return;
                }

                $select.val(value);

                if ($select[0] && $select[0].choices && typeof $select[0].choices.setChoiceByValue === 'function') {
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
            routePrefix: 'products',
            routes: {
                table: 'table',
                create: 'create',
                edit: 'edit',
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
                { data: 'thumbnail', orderable: false, searchable: false, width: '10%' },
                { data: 'name', name: 'translations.name', class: 'name', orderable: false, defaultContent: '' },
                { data: 'price', searchable: false },
                { data: 'in_stock', name: 'in_stock', searchable: false },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'updated', name: 'updated_at' },
            ]
        });

        $(tableSelector).on('draw.dt', function () {
            appendFiltersToRowUrls();
        });
    </script>
@endpush
