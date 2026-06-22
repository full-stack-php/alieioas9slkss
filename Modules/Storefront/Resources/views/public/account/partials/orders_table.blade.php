<div class="table-responsive">
    <table class="table ch-table">
        <thead>
        <tr>
            <td class="text-left">{{ trans('storefront::account.orders.order_id') }}</td>
            <td class="text-left">{{ trans('storefront::account.date') }}</td>
            <td class="text-left">{{ trans('storefront::account.status') }}</td>
            <td class="text-right">{{ trans('storefront::account.orders.total') }}</td>
            <td class="text-right">{{ trans('storefront::account.action') }}</td>
        </tr>
        </thead>

        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td class="text-left">
                    <a href="{{ route('account.orders.show', $order) }}">
                        #{{ $order->id }}
                    </a>
                </td>

                <td class="text-left">
                    {{ $order->created_at->toFormattedDateString() }}
                </td>

                <td class="text-left">
                    @if ($order->orderStatus && $order->orderStatus->translatedName())
                        <span
                            class="badge"
                            style="background-color: {{ $order->orderStatus->color ?? '#777' }}; color: #fff;"
                        >
                            {{ $order->orderStatus->translatedName() }}
                        </span>
                    @else
                        —
                    @endif
                </td>

                <td class="text-right">
                    <b>
                        {{ $order->total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                    </b>
                </td>

                <td class="text-right">
                    <a
                        href="{{ route('account.orders.show', $order) }}"
                        class="btn btn-primary btn-accent chm-sm"
                    >
                       Повторить заказ
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
