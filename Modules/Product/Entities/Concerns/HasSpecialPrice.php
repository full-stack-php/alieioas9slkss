<?php

namespace Modules\Product\Entities\Concerns;

use Modules\Support\Money;

trait HasSpecialPrice
{
    public function getSpecialPrice(): Money
    {
        $specialPrice = $this->attributes['special_price'];

        if ($this->special_price_type === 'percent') {
            $discountedPrice = ($specialPrice / 100) * $this->attributes['price'];

            $specialPrice = $this->attributes['price'] - $discountedPrice;
        }

        if ($specialPrice < 0) {
            $specialPrice = 0;
        }

        return Money::inDefaultCurrency($specialPrice);
    }


    public function hasSpecialPrice(): bool
    {
        if (is_null($this->special_price)) {
            return false;
        }

        if ($this->hasSpecialPriceStartDate() && $this->hasSpecialPriceEndDate()) {
            return $this->specialPriceStartDateIsValid() && $this->specialPriceEndDateIsValid();
        }

        if ($this->hasSpecialPriceStartDate()) {
            return $this->specialPriceStartDateIsValid();
        }

        if ($this->hasSpecialPriceEndDate()) {
            return $this->specialPriceEndDateIsValid();
        }

        return true;
    }

    public function hasGift(): bool
    {
        if ($this->relationLoaded('gifts')) {
            return $this->gifts->isNotEmpty();
        }

        return $this->gifts()->exists();
    }
    public function hasBundle(): bool
    {
        if ($this->relationLoaded('bundles')) {
            return $this->bundles->isNotEmpty();
        }

        return $this->bundles()->exists();
    }
    public function hasPackaging(): bool
    {
        if ($this->relationLoaded('packagings')) {
            return $this->packagings->isNotEmpty();
        }

        return $this->packagings()->exists();
    }


    public function hasPercentageSpecialPrice(): bool
    {
        return $this->hasSpecialPrice() && $this->special_price_type === 'percent';
    }


    private function hasSpecialPriceStartDate(): bool
    {
        return !is_null($this->special_price_start);
    }


    private function hasSpecialPriceEndDate(): bool
    {
        return !is_null($this->special_price_end);
    }


    private function specialPriceStartDateIsValid(): bool
    {
        return today() >= $this->special_price_start;
    }


    private function specialPriceEndDateIsValid(): bool
    {
        return today() <= $this->special_price_end;
    }

}
