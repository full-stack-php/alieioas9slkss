<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;

class ProductPackaging extends Model
{
    use SoftDeletes, Translatable;

    protected $fillable = ['product_id', 'qty', 'price', 'special_price', 'special_price_type', 'gift_id', 'is_gift'];

    public array $translatedAttributes = ['name'];

    protected $with = ['translations'];


    public function gift()
    {
        return $this->belongsTo(self::class, 'gift_id')->where('is_gift', true);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedPriceAttribute()
    {
        $baseMoney = \Modules\Support\Money::inDefaultCurrency($this->price * $this->qty);
        $formattedBase = $baseMoney->convertToCurrentCurrency()->format();

        $specialAmount = $this->calculateSpecialAmount();

        if (is_null($specialAmount) || $specialAmount >= (float) $this->price) {
            return "<span class='new-price'>{$formattedBase}</span>";
        }

        $formattedSpecial = \Modules\Support\Money::inDefaultCurrency($specialAmount * $this->qty)
            ->convertToCurrentCurrency()
            ->format();

        return "<span class='old-price'>{$formattedBase}</span> <span class='new-price'>{$formattedSpecial}</span>";
    }

    private function calculateSpecialAmount()
    {
        $price = (float) $this->price;
        $special = (float) $this->special_price;

        if ($special <= 0) return null;

        if ($this->special_price_type === 'percent') {
            return $price - ($price * ($special / 100));
        }

        return $special;
    }
}
