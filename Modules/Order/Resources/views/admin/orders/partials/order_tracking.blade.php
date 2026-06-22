@php
    use Modules\Order\Entities\OrderStatus;
@endphp
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="fw-medium text-dark d-flex align-items-center gap-2">
                    {{ trans('order::orders.order_id') }}: #{{ $order->id }}
                    @php
                        $status = $order->orderStatus;
                        $statusName = optional($status?->translation)->name;
                        $statusColor = $status->color ?? '#6c757d';
                    @endphp

                    <span class="badge bg-success-subtle text-success px-2 py-1 fs-13" style="background-color: {{ e($statusColor) }}!important; color: #fff!important;">{{ $statusName ?: '—' }}</span>
                </h4>
                <p class="mb-0">Order / Order Details / #{{ $order->id }} - {{ $order->created_at->toFormattedDateString() }}</p>
            </div>

            <div class="d-flex gap-2">
                <!-- Кнопки Печати и Email из старого дизайна -->
                <form method="POST" action="{{ route('admin.orders.email.store', $order) }}" class="d-inline-block">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-outline-secondary" data-toggle="tooltip" title="{{ trans('order::orders.send_email') }}" data-loading>
                        <i class="fa fa-envelope-o" aria-hidden="true"></i> {{ trans('order::orders.send_email') }}
                    </button>
                </form>
                <a href="{{ route('admin.orders.print.show', $order) }}" class="btn btn-outline-secondary" target="_blank" data-toggle="tooltip" title="{{ trans('order::orders.print') }}">
                    <i class="fa fa-print" aria-hidden="true"></i> {{ trans('order::orders.print') }}
                </a>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="fw-medium text-dark">Трекинг и Статус</h4>
        </div>

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row mt-3 align-items-end">
{{--                <div class="col-md-5">--}}
{{--                    <div class="form-group mb-0">--}}
{{--                        <label for="order-status" class="form-label">{{ trans('order::orders.order_status') }}</label>--}}
{{--                        <select id="order-status" name="status" class="form-select form-control custom-select-black" data-id="{{ $order->id }}">--}}
{{--                            @foreach (trans('order::statuses') as $name => $label)--}}
{{--                                <option value="{{ $name }}" {{ $order->status === $name ? 'selected' : '' }}>--}}
{{--                                    {{ $label }}--}}
{{--                                </option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <select name="status_id" class="form-control custom-select-black" id="order-status">
                    @foreach (OrderStatus::list() as $id => $name)
                        <option value="{{ $id }}" {{ $order->status_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>

                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <label for="tracking_reference" class="form-label">{{ trans('order::orders.tracking_reference') }}</label>
                        <input type="text" name="tracking_reference" id="tracking_reference" class="form-control @error('tracking_reference') is-invalid @enderror" value="{{ old('tracking_reference', $order->tracking_reference) }}" placeholder="{{ trans('order::orders.tracking_reference_placeholder') }}">
                        @error('tracking_reference')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ trans('admin::admin.buttons.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
