<?php

namespace Modules\EmailTemplate\Services;

use Illuminate\Support\Facades\App;
use Modules\Order\Entities\Order;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateRenderer
{
    private const CUSTOMER_MAIL_LOCALE = 'uk';

    public function render(EmailTemplate $template, array $data = []): array
    {
        $locale = $this->localeFromData($data, $template);

        return $this->withLocale($locale, function () use ($template, $data, $locale) {
            $translation = $template->translate($locale);

            if (!$translation && !$this->isCustomerMail($template)) {
                $translation = $template->translate($this->fallbackLocale());
            }

            $subject = optional($translation)->subject ?: '';
            $content = optional($translation)->content ?: '';

            return [
                'subject' => $this->replaceShortcodes($subject, $template, $data),
                'html' => $this->replaceShortcodes($content, $template, $data),
            ];
        });
    }

    public function replaceShortcodes(string $content, EmailTemplate $template, array $data = []): string
    {
        $shortcodes = $this->shortcodes($data, $template);

        return str_replace(
            array_keys($shortcodes),
            array_values($shortcodes),
            $content
        );
    }

    private function localeFromData(array $data, EmailTemplate $template): string
    {
        if ($this->isCustomerMail($template)) {
            return self::CUSTOMER_MAIL_LOCALE;
        }

        if (isset($data['order']) && !empty($data['order']->locale)) {
            return $data['order']->locale;
        }

        if (isset($data['user']) && !empty($data['user']->locale)) {
            return $data['user']->locale;
        }

        return $this->currentLocale();
    }

    private function isCustomerMail(EmailTemplate $template): bool
    {
        return $template->recipient === EmailTemplateType::RECIPIENT_CUSTOMER;
    }

    private function withLocale(string $locale, callable $callback): mixed
    {
        $previousLocale = App::getLocale();

        App::setLocale($locale);

        try {
            return $callback();
        } finally {
            App::setLocale($previousLocale);
        }
    }

    private function shortcodes(array $data, EmailTemplate $template): array
    {
        $order = $data['order'] ?? null;
        $user = $data['user'] ?? null;

        return [
            '{$firstname}' => e($this->firstName($order, $user, $data)),
            '{$lastname}' => e($this->lastName($order, $user, $data)),
            '{$fullname}' => e($this->fullName($order, $user, $data)),
            '{$email}' => e($this->email($order, $user, $data)),
            '{$phone}' => e($this->phone($order, $user, $data)),

            '{$order_id}' => $order ? e($order->id) : '',
            '{$order_status}' => $order ? e($order->status()) : '',
            '{$order_total}' => $order ? e($order->total->convert($order->currency, $order->currency_rate)->format($order->currency)) : '',
            '{$order_subtotal}' => $order ? e($order->sub_total->convert($order->currency, $order->currency_rate)->format($order->currency)) : '',
            '{$order_discount}' => $order ? e($order->discount->convert($order->currency, $order->currency_rate)->format($order->currency)) : '',
            '{$shipping_method}' => $order ? e($this->shippingMethodLabel($order)) : '',
            '{$payment_method}' => $order ? e($this->paymentMethodLabel($order)) : '',
            '{$billing_address}' => $order ? nl2br(e($this->billingAddress($order))) : '',
            '{$shipping_address}' => $order ? nl2br(e($this->shippingAddress($order))) : '',
            '{$cart_weight}' => $order ? e($this->cartWeight($order)) : '',
            '{$order_products}' => $order ? $this->orderProducts($order, $template) : '',

            '{$order_date}' => $order && $order->created_at ? e($order->created_at->format('Y/m/d')) : '',
            '{$shipping_cost}' => $order ? e($order->shipping_cost->convert($order->currency, $order->currency_rate)->format($order->currency)) : '',
            '{$store_phone}' => e($this->settingText('store_phone')),
            '{$store_address}' => e($this->storeAddress()),

            '{$reset_url}' => e((string) ($data['reset_url'] ?? '')),
            '{$activation_url}' => e((string) ($data['activation_url'] ?? '')),
            '{$review_url}' => e((string) ($data['review_url'] ?? '')),
            '{$gift_certificate_code}' => e((string) ($data['gift_certificate_code'] ?? '')),
            '{$transaction_id}' => e((string) ($data['transaction_id'] ?? '')),

            '{$return_id}' => e((string) ($data['return_id'] ?? '')),
            '{$return_status}' => e((string) ($data['return_status'] ?? '')),
            '{$return_reason}' => e((string) ($data['return_reason'] ?? '')),
            '{$return_comment}' => e((string) ($data['return_comment'] ?? '')),

            '{$gift_certificate_amount}' => e((string) ($data['gift_certificate_amount'] ?? '')),
            '{$gift_certificate_from}' => e((string) ($data['gift_certificate_from'] ?? '')),
            '{$gift_certificate_message}' => e((string) ($data['gift_certificate_message'] ?? '')),

            '{$transaction_amount}' => e((string) ($data['transaction_amount'] ?? '')),
            '{$message}' => e((string) ($data['message'] ?? '')),

            '{$question}' => e((string) ($data['question'] ?? '')),
            '{$answer}' => e((string) ($data['answer'] ?? '')),
            '{$product_name}' => e($this->productName($data)),
            '{$product_url}' => e($this->productUrl($data)),

            '{$review_rating}' => e((string) ($data['review_rating'] ?? '')),
            '{$review_plus}' => e((string) ($data['review_plus'] ?? '')),
            '{$review_minus}' => e((string) ($data['review_minus'] ?? '')),
            '{$review_comment}' => e((string) ($data['review_comment'] ?? '')),

            '{$store_name}' => e($this->settingText('store_name')),
            '{$store_email}' => e($this->settingText('store_email')),
            '{$site_url}' => e(url('/')),
        ];
    }

    private function storeAddress(): string
    {
        return collect([
            'store_address_1',
            'store_address_2',
            'store_city',
            'store_zip',
        ])
            ->map(fn ($key) => $this->settingText($key))
            ->filter()
            ->implode(', ');
    }

    private function settingText(string $key): string
    {
        return $this->normalizeText(setting($key));
    }

    private function normalizeText(mixed $value): string
    {
        if (is_null($value)) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_array($value)) {
            foreach (array_merge($this->localeCandidates(), ['value', 'name']) as $key) {
                if (array_key_exists($key, $value)) {
                    return $this->normalizeText($value[$key]);
                }
            }

            foreach ($value as $item) {
                $text = $this->normalizeText($item);

                if ($text !== '') {
                    return $text;
                }
            }

            return '';
        }

        return '';
    }

    private function firstName($order, $user, array $data): string
    {
        return (string) (
            $data['firstname']
            ?? $data['first_name']
            ?? $order?->customer_first_name
            ?? $user?->first_name
            ?? ''
        );
    }

    private function lastName($order, $user, array $data): string
    {
        return (string) (
            $data['lastname']
            ?? $data['last_name']
            ?? $order?->customer_last_name
            ?? $user?->last_name
            ?? ''
        );
    }

    private function fullName($order, $user, array $data): string
    {
        return trim(
            (string) (
                $data['fullname']
                ?? $data['full_name']
                ?? $this->firstName($order, $user, $data) . ' ' . $this->lastName($order, $user, $data)
            )
        );
    }

    private function email($order, $user, array $data): string
    {
        return (string) (
            $data['email']
            ?? $order?->customer_email
            ?? $user?->email
            ?? ''
        );
    }

    private function phone($order, $user, array $data): string
    {
        return (string) (
            $data['phone']
            ?? $order?->customer_phone
            ?? $user?->phone
            ?? ''
        );
    }

    private function billingAddress(Order $order): string
    {
        if ($order->is_quick_order) {
            return $this->quickOrderToBeConfirmed($order);
        }

        return collect([
            $order->billing_full_name,
            $order->billing_address_1_display,
            $order->translateStoredValue($order->billing_address_2),
            $this->addressCityLine($order, 'billing'),
            $order->billing_country_name,
        ])->filter()->implode("\n");
    }

    private function shippingAddress(Order $order): string
    {
        if ($order->is_quick_order) {
            return $this->quickOrderToBeConfirmed($order);
        }

        return collect([
            $order->shipping_full_name,
            $order->shipping_address_1_display,
            $order->translateStoredValue($order->shipping_address_2),
            $this->addressCityLine($order, 'shipping'),
            $order->shipping_country_name,
        ])->filter()->implode("\n");
    }

    private function addressCityLine(Order $order, string $type): string
    {
        if ($type === 'billing') {
            $city = $order->billing_city_display;
            $state = $order->billing_state_display;
            $zip = $order->billing_zip;
        } else {
            $city = $order->shipping_city_display;
            $state = $order->shipping_state_display;
            $zip = $order->shipping_zip;
        }

        return trim(trim("{$city}, {$state} {$zip}"), ',');
    }

    private function cartWeight(Order $order): string
    {
        $weight = $order->products->sum(function ($orderProduct) {
            return (float) optional($orderProduct->product)->weight * (int) $orderProduct->qty;
        });

        return $weight > 0 ? rtrim(rtrim(number_format($weight, 2, '.', ''), '0'), '.') : '0';
    }

    private function orderProducts(Order $order, EmailTemplate $template): string
    {
        $order->loadMissing([
            'products.product.files',
            'products.options.option.translations',
            'products.options.values.translations',
            'products.packaging.translations',
        ]);

        $rows = '';

        foreach ($order->products as $orderProduct) {
            $rows .= $this->orderProductRow($orderProduct, $order, $template);
        }

        return <<<HTML
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-spacing:0;width:100%;border-collapse:collapse">
                <tbody>
                    {$rows}
                </tbody>
            </table>
            HTML;
    }

    private function orderProductRow($orderProduct, Order $order, EmailTemplate $template): string
    {
        $imageColumn = $this->orderProductImageColumn($orderProduct, $template);
        $meta = $this->orderProductMetaHtml($orderProduct, $order);
        $url = e($this->orderProductUrl($orderProduct));
        $name = e((string) $orderProduct->name);
        $sku = e((string) $orderProduct->sku);
        $qty = e((string) $orderProduct->qty);
        $total = e($orderProduct->line_total->convert($order->currency, $order->currency_rate)->format($order->currency));

        $skuLabel = e(trans('emailtemplate::email_templates.mail.sku'));
        $qtyLabel = e(trans('emailtemplate::email_templates.mail.qty'));
        $priceLabel = e(trans('emailtemplate::email_templates.mail.price'));

        return <<<HTML
            <tr>
                <td align="left" style="padding:20px 0;border-bottom:1px solid #ebe3ff">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-spacing:0;width:100%">
                        <tbody>
                            <tr>
                                {$imageColumn}

                                <td valign="top" style="padding:0;width:auto">
                                    <h3 style="Margin:0 0 12px 0;font-family:Poppins,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:24px;color:#022b3a">
                                        <a href="{$url}" target="_blank" style="color:#022b3a;text-decoration:none">{$name}</a>
                                    </h3>

                                    <p style="Margin:0 0 6px 0;font-family:Poppins,Arial,sans-serif;font-size:14px;line-height:21px;color:#022b3a">
                                        <strong>{$skuLabel}:</strong> {$sku}
                                    </p>

                                    {$meta}

                                    <p style="Margin:8px 0 0 0;font-family:Poppins,Arial,sans-serif;font-size:14px;line-height:21px;color:#022b3a">
                                        <strong>{$qtyLabel}:</strong> {$qty}
                                    </p>

                                    <p style="Margin:8px 0 0 0;font-family:Poppins,Arial,sans-serif;font-size:14px;line-height:21px;color:#022b3a">
                                        <strong>{$priceLabel}:</strong> {$total}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            HTML;
    }

    private function orderProductImageColumn($orderProduct, EmailTemplate $template): string
    {
        if (!$template->show_product_image) {
            return '';
        }

        $image = optional(optional($orderProduct->product)->base_image)->path;

        if (!$image) {
            return '';
        }

        $url = e($this->orderProductUrl($orderProduct));
        $image = e($image);
        $alt = e((string) $orderProduct->name);
        $width = (int) $template->product_image_max_width;
        $height = (int) $template->product_image_max_height;

        return <<<HTML
            <td valign="top" style="padding:0 20px 0 0;width:{$width}px">
                <a href="{$url}" target="_blank" style="text-decoration:none">
                    <img src="{$image}" alt="{$alt}" width="{$width}" style="display:block;border:0;outline:none;text-decoration:none;max-width:{$width}px;max-height:{$height}px;height:auto">
                </a>
            </td>
            HTML;
    }

    private function orderProductMetaHtml($orderProduct, Order $order): string
    {
        $lines = [];

        if ($orderProduct->isGift()) {
            $lines[] = trans('emailtemplate::email_templates.mail.gift');
        }

        $packaging = $this->orderProductPackagingText($orderProduct);

        if ($packaging !== '') {
            $lines[] = $packaging;
        }

        foreach ($this->orderProductOptionTexts($orderProduct, $order) as $optionText) {
            $lines[] = $optionText;
        }

        if (empty($lines)) {
            return '';
        }

        $html = '';

        foreach ($lines as $line) {
            $line = e($line);

            $html .= <<<HTML
        <p style="Margin:0 0 6px 0;font-family:Poppins,Arial,sans-serif;font-size:14px;line-height:21px;color:#022b3a">
            {$line}
        </p>
        HTML;
        }

        return $html;
    }

    private function orderProductPackagingText($orderProduct): string
    {
        if (!$orderProduct->hasPackaging()) {
            return '';
        }

        $packaging = $orderProduct->packaging;

        $name = trim((string) $packaging->name);
        $qty = (int) $packaging->qty;

        if ($name === '' && $qty <= 0) {
            return '';
        }

        $packagingLabel = trans('emailtemplate::email_templates.mail.packaging');
        $piecesLabel = trans('emailtemplate::email_templates.mail.pieces');

        if ($name !== '' && $qty > 0) {
            return "{$packagingLabel}: {$name} ({$qty} {$piecesLabel})";
        }

        if ($name !== '') {
            return "{$packagingLabel}: {$name}";
        }

        return "{$packagingLabel}: {$qty} {$piecesLabel}";
    }

    private function orderProductOptionTexts($orderProduct, Order $order): array
    {
        $texts = [];

        foreach ($orderProduct->options as $option) {
            $optionName = trim((string) $option->name);

            if ($optionName === '') {
                continue;
            }

            if ($option->isFieldType() && !empty($option->value)) {
                $texts[] = "{$optionName}: {$option->value}";

                continue;
            }

            $values = [];

            foreach ($option->values as $value) {
                $label = trim((string) $value->label);

                if ($label === '') {
                    continue;
                }

                $values[] = $label;
            }

            if (!empty($values)) {
                $texts[] = "{$optionName}: " . implode(', ', $values);
            }
        }

        return $texts;
    }


    private function orderProductUrl($orderProduct): string
    {
        if (!$orderProduct->product) {
            return url('/');
        }

        return $orderProduct->url();
    }

    private function localeCandidates(): array
    {
        return array_values(array_unique(array_filter([
            $this->currentLocale(),
            $this->fallbackLocale(),
            config('app.fallback_locale'),
        ])));
    }

    private function currentLocale(): string
    {
        return (string) App::getLocale();
    }

    private function fallbackLocale(): string
    {
        if (function_exists('default_locale')) {
            return (string) \default_locale();
        }

        return (string) config('app.fallback_locale', 'en');
    }

    private function productName(array $data): string
    {
        if (isset($data['product']) && $data['product']) {
            return (string) $data['product']->name;
        }

        return (string) ($data['product_name'] ?? '');
    }

    private function productUrl(array $data): string
    {
        if (isset($data['product']) && $data['product']) {
            return route('products.show', ['slug' => $data['product']->slug]);
        }

        return (string) ($data['product_url'] ?? '');
    }

    private function shippingMethodLabel(Order $order): string
    {
        if ($order->is_quick_order) {
            return $this->quickOrderToBeConfirmed($order);
        }

        return (string) $order->shipping_method;
    }

    private function paymentMethodLabel(Order $order): string
    {
        if ($order->is_quick_order) {
            return $this->quickOrderToBeConfirmed($order);
        }

        return (string) $order->payment_method;
    }

    private function quickOrderToBeConfirmed(Order $order): string
    {
        if (!$order->is_quick_order) {
            return '';
        }

        return trans('quickorder::quick_order.to_be_confirmed');
    }
}
