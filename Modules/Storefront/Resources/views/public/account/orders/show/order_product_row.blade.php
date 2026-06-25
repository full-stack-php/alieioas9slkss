<tr class="{{ $isChild ? 'order-product-child' : '' }}">
    <td>
        <a href="{{ $product->url() }}" class="product-name">
            @if($product->is_gift)
                🎁
            @endif

            {{ $isChild ? '↳ ' : '' }}{{ $product->name }}
        </a>

        @if($product->is_gift)
            <div class="text-success small">
                Подарок
            </div>
        @endif

        @if($product->packaging)
            <ul class="list-inline product-options">
                <li>
                    <label>Упаковка:</label>
                    {{ $product->packaging->name }} ({{ $product->packaging->qty }} шт.)
                </li>
            </ul>
        @endif

        @if ($product->hasAnyOption())
            <ul class="list-inline product-options">
                @foreach ($product->options as $option)
                    <li>
                        @if ($option->isFieldType())
                            <label>{{ $option->name }}:</label> {{ $option->value }}
                        @else
                            <label>{{ $option->name }}:</label> {{ $option->values->implode('label', ', ') }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </td>

    <td>
        <label>{{ trans('storefront::account.view_order.unit_price') }}</label>

        <span class="product-price">
            {{ $product->unit_price->convert($order->currency, $order->currency_rate)->format($order->currency) }}
        </span>
    </td>

    <td>
        <label>{{ trans('storefront::account.view_order.quantity') }}</label>

        <span class="quantity">
            {{ $product->qty }}
        </span>
    </td>

    <td>
        <label>{{ trans('storefront::account.view_order.line_total') }}</label>

        <span class="product-price">
            {{ $product->line_total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
        </span>
    </td>
</tr>
