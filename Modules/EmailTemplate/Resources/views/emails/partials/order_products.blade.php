<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
    <tr>
        @if($template->show_product_image)
            <th style="text-align: left; padding: 8px; border-bottom: 1px solid #e5e5e5;">
                {{ trans('emailtemplate::email_templates.mail.image') }}
            </th>
        @endif

        <th style="text-align: left; padding: 8px; border-bottom: 1px solid #e5e5e5;">
            {{ trans('emailtemplate::email_templates.mail.product') }}
        </th>

        <th style="text-align: left; padding: 8px; border-bottom: 1px solid #e5e5e5;">
            {{ trans('emailtemplate::email_templates.mail.sku') }}
        </th>

        <th style="text-align: left; padding: 8px; border-bottom: 1px solid #e5e5e5;">
            {{ trans('emailtemplate::email_templates.mail.stock') }}
        </th>

        <th style="text-align: right; padding: 8px; border-bottom: 1px solid #e5e5e5;">
            {{ trans('emailtemplate::email_templates.mail.qty') }}
        </th>

        <th style="text-align: right; padding: 8px; border-bottom: 1px solid #e5e5e5;">
            {{ trans('emailtemplate::email_templates.mail.total') }}
        </th>
    </tr>
    </thead>

    <tbody>
    @foreach($order->products as $orderProduct)
        <tr>
            @if($template->show_product_image)
                <td style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                    @php
                        $image = optional($orderProduct->product->base_image)->path;
                    @endphp

                    @if($image)
                        <img
                            src="{{ $image }}"
                            alt="{{ $orderProduct->name }}"
                            style="max-width: {{ $template->product_image_max_width }}px; max-height: {{ $template->product_image_max_height }}px;"
                        >
                    @endif
                </td>
            @endif

            <td style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                <a href="{{ $orderProduct->url() }}" style="color: {{ mail_theme_color() }}; text-decoration: none;">
                    {{ $orderProduct->name }}
                </a>
            </td>

            <td style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                {{ $orderProduct->sku }}
            </td>

            <td style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                {{ optional($orderProduct->product)->qty }}
            </td>

            <td style="padding: 8px; border-bottom: 1px solid #f1f1f1; text-align: right;">
                {{ $orderProduct->qty }}
            </td>

            <td style="padding: 8px; border-bottom: 1px solid #f1f1f1; text-align: right;">
                {{ $orderProduct->line_total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
