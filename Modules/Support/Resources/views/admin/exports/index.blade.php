@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('support::export.exports'))

    <li class="breadcrumb-item active">{{ trans('support::export.exports') }}</li>
@endcomponent

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@component('admin::components.page.index_table')
    @slot('resource', 'exports')
    @slot('buttons', ['create'])
    @slot('name', trans('support::export.exports'))

    @slot('thead')
        <tr>
            @include('admin::partials.table.select_all')

            <th>{{ trans('admin::admin.table.id') }}</th>
            <th>{{ trans('support::export.table.name') }}</th>
            <th>{{ trans('support::export.table.format') }}</th>
            <th>{{ trans('admin::admin.table.status') }}</th>
            <th data-sort>{{ trans('admin::admin.table.created') }}</th>
            <th data-sort>{{ trans('admin::admin.table.action') }}</th>
        </tr>
    @endslot
@endcomponent

@push('scripts')
    <script type="module">
        new DataTable('#exports-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'name', name: 'name', orderable: false, defaultContent: '' },
                { data: 'format', name: 'format', orderable: false, searchable: false },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'created', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' },
            ],
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-run-export');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const url = btn.getAttribute('data-url');
            const originalHtml = btn.innerHTML;

            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Запуск...';
            btn.disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Экспорт успешно запущен!');
                    } else {
                        alert('Ошибка: ' + (data.message || 'Что-то пошло не так'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка AJAX:', error);
                    alert('Произошла ошибка сервера. Проверьте консоль.');
                })
                .finally(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
        });

    </script>
@endpush
