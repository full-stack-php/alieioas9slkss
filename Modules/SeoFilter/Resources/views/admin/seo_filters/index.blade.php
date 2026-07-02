@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('seo_filter::seo_filters.seo_filters'))

    <li class="breadcrumb-item active">{{ trans('seo_filter::seo_filters.seo_filters') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'seo_filters')
    @slot('name', trans('seo_filter::seo_filters.seo_filter'))

    @component('admin::components.table')
        @slot('thead')
            <tr>
                @include('admin::partials.table.select_all')

                <th>{{ trans('admin::admin.table.id') }}</th>
                <th>URL</th>
                <th>{{ trans('seo_filter::seo_filters.table.category') }}</th>
                <th>H1</th>
                <th>{{ trans('admin::admin.table.status') }}</th>
                <th data-sort>{{ trans('admin::admin.table.created') }}</th>
            </tr>
        @endslot
    @endcomponent
@endcomponent

@push('scripts')
    <script type="module">
        DataTable.set('#seo_filters-table .table', {
            routePrefix: 'seo-filters',
            routes: {
                table: 'table',
                create: 'create',
                edit: 'edit',
                destroy: 'destroy',
            }
        });

    </script>

    <script type="module">
        new DataTable('#seo_filters-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'path', name: 'path' },
                { data: 'category', orderable: false, searchable: false },
                { data: 'h1', name: 'translations.h1', orderable: false, defaultContent: '' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
