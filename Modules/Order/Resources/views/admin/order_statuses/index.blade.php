@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('order::statuses.statuses'))

    <li class="breadcrumb-item active">{{ trans('order::statuses.statuses') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'order_statuses')
    @slot('name', trans('order::statuses.status'))

    <div class="box box-primary">
        <div class="box-body index-table" id="order_statuses-table">
            @component('admin::components.table')

                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')
                        <th>{{ trans('admin::admin.table.id') }}</th>
                        <th>{{ trans('order::statuses.attributes.name') }}</th>
                        <th>{{ trans('admin::admin.table.status') }}</th>
                        <th data-sort>{{ trans('admin::admin.table.created') }}</th>
                    </tr>
                @endslot
            @endcomponent
        </div>
    </div>
@endcomponent

@push('scripts')
    <script type="module">
        DataTable.set('#order_statuses-table .table', {
            routePrefix: 'order-statuses',
            routes: {
                table: 'table',
                edit: 'edit',
                create: 'create',
                destroy: 'destroy',
            }
        });

        new DataTable('#order_statuses-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'name', name: 'translations.name', class: 'name', orderable: false, defaultContent: '' },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
