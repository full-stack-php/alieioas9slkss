<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ trans('order::orders.items_ordered') }}</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @php
                $parentProducts = $order->products->whereNull('parent_id');
                $childrenByParent = $order->products->whereNotNull('parent_id')->groupBy('parent_id');
            @endphp

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
                @foreach ($parentProducts as $product)
                    @include('order::admin.orders.partials.order_product_row', [
                        'product' => $product,
                        'isChild' => false,
                    ])

                    @foreach($childrenByParent->get($product->id, collect()) as $childProduct)
                        @include('order::admin.orders.partials.order_product_row', [
                            'product' => $childProduct,
                            'isChild' => true,
                        ])
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
