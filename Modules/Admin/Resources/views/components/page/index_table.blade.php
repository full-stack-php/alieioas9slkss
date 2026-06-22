@section('content')
    <div class="card card-body">
        <div class="box-body index-table" id="{{ isset($resource) ? "{$resource}-table" : '' }}">
            @if (isset($thead))
                @include('admin::components.table')
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
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
                    }
                });
            @endisset
        </script>
    @endpush
@endisset
