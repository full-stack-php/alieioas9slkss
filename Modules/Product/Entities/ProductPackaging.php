<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Support\Money;

class ProductPackaging extends Model
{
    use SoftDeletes, Translatable;

    protected $fillable = [
        'product_id',
        'qty',
        'price',
        'special_price',
        'special_price_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'qty' => 'integer',
        'price' => 'float',
        'special_price' => 'float',
    ];

    public array $translatedAttributes = ['name'];

    protected $with = ['translations'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(ProductGift::class, 'parent_packaging_id')
            ->with([
                'giftProduct.translations',
                'giftPackaging.translations',
                'options.productOption',
                'options.productOptionValue',
            ]);
    }

    public function activeGifts(): HasMany
    {
        return $this->gifts()->where('is_active', true);
    }

    public function getFormattedPriceAttribute()
    {
        $baseMoney = Money::inDefaultCurrency($this->price * $this->qty);
        $formattedBase = $baseMoney->convertToCurrentCurrency()->format();

        $specialAmount = $this->calculateSpecialAmount();

        if (is_null($specialAmount) || $specialAmount >= (float) $this->price) {
            return "<span class='new-price'>{$formattedBase}</span>";
        }

        $formattedSpecial = Money::inDefaultCurrency($specialAmount * $this->qty)
            ->convertToCurrentCurrency()
            ->format();

        return "<span class='old-price'>{$formattedBase}</span> <span class='new-price'>{$formattedSpecial}</span>";
    }

    private function calculateSpecialAmount()
    {
        $price = (float) $this->price;
        $special = (float) $this->special_price;

        if ($special <= 0) {
            return null;
        }

        if ($this->special_price_type === 'percent') {
            return $price - ($price * ($special / 100));
        }

        return $special;
    }
}
