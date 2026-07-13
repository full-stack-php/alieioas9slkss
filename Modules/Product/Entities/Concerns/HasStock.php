<?php

namespace Modules\Product\Entities\Concerns;

use Modules\Product\Entities\Product;

trait HasStock
{
    public function isOutOfStock(): bool
    {
        return !$this->isInStock();
    }

    public function isInStock(): bool
    {
        if ($this->isPreorder() || $this->isDiscontinued()) {
            return false;
        }

        if ($this->manage_stock && (int) $this->qty <= 0) {
            return false;
        }

        return (bool) $this->in_stock;
    }

    public function isPurchasable(): bool
    {
        return !$this->isPreorder()
            && !$this->isDiscontinued()
            && $this->isInStock();
    }

    public function isPreorder(): bool
    {
        return (int) $this->stock_status === Product::STOCK_STATUS_PREORDER;
    }

    public function isDiscontinued(): bool
    {
        return (int) $this->stock_status === Product::STOCK_STATUS_DISCONTINUED;
    }

    public function tracksStock(): bool
    {
        return (int) $this->stock_status === Product::STOCK_STATUS_TRACKED;
    }

    public function doesNotTrackStock(): bool
    {
        return (int) $this->stock_status === Product::STOCK_STATUS_NOT_TRACKED;
    }

    public function markAsInStock(): void
    {
        $this->withoutEvents(function () {
            $this->update([
                'in_stock' => true,
            ]);
        });
    }

    public function markAsOutOfStock(): void
    {
        $this->withoutEvents(function () {
            $this->update([
                'in_stock' => false,
            ]);
        });
    }
}
