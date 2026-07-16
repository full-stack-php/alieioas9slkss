@extends('admin::layout')

@component('admin::components.page.header')
    @slot(
        'title',
        trans('preorder::preorders.preorders')
    )

    <li class="breadcrumb-item active">
        {{ trans('preorder::preorders.preorders') }}
    </li>
@endcomponent

@section('content')
    <div class="card card-body">
        <div
            class="box-body index-table"
            id="preorders-table"
        >
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include(
                            'admin::partials.table.select_all'
                        )

                        <th>
                            {{ trans('admin::admin.table.id') }}
                        </th>

                        <th>
                            {{ trans('preorder::preorders.table.product') }}
                        </th>

                        <th>
                            {{ trans('preorder::preorders.table.options') }}
                        </th>

                        <th>
                            {{ trans('preorder::preorders.table.packaging') }}
                        </th>

                        <th>
                            {{ trans('preorder::preorders.table.phone') }}
                        </th>

                        <th data-sort>
                            {{ trans('admin::admin.table.date') }}
                        </th>
                    </tr>
                @endslot
            @endcomponent
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        const tableSelector = '#preorders-table .table';

        DataTable.set(tableSelector, {
            routePrefix: 'preorders',

            routes: {
                table: 'table',
                show: 'show',
                destroy: 'destroy',
            },
        });

        new DataTable(tableSelector, {
            columns: [
                {
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '3%',
                },
                {
                    data: 'id',
                    width: '5%',
                },
                {
                    data: 'product',
                    name: 'product_id',
                    orderable: false,
                },
                {
                    data: 'options',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'packaging',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'phone',
                    name: 'phone',
                },
                {
                    data: 'created',
                    name: 'created_at',
                },
            ],
        });
    </script>
@endpush
