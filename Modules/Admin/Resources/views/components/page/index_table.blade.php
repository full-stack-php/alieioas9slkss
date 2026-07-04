@section('content')
    <div class="card card-body">
        <div class="box-body index-table" id="{{ isset($resource) ? "{$resource}-table" : '' }}">
            @if (isset($filters))
                {{ $filters }}
            @endif

            @if (isset($thead))
                @include('admin::components.table')
            @else
                {{ $slot }}
            @endif
        </div>
    </div>

    @if (isset($modal))
        {{ $modal }}
    @endif
@endsection

@isset($name)
    @push('scripts')
        <script type="module">
            @isset($resource)
            DataTable.set('#{{ $resource }}-table .table', {
                routePrefix: '{{ str_replace('_', '/', $resource) }}',
                routes: {
                    table: 'table',
                    create: 'create',
                    edit: 'edit',
                    destroy: 'destroy',
                },
                customActions: @json($customActions ?? [])
            });
            @endisset
        </script>
    @endpush
@endisset
