<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Money;

class ProductBundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'bundle_product_id',
        'product_qty',
        'product_price',
        'special_price',
        'special_price_type',
        'bundle_qty',
        'bundle_price',
        'special_bundle_price',
        'special_bundle_price_type',
    ];

    public function bundleProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bundle_product_id')->withoutGlobalScope('active');
    }


    public function getProductQtnAttribute(): int
    {
        return (int) $this->product_qty;
    }
    public function getBundleQtnAttribute(): string
    {
        return $this->bundle_qty;
    }

    public function getTotalBasePriceAttribute(): float
    {
        $mainTotal = (float) $this->product_price * (int) $this->product_qty;
        $bundleTotal = (float) $this->bundle_price * (int) $this->bundle_qty;

        return $mainTotal + $bundleTotal;
    }

    public function getTotalSpecialPriceAttribute(): float
    {
        $baseMain = (float) $this->product_price;
        $specialMain = (float) $this->special_price;
        $finalMainUnit = $baseMain;

        if ($specialMain > 0) {
            $finalMainUnit = $this->special_price_type === 'percent'
                ? $baseMain - ($baseMain * ($specialMain / 100))
                : $specialMain;
        }

        $totalMain = $finalMainUnit * (int) $this->product_qty;

        $baseBundle = (float) $this->bundle_price;
        $specialBundle = (float) $this->special_bundle_price;
        $finalBundleUnit = $baseBundle;

        if ($specialBundle > 0) {
            $finalBundleUnit = $this->special_bundle_price_type === 'percent'
                ? $baseBundle - ($baseBundle * ($specialBundle / 100))
                : $specialBundle;
        }

        $totalBundle = $finalBundleUnit * (int) $this->bundle_qty;

        return $totalMain + $totalBundle;
    }

    public function getFormattedTotalSpecialPriceAttribute(): string
    {
        return Money::inDefaultCurrency($this->total_special_price)
            ->convertToCurrentCurrency()
            ->format();
    }

    public function getTotalDiscountAttribute(): ?string
    {
        $baseTotal = $this->total_base_price;
        $specialTotal = $this->total_special_price;

        if ($baseTotal <= 0 || $specialTotal >= $baseTotal) {
            return null;
        }

        $discountAmount = $baseTotal - $specialTotal;
        $percentage = ($discountAmount / $baseTotal) * 100;

        return round($percentage);
    }

    public function getFinalSpecialPriceAttribute()
    {
        $base = (float) $this->bundle_price;
        $special = (float) $this->special_bundle_price;

        if ($special <= 0) {
            return null;
        }

        if ($this->special_bundle_price_type === 'percent') {
            return $base - ($base * ($special / 100));
        }
        return $special;
    }

    public function getFormattedPriceAttribute()
    {
        $baseMoney = Money::inDefaultCurrency($this->bundle_price);

        $formattedBase = $baseMoney->convertToCurrentCurrency()->format();

        $specialAmount = $this->calculateSpecialAmount();

        if (is_null($specialAmount) || $specialAmount >= (float) $this->bundle_price) {
            return "<span class='autocalc-bundle-price'>{$formattedBase}</span>";
        }
        $specialMoney = Money::inDefaultCurrency($specialAmount);
        $formattedSpecial = $specialMoney->convertToCurrentCurrency()->format();

        return "<span class='old-price'><span class='price_value'>{$formattedBase}</span></span> " .
            "<span class='new-price'><span class='special_value'>{$formattedSpecial}</span></span>";
    }

    public function getFormattedPriceBundleAttribute()
    {
        $baseMoney = Money::inDefaultCurrency($this->bundle_price);

        $formattedBase = $baseMoney->convertToCurrentCurrency()->format();

        $specialAmount = $this->calculateBundleSpecialAmount();

        if (is_null($specialAmount) || $specialAmount >= (float) $this->bundle_price) {
            return "<span class='price'>{$formattedBase}</span>";
        }
        $specialMoney = Money::inDefaultCurrency($specialAmount);
        $formattedSpecial = $specialMoney->convertToCurrentCurrency()->format();

        return "<span class='price-old'>{$formattedBase}</span> " .
            "<span class='price-new'>{$formattedSpecial}</span>";
    }
    public function getFormattedPriceProductAttribute()
    {
        $baseMoney = Money::inDefaultCurrency($this->product_price);

        $formattedBase = $baseMoney->convertToCurrentCurrency()->format();

        $specialAmount = $this->calculateSpecialAmount();

        if (is_null($specialAmount) || $specialAmount >= (float) $this->product_price) {
            return "<span class='price'>{$formattedBase}</span>";
        }
        $specialMoney = Money::inDefaultCurrency($specialAmount);
        $formattedSpecial = $specialMoney->convertToCurrentCurrency()->format();

        return "<span class='price-old'>{$formattedBase}</span> " .
            "<span class='price-new'>{$formattedSpecial}</span>";
    }

    private function calculateSpecialAmount()
    {
        $base = (float) $this->product_price;
        $special = (float) $this->special_price;

        if ($special <= 0) {
            return null;
        }

        if ($this->special_price_type === 'percent') {
            return $base - ($base * ($special / 100));
        }

        return $special;
    }

    private function calculateBundleSpecialAmount()
    {
        $base = (float) $this->bundle_price;
        $special = (float) $this->special_bundle_price;

        if ($special <= 0) {
            return null;
        }

        if ($this->special_bundle_price_type === 'percent') {
            return $base - ($base * ($special / 100));
        }

        return $special;
    }

}
