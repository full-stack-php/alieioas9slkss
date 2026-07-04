<?php

namespace Modules\EmailTemplate\Services;

use Modules\Order\Entities\Order;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateRenderer
{
    public function render(EmailTemplate $template, array $data = []): string
    {
        $translation = $template->translate(locale()) ?: $template->translate(default_locale());

        $content = optional($translation)->content ?: '';

        return $this->replaceShortcodes($content, $template, $data);
    }

    private function compile(string $content, array $shortcodes): string
    {
        return str_replace(array_keys($shortcodes), array_values($shortcodes), $content);
    }

    private function wrap(string $header, string $body, string $footer): string
    {
        return view('emailtemplate::emails.template', [
            'header' => $header,
            'body' => $body,
            'footer' => $footer,
        ])->render();
    }

    private function localeFromData(array $data): string
    {
        if (isset($data['order']) && !empty($data['order']->locale)) {
            return $data['order']->locale;
        }

        return locale();
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
            '{$shipping_method}' => $order ? e((string) $order->shipping_method) : '',
            '{$payment_method}' => $order ? e((string) $order->payment_method) : '',
            '{$billing_address}' => $order ? nl2br(e($this->billingAddress($order))) : '',
            '{$shipping_address}' => $order ? nl2br(e($this->shippingAddress($order))) : '',
            '{$cart_weight}' => $order ? e($this->cartWeight($order)) : '',
            '{$order_products}' => $order ? $this->orderProducts($order, $template) : '',

            '{$reset_url}' => e((string) ($data['reset_url'] ?? '')),

            '{$store_name}' => e((string) setting('store_name')),
            '{$store_email}' => e((string) setting('store_email')),
            '{$site_url}' => e(url('/')),
        ];
    }

    private function firstName($order, $user, array $data): string
    {
        return (string) (
            $data['firstname']
            ?? $order?->customer_first_name
            ?? $user?->first_name
            ?? ''
        );
    }

    private function lastName($order, $user, array $data): string
    {
        return (string) (
            $data['lastname']
            ?? $order?->customer_last_name
            ?? $user?->last_name
            ?? ''
        );
    }

    private function fullName($order, $user, array $data): string
    {
        return trim($this->firstName($order, $user, $data) . ' ' . $this->lastName($order, $user, $data));
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
        return collect([
            $order->billing_full_name,
            $order->billing_address_1,
            $order->billing_address_2,
            trim("{$order->billing_city}, {$order->billing_state_name} {$order->billing_zip}"),
            $order->billing_country_name,
        ])->filter()->implode("\n");
    }

    private function shippingAddress(Order $order): string
    {
        return collect([
            $order->shipping_full_name,
            $order->shipping_address_1,
            $order->shipping_address_2,
            trim("{$order->shipping_city}, {$order->shipping_state_name} {$order->shipping_zip}"),
            $order->shipping_country_name,
        ])->filter()->implode("\n");
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
        $order->loadMissing('products.product.files');

        return view('emailtemplate::emails.partials.order_products', [
            'order' => $order,
            'template' => $template,
        ])->render();
    }
}
