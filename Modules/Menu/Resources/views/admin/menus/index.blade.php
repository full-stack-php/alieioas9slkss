@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('menu::menus.menus'))

    <li class="breadcrumb-item active">{{ trans('menu::menus.menu') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'menus')
    @slot('name', trans('menu::menus.menu'))

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('menu::menus.table.name') }}</th>
            <th>{{ trans('admin::admin.table.status') }}</th>
            <th data-sort>{{ trans('admin::admin.table.created') }}</th>
        </tr>
    @endslot
@endcomponent

@push('scripts')
    <script type="module">

        new DataTable('#menus-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'name', name: 'translations.name', orderable: false, defaultContent: '' },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'created', name: 'created_at' },
            ]
        });
    </script>
@endpush
