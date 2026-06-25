<?php

namespace Modules\Cart;

use stdClass;
use JsonSerializable;
use Modules\Support\Money;
use Modules\Product\Entities\Product;

class CartItem implements JsonSerializable
{
    public $id;
    public $qty;
    public $product;
    public $item;
    public $options;
    public $packaging;
    public $price;

    public $attributes;
    public $excludeFromCoupon = false;
    public $isGiftPackaging = false;

    public function __construct($item)
    {
        $this->id = $item->id;
        $this->qty = $item->quantity;
        $this->price = $item->price;

        $this->attributes = $item->attributes;

        $this->product = $item->attributes['product'];
        $this->options = $item->attributes['options'];
        $this->packaging = $item->attributes['packaging'] ?? [];
        $this->item = $item->attributes['item'] ?? $item->attributes['product'];

        $this->excludeFromCoupon = (bool) ($item->attributes['exclude_from_coupon'] ?? false);
        $this->isGiftPackaging = (bool) ($item->attributes['is_gift_packaging'] ?? false);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'qty' => (int) $this->qty,
            'product' => $this->product,
            'item' => $this->refreshStock()->item,
            'options' => $this->options->isNotEmpty() ? $this->options->keyBy('position') : new stdClass(),
            'packaging' => !empty($this->packaging) ? $this->packaging : new stdClass(),
            'is_gift_packaging' => $this->isGiftPackaging,
            'exclude_from_coupon' => $this->excludeFromCoupon,
            'unitPrice' => $this->unitPrice(),
            'total' => $this->totalPrice(),
        ];
    }

    public function unitPrice()
    {
        return Money::inDefaultCurrency($this->price);
    }

    public function totalPrice()
    {
        return $this->unitPrice()->multiply($this->qty);
    }

    public function optionsPrice()
    {
        return Money::inDefaultCurrency(0);
    }

    public function refreshStock()
    {
        $freshProduct = $this->getProduct();

        if ($freshProduct) {
            $this->item = $freshProduct;
        } else {
            $this->item = is_array($this->item) ? (object)$this->item : $this->item;
        }

        return $this;
    }

    private function getProduct()
    {
        return Product::withName()
            ->addSelect('id', 'in_stock', 'manage_stock', 'qty', 'is_active', 'price',
                'special_price',
                'special_price_type',
                'special_price_start',
                'special_price_end')
            ->where('id', $this->product->id)
            ->first();
    }

    public function isGift(): bool
    {
        return isset($this->product->is_gift) && (bool) $this->product->is_gift;
    }

    public function getMinQty(): int
    {
        return (int) ($this->attributes['min_qty'] ?? 1);
    }
}
