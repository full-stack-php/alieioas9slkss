@php
    $filterPrefix = $name . '_filter';
    $tableSelector = '#' . $name . ' .table';
@endphp

<div class="col-md-12" id="{{ $name }}">
    <div class="card mb-3">
        <div class="card-body border-bottom">
            <div class="row">
                <div class="col-md-4">
                    {{ Form::select(
                        $filterPrefix . '_category_id',
                        trans('product::attributes.categories'),
                        $errors,
                        $categories,
                        null,
                        [
                            'class' => 'js-filter',
                            'id' => $filterPrefix . '_category_id',
                        ]
                    ) }}
                </div>

                <div class="col-md-4">
                    {{ Form::select(
                        $filterPrefix . '_brand_id',
                        trans('product::attributes.brand_id'),
                        $errors,
                        $brands,
                        null,
                        [
                            'class' => 'js-filter',
                            'id' => $filterPrefix . '_brand_id',
                        ]
                    ) }}
                </div>

                <div class="col-md-4">
                    {{ Form::select(
                        $filterPrefix . '_is_active',
                        trans('product::attributes.is_active'),
                        $errors,
                        $statuses,
                        null,
                        [
                            'class' => 'js-filter',
                            'id' => $filterPrefix . '_is_active',
                        ]
                    ) }}
                </div>
            </div>
        </div>
    </div>

    @component('admin::components.table')
        @slot('thead')
            @include('product::admin.products.partials.thead')
        @endslot
    @endcomponent
</div>

@push('scripts')
    <script type="module">
        const tableSelector_{{ $name }} = '#{{ $name }} .table';
        const filterPrefix_{{ $name }} = '{{ $filterPrefix }}';

        function getProductTabFilterValue_{{ $name }}(field) {
            const value = $('#' + filterPrefix_{{ $name }} + '_' + field).val();

            if (value === undefined || value === null || value === '') {
                return null;
            }

            return value;
        }

        function addProductTabFilters_{{ $name }}(data) {
            const categoryId = getProductTabFilterValue_{{ $name }}('category_id');
            const brandId = getProductTabFilterValue_{{ $name }}('brand_id');
            const isActive = getProductTabFilterValue_{{ $name }}('is_active');

            if (categoryId !== null) {
                data.page_filter_category_id = categoryId;
            }

            if (brandId !== null) {
                data.page_filter_brand_id = brandId;
            }

            if (isActive !== null) {
                data.page_filter_is_active = isActive;
            }

            data.except = {!! $product->id ?? "''" !!};

            const selectedIds = DataTable.getSelectedIds(tableSelector_{{ $name }});

            if (selectedIds.length) {
                data.selected_ids = selectedIds;
            }
        }

        @if ($name === 'related_products')
        DataTable.setSelectedIds(
            '#related_products .table',
            {!! old_json('related_products', $product->relatedProductList(), JSON_NUMERIC_CHECK) !!}
        );
        @elseif ($name === 'colors')
        DataTable.setSelectedIds(
            '#colors .table',
            {!! old_json('colors', $product->colorProductsList(), JSON_NUMERIC_CHECK) !!}
        );
        @elseif ($name === 'cross_sells')
        DataTable.setSelectedIds(
            '#cross_sells .table',
            {!! old_json('cross_sells', $product->crossSellProductList(), JSON_NUMERIC_CHECK) !!}
        );
        @endif

        DataTable.set(tableSelector_{{ $name }}, {
            routePrefix: 'products',
            routes: {
                table: 'table',
            }
        });

        new DataTable(tableSelector_{{ $name }}, {
            pageLength: 10,

            ajax: {
                data: function (data) {
                    addProductTabFilters_{{ $name }}(data);
                }
            },

            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'thumbnail', orderable: false, searchable: false, width: '10%' },
                { data: 'name', name: 'translations.name', orderable: false, defaultContent: '' },
                { data: 'price', searchable: false },
                { data: 'in_stock', name: 'in_stock', searchable: false },
                { data: 'status', name: 'is_active', searchable: false },
                { data: 'updated', name: 'updated_at' },
            ],
        });

        $('#{{ $name }} .js-filter').on('change', function () {
            $(tableSelector_{{ $name }}).DataTable().ajax.reload(null, false);
        });
    </script>
@endpush
