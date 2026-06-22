@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('currency::currency_rates.currency_rates'))

    <li class="breadcrumb-item active">{{ trans('currency::currency_rates.currency_rates') }}</li>
@endcomponent

@section('content')
    <div class="card">
        <div class="d-flex card-header justify-content-between align-items-center">
            <div class="row">
                <div class="btn-group pull-right">
                    <a href="{{ route("admin.languages.add") }}" id="refresh-rates" class="btn btn-primary btn-actions btn-create">
                        {{ trans('currency::currency_rates.refresh_rates') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body index-table" id="currency-rates-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        <th>{{ trans('currency::currency_rates.table.currency') }}</th>
                        <th data-sort="asc">{{ trans('currency::currency_rates.table.code') }}</th>
                        <th>{{ trans('currency::currency_rates.table.rate') }}</th>
                        <th>{{ trans('currency::currency_rates.table.last_updated') }}</th>
                    </tr>
                @endslot
            @endcomponent
        </div>
    </div>

@endsection

@push('globals')
    @vite([
        'Modules/Currency/Resources/assets/admin/js/main.js',
    ])
@endpush

@push('scripts')
    <script type="module">
        DataTable.set('#currency-rates-table .table', {
            routePrefix: 'currency-rates',
            routes: {
                table: 'table',
                edit: 'edit',
            }
        });

        new DataTable('#currency-rates-table .table', {
            columns: [
                { data: 'currency_name', orderable: false, searchable: false },
                { data: 'currency' },
                { data: 'rate', searchable: false },
                { data: 'updated_at', searchable: false },
            ],
        });
    </script>
@endpush
