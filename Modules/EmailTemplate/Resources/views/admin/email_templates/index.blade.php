@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('emailtemplate::email_templates.email_templates'))

    <li class="breadcrumb-item active">{{ trans('emailtemplate::email_templates.email_templates') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('resource', 'email_templates')
    @slot('buttons', ['create'])
    @slot('name', trans('emailtemplate::email_templates.email_template'))

    @slot('filters')
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">{{ trans('emailtemplate::attributes.type') }}</label>
                <select name="type" class="form-select js-filter">
                    <option value="">{{ trans('emailtemplate::email_templates.filters.all') }}</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ trans('emailtemplate::attributes.recipient') }}</label>
                <select name="recipient" class="form-select js-filter">
                    <option value="">{{ trans('emailtemplate::email_templates.filters.all') }}</option>
                    @foreach($recipients as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ trans('emailtemplate::email_templates.filters.status') }}</label>
                <select name="status" class="form-select js-filter">
                    <option value="">{{ trans('emailtemplate::email_templates.filters.all') }}</option>
                    <option value="active">{{ trans('emailtemplate::email_templates.filters.active') }}</option>
                    <option value="inactive">{{ trans('emailtemplate::email_templates.filters.inactive') }}</option>
                </select>
            </div>
        </div>
    @endslot

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('emailtemplate::attributes.name') }}</th>
            <th>{{ trans('emailtemplate::attributes.type') }}</th>
            <th>{{ trans('emailtemplate::attributes.recipient') }}</th>
            <th>{{ trans('emailtemplate::attributes.status_key') }}</th>
            <th>{{ trans('emailtemplate::attributes.sort_order') }}</th>
            <th>{{ trans('admin::admin.table.status') }}</th>
            <th data-sort>{{ trans('admin::admin.table.created') }}</th>
        </tr>
    @endslot
@endcomponent

@push('scripts')
    <script type="module">
        DataTable.set('#email_templates-table .table', {
            routePrefix: 'email-templates',
            routes: {
                table: 'table',
                create: 'create',
                edit: 'edit',
                destroy: 'destroy',
            }
        });
    </script>

    <script type="module">
        new DataTable('#email_templates-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'name', name: 'translations.name' },
                { data: 'type_label', name: 'type' },
                { data: 'recipient_label', name: 'recipient' },
                { data: 'status_key_label', name: 'status_key' },
                { data: 'sort_order', name: 'sort_order', searchable: false },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
