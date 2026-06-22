<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">Customer Details</h4>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="avatar-md bg-light-subtle rounded-circle d-flex align-items-center justify-content-center border border-light border-3">
                <i class="bx bx-user fs-24 text-primary"></i>
            </div>
            <div>
                <p class="mb-1 fw-medium text-dark">{{ $order->customer_full_name }}</p>
                <a href="mailto:{{ $order->customer_email }}" class="link-primary fw-medium">{{ $order->customer_email }}</a>
                <p class="mb-0 mt-1 fs-12 text-muted">
                    {{ is_null($order->customer_id) ? trans('order::orders.guest') : trans('order::orders.registered') }}
                </p>
            </div>
        </div>

        <div class="mt-3">
            <h5 class="fs-14 fw-medium text-dark mb-1">Contact Number</h5>
            <p class="mb-1 text-muted">{{ $order->customer_phone ?? 'Не указан' }}</p>
        </div>

        <div class="mt-4">
            <h5 class="fs-14 fw-medium text-dark mb-2">{{ trans('order::orders.shipping_address') }}</h5>
            <div class="text-muted">
                <p class="mb-1 text-dark fw-medium">{{ $order->shipping_full_name }}</p>
                <p class="mb-1">{{ $order->shipping_address_1 }}</p>
                @if ($order->shipping_address_2)
                    <p class="mb-1">{{ $order->shipping_address_2 }}</p>
                @endif
                <p class="mb-1">{{ $order->shipping_city }}, {!! $order->shipping_state_name !!} {{ $order->shipping_zip }}</p>
                <p class="mb-1">{{ $order->shipping_country_name }}</p>
            </div>
        </div>
    </div>
</div>
