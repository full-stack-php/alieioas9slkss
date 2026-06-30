<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ trans('order::orders.items_ordered') }}</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle border-bottom">
                <tr>
                    <th>{{ trans('order::orders.product') }}</th>
                    <th>{{ trans('order::orders.unit_price') }}</th>
                    <th>{{ trans('order::orders.quantity') }}</th>
                    <th>{{ trans('order::orders.line_total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->parentProducts() as $product)
                    @include('order::admin.orders.partials.order_product_row', [
                        'product' => $product,
                    ])

                    @foreach ($order->childrenForProduct($product) as $childProduct)
                        @include('order::admin.orders.partials.order_product_row', [
                            'product' => $childProduct,
                        ])
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
