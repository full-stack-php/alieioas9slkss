@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('redirect::redirects.redirects'))

    <li class="breadcrumb-item active">{{ trans('redirect::redirects.redirects') }}</li>
@endcomponent

@php
    $redirectCustomActions = [];

    if (auth()->check() && auth()->user()->hasAccess('admin.redirects.import')) {
        $redirectCustomActions[] = [
            'id' => 'import-redirects-button',
            'label' => trans('redirect::redirects.import.button'),
            'class' => 'btn btn-soft-primary',
            'placement' => 'prepend',
            'container' => '.dt-search',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M11 15h2V9h3l-4-5l-4 5h3zm-4 4h10v-2H7zm12 2H5a2 2 0 0 1-2-2V5h2v14h14V5h2v14a2 2 0 0 1-2 2"/></svg>',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#importRedirectsModal',
            ],
        ];
    }

    if (auth()->check() && auth()->user()->hasAccess('admin.redirects.export')) {
        $redirectCustomActions[] = [
            'id' => 'export-redirects-button',
            'label' => trans('redirect::redirects.export.button'),
            'class' => 'btn btn-soft-info',
            'placement' => 'prepend',
            'container' => '.dt-search',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 20h14v-2H5m14-9h-4V3H9v6H5l7 7z"/></svg>',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#exportRedirectsModal',
            ],
        ];
    }
@endphp

@component('admin::components.page.index_table')
    @slot('resource', 'redirects')
    @slot('buttons', ['create'])
    @slot('customActions', $redirectCustomActions)
    @slot('name', trans('redirect::redirects.redirect'))

    @slot('filters')
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">{{ trans('redirect::redirects.filters.status') }}</label>
                <select name="status" class="form-select js-filter">
                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                    <option value="active">{{ trans('redirect::redirects.filters.active') }}</option>
                    <option value="inactive">{{ trans('redirect::redirects.filters.inactive') }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ trans('redirect::redirects.filters.redirect_type') }}</label>
                <select name="status_code" class="form-select js-filter">
                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                    <option value="301">301</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ trans('redirect::redirects.filters.page_type') }}</label>
                <select name="page_type" class="form-select js-filter">
                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                    <option value="product">{{ trans('redirect::redirects.page_types.product') }}</option>
                    <option value="category">{{ trans('redirect::redirects.page_types.category') }}</option>
                    <option value="brand">{{ trans('redirect::redirects.page_types.brand') }}</option>
                    <option value="blog">{{ trans('redirect::redirects.page_types.blog') }}</option>
                    <option value="other">{{ trans('redirect::redirects.page_types.other') }}</option>
                </select>
            </div>
        </div>
    @endslot

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('redirect::redirects.table.old_url') }}</th>
            <th>{{ trans('redirect::redirects.table.new_url') }}</th>
            <th>{{ trans('redirect::redirects.table.status_code') }}</th>
            <th>{{ trans('redirect::redirects.table.page_type') }}</th>
            <th>{{ trans('redirect::redirects.table.comment') }}</th>
            <th>{{ trans('admin::admin.table.status') }}</th>
            <th data-sort>{{ trans('admin::admin.table.created') }}</th>
        </tr>
    @endslot

    @slot('modal')
        @if(auth()->check() && auth()->user()->hasAccess('admin.redirects.import'))
            <div class="modal fade" id="importRedirectsModal" tabindex="-1" aria-labelledby="importRedirectsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.redirects.import') }}" enctype="multipart/form-data" class="modal-content">
                        {{ csrf_field() }}

                        <div class="modal-header">
                            <h5 class="modal-title" id="importRedirectsModalLabel">
                                {{ trans('redirect::redirects.import.title') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('redirect::redirects.import.close') }}"></button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-info">
                                {{ trans('redirect::redirects.import.description') }}
                            </div>

                            <div class="mb-3">
                                <label for="redirects-import-file" class="form-label">
                                    {{ trans('redirect::attributes.file') }}
                                </label>

                                <input type="file" name="file" id="redirects-import-file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            </div>

                            <div class="small text-muted">
                                {{ trans('redirect::redirects.import.format') }}
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ trans('redirect::redirects.import.cancel') }}
                            </button>

                            <button type="submit" class="btn btn-primary">
                                {{ trans('redirect::redirects.import.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if(auth()->check() && auth()->user()->hasAccess('admin.redirects.export'))
            <div class="modal fade" id="exportRedirectsModal" tabindex="-1" aria-labelledby="exportRedirectsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="GET" action="{{ route('admin.redirects.export') }}" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exportRedirectsModalLabel">
                                {{ trans('redirect::redirects.export.title') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('redirect::redirects.export.close') }}"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('redirect::redirects.export.format') }}</label>
                                <select name="format" class="form-select">
                                    <option value="xlsx">XLSX</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ trans('redirect::redirects.filters.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                                    <option value="active">{{ trans('redirect::redirects.filters.active') }}</option>
                                    <option value="inactive">{{ trans('redirect::redirects.filters.inactive') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ trans('redirect::redirects.filters.redirect_type') }}</label>
                                <select name="status_code" class="form-select">
                                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                                    <option value="301">301</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ trans('redirect::redirects.filters.page_type') }}</label>
                                <select name="page_type" class="form-select">
                                    <option value="">{{ trans('redirect::redirects.filters.all') }}</option>
                                    <option value="product">{{ trans('redirect::redirects.page_types.product') }}</option>
                                    <option value="category">{{ trans('redirect::redirects.page_types.category') }}</option>
                                    <option value="brand">{{ trans('redirect::redirects.page_types.brand') }}</option>
                                    <option value="blog">{{ trans('redirect::redirects.page_types.blog') }}</option>
                                    <option value="other">{{ trans('redirect::redirects.page_types.other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ trans('redirect::redirects.export.cancel') }}
                            </button>

                            <button type="submit" class="btn btn-primary">
                                {{ trans('redirect::redirects.export.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endslot
@endcomponent

@push('scripts')
    <script type="module">
        new DataTable('#redirects-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'old_url', name: 'old_url' },
                { data: 'new_url', name: 'new_url' },
                { data: 'status_code', name: 'status_code', searchable: false },
                { data: 'page_type', name: 'page_type' },
                { data: 'comment', name: 'comment', orderable: false },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
