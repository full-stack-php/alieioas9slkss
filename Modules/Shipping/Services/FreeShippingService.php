<?php

namespace Modules\Shipping\Services;

use Modules\Cart\Facades\Cart;
use Modules\Support\Money;

class FreeShippingService
{
    public function enabled(): bool
    {
        return (bool) setting('free_shipping_enabled');
    }

    public function minAmount(): float
    {
        return (float) setting('free_shipping_min_amount', 0);
    }

    public function subtotal(): float
    {
        return (float) Cart::subTotal()->amount();
    }

    public function isAvailable(): bool
    {
        return $this->enabled()
            && $this->minAmount() > 0
            && $this->subtotal() >= $this->minAmount();
    }

    public function amountLeft(): float
    {
        if (! $this->enabled()) {
            return 0;
        }

        return max($this->minAmount() - $this->subtotal(), 0);
    }

    public function percentage(): int
    {
        if (! $this->enabled() || $this->minAmount() <= 0) {
            return 0;
        }

        return min((int) round(($this->subtotal() / $this->minAmount()) * 100), 100);
    }

    public function formattedAmountLeft(): string
    {
        return Money::inDefaultCurrency($this->amountLeft())->format();
    }

    public function formattedMinAmount(): string
    {
        return Money::inDefaultCurrency($this->minAmount())->format();
    }

    public function shippingLabel(): string
    {
        return $this->isAvailable()
            ? trans('shipping::shipping.free')
            : trans('shipping::shipping.carrier_tariffs');
    }

    public function messageText(): string
    {
        return $this->isAvailable()
            ? trans('shipping::shipping.free_shipping_reached')
            : trans('shipping::shipping.free_shipping_left');
    }

    public function summary(): array
    {
        return [
            'enabled' => $this->enabled(),
            'available' => $this->isAvailable(),
            'min_amount' => $this->minAmount(),
            'amount_left' => $this->amountLeft(),
            'percentage' => $this->percentage(),
            'shipping_label' => $this->shippingLabel(),

            'message_text' => $this->messageText(),
            'amount_left_formatted' => $this->formattedAmountLeft(),

            'formatted_amount_left' => $this->formattedAmountLeft(),
            'formatted_min_amount' => $this->formattedMinAmount(),
        ];
    }
}
