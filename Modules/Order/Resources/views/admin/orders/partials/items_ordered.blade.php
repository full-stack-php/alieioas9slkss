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
                @foreach ($order->products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                    @if($product->product && $product->product->base_image->exists)
                                        <img src="{{ $product->product->base_image->path }}" alt="{{ $product->name }}" class="avatar-md object-fit-cover">
                                    @else
                                        <i class="bx bx-image fs-24 text-muted"></i>
                                    @endif
                                </div>
                                <div>
                                    @if ($product->trashed())
                                        <span class="text-dark fw-medium fs-15">{{ $product->name }}</span>
                                    @else
                                        <a href="{{ route('admin.products.edit', $product->product->id) }}" class="text-dark fw-medium fs-15">{{ $product->name }}</a>
                                    @endif

                                    @if ($product->hasAnyOption())
                                        <p class="text-muted mb-0 mt-1 fs-13">
                                            @foreach ($product->options as $option)
                                                <span>{{ $option->name }}: </span>
                                                <span class="text-dark">
                                                        @if ($option->option->isFieldType())
                                                        {{ $option->value }}
                                                    @else
                                                        {{ $option->values->implode('label', ', ') }}
                                                    @endif
                                                    </span>
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->unit_price->format() }}</td>
                        <td>{{ $product->qty }}</td>
                        <td class="fw-medium text-dark">{{ $product->line_total->format() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
