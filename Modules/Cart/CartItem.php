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
            'has_discounted_price' => $this->hasDiscountedPrice(),
            'regularUnitPrice' => $this->regularUnitPrice(),
            'regularTotal' => $this->regularTotalPrice(),
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

    public function regularUnitPrice()
    {
        return Money::inDefaultCurrency($this->regularUnitPriceAmount());
    }

    public function regularTotalPrice()
    {
        return $this->regularUnitPrice()->multiply($this->qty);
    }

    public function hasDiscountedPrice(): bool
    {
        if ($this->isGift()) {
            return false;
        }

        return $this->regularUnitPrice()->amount() > $this->unitPrice()->amount();
    }

    private function regularUnitPriceAmount(): float
    {
        if ($this->hasPackaging()) {
            return $this->regularPackagingUnitPriceAmount();
        }

        if ($this->attribute('regular_price') !== null) {
            return $this->amount($this->attribute('regular_price'), (float) $this->price);
        }

        return $this->regularProductPriceWithOptions();
    }

    private function regularPackagingUnitPriceAmount(): float
    {
        $pricePerUnit = $this->amount($this->packaging->price_per_unit ?? null, 0);
        $qty = max(1, (int) ($this->packaging->qty ?? 1));

        return $pricePerUnit * $qty;
    }

    private function regularProductPriceWithOptions(): float
    {
        $finalPrice = $this->productRegularPriceAmount();

        foreach (collect($this->options ?? []) as $option) {
            foreach (collect($option->values ?? []) as $value) {
                $normalAmount = $this->amount($value->price ?? null, 0);

                if (($value->price_type ?? null) === 'fixed' && $normalAmount > 0) {
                    $finalPrice = $normalAmount;

                    break 2;
                }
            }
        }

        foreach (collect($this->options ?? []) as $option) {
            foreach (collect($option->values ?? []) as $value) {
                $normalPercent = $this->amount($value->price ?? null, 0);

                if (($value->price_type ?? null) === 'percent' && $normalPercent > 0) {
                    $finalPrice = ($finalPrice * $normalPercent) / 100;
                }
            }
        }

        return $finalPrice;
    }

    private function productRegularPriceAmount(): float
    {
        $price = data_get($this->product, 'price');

        if ($price === null) {
            $this->refreshStock();

            $price = data_get($this->item, 'price');
        }

        return $this->amount($price, (float) $this->price);
    }

    private function amount($value, float $default = 0): float
    {
        if (is_object($value) && method_exists($value, 'amount')) {
            return (float) $value->amount();
        }

        if (is_array($value)) {
            return (float) ($value['amount'] ?? $default);
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return $default;
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
                'stock_status',
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

    public function attribute(string $key, $default = null)
    {
        if ($this->attributes instanceof \Illuminate\Support\Collection) {
            return $this->attributes->get($key, $default);
        }

        if (is_array($this->attributes)) {
            return $this->attributes[$key] ?? $default;
        }

        return data_get($this->attributes, $key, $default);
    }

    public function parentId(): ?string
    {
        return $this->attribute('parent_id');
    }

    public function isChild(): bool
    {
        return !empty($this->parentId());
    }

    public function isParent(): bool
    {
        return !$this->isChild();
    }

    public function hasOptions(): bool
    {
        return $this->options->isNotEmpty();
    }

    public function hasPackaging(): bool
    {
        return !empty($this->packaging->id);
    }

    public function isRemovable(): bool
    {
        return true;
    }

    public function canChangeQuantity(): bool
    {
        return !$this->isGift();
    }
}
