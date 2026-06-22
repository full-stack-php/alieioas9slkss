<?php

namespace Modules\Product\Entities\Concerns;

use Modules\Support\Money;
use Modules\Media\Entities\File;
use Illuminate\Database\Eloquent\Collection;

trait ModelAccessors
{

    public function getPriceAttribute($price): Money
    {
        return Money::inDefaultCurrency($price);
    }


    public function getFormattedPriceAttribute(): string
    {
        return product_price_formatted($this);
    }


    public function getSpecialPriceAttribute($specialPrice)
    {
        if (!is_null($specialPrice)) {
            return Money::inDefaultCurrency($specialPrice);
        }
    }


    public function getHasPercentageSpecialPriceAttribute(): bool
    {
        return $this->hasPercentageSpecialPrice();
    }



    public function getSpecialPricePercentAttribute()
    {
        if (! $this->hasSpecialPrice()) {
            return 0;
        }
        $originalPrice = $this->price->amount();
        $specialPrice = $this->special_price->amount();
        if ($originalPrice <= 0) {
            return 0;
        }

        $discount = (($originalPrice - $specialPrice) / $originalPrice) * 100;

        return round($discount);
    }


    public function getSellingPriceAttribute($sellingPrice): Money
    {
        return Money::inDefaultCurrency($sellingPrice);
    }


    public function getTotalAttribute($total): Money
    {
        return Money::inDefaultCurrency($total);
    }


    /**
     * Get the product's base image.
     *
     * @return File
     */
    public function getBaseImageAttribute(): File
    {
        return $this->files
            ->where('pivot.zone', 'base_image')
            ->first()
            ?:
            new File();
    }


    /**
     * Get product's additional images.
     *
     * @return Collection
     */
    public function getAdditionalImagesAttribute(): Collection
    {
        return $this->files
            ->where('pivot.zone', 'additional_images')
            ->sortBy('pivot.id');
    }


    public function getMediaAttribute()
    {
        return $this->files
            ->whereIn('pivot.zone', ['base_image', 'additional_images'])
            ->sortBy('pivot.id');
    }


    /**
     * Get product's downloadable files.
     *
     * @return Collection
     */
    public function getDownloadsAttribute()
    {
        return $this->files
            ->where('pivot.zone', 'downloads')
            ->sortBy('pivot.id')
            ->flatten();
    }


    public function getDoesManageStockAttribute(): bool
    {
        return (bool)$this->manage_stock;
    }


    public function getQtyAttribute($qty)
    {
        return $qty;
    }


    public function getIsInStockAttribute(): bool
    {
        return (bool)$this->isInStock();
    }


    public function getIsOutOfStockAttribute(): bool
    {
        return $this->isOutOfStock();
    }


    public function getIsNewAttribute(): bool
    {
        return $this->isNew();
    }


    public function getAttributeSetsAttribute()
    {
        return $this->getAttribute('attributes')->groupBy('attributeSet');
    }


    public function getRatingPercentAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            return ($this->reviews->avg->rating / 5) * 100;
        }
    }
}
