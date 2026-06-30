@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('product::products.one_c_mappings.title'))

    <li class="breadcrumb-item active">
        {{ trans('product::products.one_c_mappings.title') }}
    </li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'product_one_c_mappings')
    @slot('name', trans('product::products.one_c_mappings.single'))

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('product::products.one_c_mappings.table.product') }}</th>
            <th>{{ trans('product::products.one_c_mappings.table.target') }}</th>
            <th>{{ trans('product::products.one_c_mappings.table.external_id') }}</th>
            <th>{{ trans('product::products.one_c_mappings.table.one_c_id') }}</th>
            <th data-sort>{{ trans('admin::admin.table.created') }}</th>
        </tr>
    @endslot
@endcomponent

@push('scripts')
    <script type="module">
        new DataTable('#product_one_c_mappings-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'product', name: 'product.translations.name', orderable: false, defaultContent: '' },
                { data: 'target', orderable: false, searchable: false, defaultContent: '' },
                { data: 'external_id', name: 'external_id' },
                { data: 'one_c_id', name: 'one_c_id' },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
