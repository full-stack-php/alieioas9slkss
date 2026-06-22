<?php

namespace Modules\Product\Entities\Concerns;

trait HasStock
{
    public function isOutOfStock(): bool
    {
        return !$this->isInStock();
    }


    public function isInStock()
    {
        if ($this->manage_stock && $this->qty === 0) {
            return false;
        }

        return $this->in_stock;
    }


    public function markAsInStock(): void
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => true]);
        });
    }


    public function markAsOutOfStock(): void
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => false]);
        });
    }
}
