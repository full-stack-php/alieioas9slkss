<div class="card">
    <div class="card-header">
        <h4 class="card-title">Order Summary</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mb-0 border-none">
                <tbody>
                <tr>
                    <td class="px-0 border-0">
                        <p class="d-flex mb-0 align-items-center gap-1"><iconify-icon icon="solar:clipboard-text-broken"></iconify-icon> {{ trans('order::orders.subtotal') }} : </p>
                    </td>
                    <td class="text-end text-dark fw-medium px-0 border-0">{{ $order->sub_total->format() }}</td>
                </tr>

                @if ($order->hasCoupon())
                    <tr>
                        <td class="px-0 border-0">
                            <p class="d-flex mb-0 align-items-center gap-1"><iconify-icon icon="solar:ticket-broken" class="align-middle"></iconify-icon> {{ trans('order::orders.coupon') }} (<span class="coupon-code">{{ $order->coupon->code }}</span>) : </p>
                        </td>
                        <td class="text-end text-danger fw-medium px-0 border-0">-{{ $order->discount->format() }}</td>
                    </tr>
                @endif

                @if ($order->hasShippingMethod())
                    <tr>
                        <td class="px-0 border-0">
                            <p class="d-flex mb-0 align-items-center gap-1"><iconify-icon icon="solar:kick-scooter-broken" class="align-middle"></iconify-icon> {{ $order->shipping_method }} : </p>
                        </td>
                        <td class="text-end text-dark fw-medium px-0 border-0">{{ $order->shipping_cost->format() }}</td>
                    </tr>
                @endif

                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between bg-light-subtle">
        <div>
            <p class="fw-medium text-dark mb-0">{{ trans('order::orders.total') }}</p>
        </div>
        <div>
            <p class="fw-medium text-primary fs-18 mb-0">{{ $order->total->format() }}</p>
        </div>
    </div>
</div>
