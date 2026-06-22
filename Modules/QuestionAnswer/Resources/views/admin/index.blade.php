@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('questionanswer::questions_answers.questions_answers'))

    <li class="breadcrumb-item active">{{ trans('questionanswer::questions_answers.questions_answers') }}</li>
@endcomponent

{
@component('admin::components.page.index_table')
    @slot('resource', 'questionanswer')
    @slot('name', trans('questionanswer::questions_answers.questions_answers'))

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('questionanswer::questions_answers.table.product') }}</th>
            <th>{{ trans('questionanswer::questions_answers.table.reviewer_name') }}</th>
            <th>{{ trans('questionanswer::questions_answers.table.approved') }}</th>
            <th data-sort>{{ trans('admin::admin.table.date') }}</th>
        </tr>
    @endslot
@endcomponent


@push('scripts')
    <script type="module">
        DataTable.set('#questionanswer-table .table', {
            routePrefix: 'questions_answers',
            routes: {
                table: 'table',
                edit: 'edit',
            }
        });

    </script>

    <script type="module">
        new DataTable('#questionanswer-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'product', name: 'product.price', orderable: false, searchable: false, defaultContent: '' },
                { data: 'asker_name' },
                { data: 'status', name: 'is_approved', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
