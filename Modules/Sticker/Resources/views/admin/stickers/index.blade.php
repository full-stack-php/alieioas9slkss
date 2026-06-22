@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('sticker::stickers.stickers'))

    <li class="breadcrumb-item active">
        {{ trans('sticker::stickers.stickers') }}
    </li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'stickers')
    @slot('name', trans('sticker::stickers.sticker'))

    @component('admin::components.table')
        @slot('thead')
            <tr>
                @include('admin::partials.table.select_all')

                <th>
                    {{ trans('admin::admin.table.id') }}
                </th>

                <th>
                    {{ trans('sticker::stickers.table.image') }}
                </th>

                <th>
                    {{ trans('sticker::stickers.table.name') }}
                </th>

                <th>
                    {{ trans('sticker::stickers.table.type') }}
                </th>

                <th>
                    {{ trans('sticker::stickers.table.sort_order') }}
                </th>

                <th>
                    {{ trans('admin::admin.table.status') }}
                </th>

                <th data-sort>
                    {{ trans('admin::admin.table.created') }}
                </th>
            </tr>
        @endslot
    @endcomponent
@endcomponent

@push('scripts')
    <script type="module">
        new DataTable('#stickers-table .table', {
            columns: [
                {
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '3%'
                },
                {
                    data: 'id',
                    width: '5%'
                },
                {
                    data: 'image',
                    orderable: false,
                    searchable: false,
                    width: '10%'
                },
                {
                    data: 'name',
                    name: 'translations.name',
                    orderable: false,
                    defaultContent: ''
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'sort_order',
                    name: 'sort_order',
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'is_active',
                    searchable: false
                },
                {
                    data: 'created',
                    name: 'created_at'
                }
            ]
        });
    </script>
@endpush
