<div class="card">
    <div class="card-header">
        <h4 class="card-title">Order Timeline & Notes</h4>
    </div>
    <div class="card-body">
        @if ($order->note)
            <div class="mb-4 p-3 bg-light rounded">
                <h5 class="fs-14 fw-medium text-dark mb-1">{{ trans('order::orders.order_note') }}</h5>
                <p class="mb-0 text-muted">{{ $order->note }}</p>
            </div>
        @endif

        <!-- Timeline Placeholder -->
        <div class="position-relative ms-2">
            <span class="position-absolute start-0 top-0 border border-dashed h-100"></span>

            <div class="position-relative ps-4">
                <div class="mb-4">
                    <span class="position-absolute start-0 avatar-sm translate-middle-x bg-light d-inline-flex align-items-center justify-content-center rounded-circle text-success fs-20">
                        <i class='bx bx-check-circle'></i>
                    </span>
                    <div class="ms-2 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1 text-dark fw-medium fs-15">Order Placed</h5>
                            <p class="mb-2">Customer successfully placed the order.</p>
                        </div>
                        <p class="mb-0">{{ $order->created_at->format('F d, Y, h:i a') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card bg-light-subtle">
    <div class="card-body">
        <div class="row g-3 g-lg-0">
            <div class="col-lg-4 border-end">
                <div class="d-flex align-items-center gap-3 justify-content-between px-3">
                    <div>
                        <p class="text-dark fw-medium fs-16 mb-1">{{ trans('order::orders.payment_method') }}</p>
                        <p class="mb-0">{{ $order->payment_method }}</p>
                    </div>
                    <div class="avatar bg-light d-flex align-items-center justify-content-center rounded">
                        <iconify-icon icon="solar:wallet-bold-duotone" class="fs-35 text-primary"></iconify-icon>
                    </div>
                </div>
            </div>

            @if ($order->shipping_method)
                <div class="col-lg-4 border-end">
                    <div class="d-flex align-items-center gap-3 justify-content-between px-3">
                        <div>
                            <p class="text-dark fw-medium fs-16 mb-1">{{ trans('order::orders.shipping_method') }}</p>
                            <p class="mb-0">{{ $order->shipping_method }}</p>
                        </div>
                        <div class="avatar bg-light d-flex align-items-center justify-content-center rounded">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-35 text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            @endif

            @if (is_multilingual())
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 justify-content-between px-3">
                        <div>
                            <p class="text-dark fw-medium fs-16 mb-1">{{ trans('order::orders.currency') }}</p>
                            <p class="mb-0">{{ $order->currency }} (Rate: {{ $order->currency_rate }})</p>
                        </div>
                        <div class="avatar bg-light d-flex align-items-center justify-content-center rounded">
                            <iconify-icon icon="solar:dollar-bold-duotone" class="fs-35 text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
