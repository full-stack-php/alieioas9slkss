@php
    $parentProducts = $order->products->whereNull('parent_id');
    $childrenByParent = $order->products->whereNotNull('parent_id')->groupBy('parent_id');
@endphp

<div class="order-details-middle">
    <div class="table-responsive">
        <table class="table table-borderless order-details-table">
            <thead>
                <tr>
                    <th>{{ trans('storefront::account.product_name') }}</th>
                    <th>{{ trans('storefront::account.view_order.unit_price') }}</th>
                    <th>{{ trans('storefront::account.view_order.quantity') }}</th>
                    <th>{{ trans('storefront::account.view_order.line_total') }}</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($parentProducts as $product)
                @include('storefront::public.account.orders.show.order_product_row', [
                    'product' => $product,
                    'order' => $order,
                    'isChild' => false,
                ])

                @foreach($childrenByParent->get($product->id, collect()) as $childProduct)
                    @include('storefront::public.account.orders.show.order_product_row', [
                        'product' => $childProduct,
                        'order' => $order,
                        'isChild' => true,
                    ])
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
</div>
