<?php

namespace Modules\Order\Entities;

use Modules\Support\Money;
use Modules\Support\Eloquent\Model;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductPackaging;

class OrderProduct extends Model
{
    public $timestamps = false;

    protected $with = ['product', 'options', 'packaging'];

    protected $guarded = [];

    public function url()
    {
        return route('products.show', ['slug' => $this->product->slug]);
    }

    public function hasAnyOption()
    {
        return $this->options->isNotEmpty();
    }

    public function trashed()
    {
        return $this->product->trashed();
    }

    /**
     * Связь с упаковкой
     */
    public function packaging()
    {
        return $this->belongsTo(ProductPackaging::class, 'packaging_id')->withTrashed();
    }

    /**
     * Связь с родительским товаром (для подарков и бандлов)
     */
    public function parent()
    {
        return $this->belongsTo(OrderProduct::class, 'parent_id');
    }

    /**
     * Связь с дочерними товарами
     */
    public function children()
    {
        return $this->hasMany(OrderProduct::class, 'parent_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)
            ->withoutGlobalScope('active')
            ->withTrashed();
    }

    public function storeOptions($options)
    {
        if (empty($options)) return;

        foreach ($options as $optionData) {
            $optionId = is_object($optionData) ? $optionData->id : $optionData['id'];

            $orderProductOption = $this->options()->create([
                'order_product_id' => $this->id,
                'option_id' => $optionId,
                'value' => null,
            ]);

            $values = is_object($optionData) ? $optionData->values : $optionData['values'];
            $orderProductOption->storeValues($values);
        }
    }

    public function options()
    {
        return $this->hasMany(OrderProductOption::class);
    }

    public function getNameAttribute()
    {
        return $this->product->name;
    }

    public function getSlugAttribute()
    {
        return $this->product->slug;
    }

    public function getUnitPriceAttribute($unitPrice)
    {
        return Money::inDefaultCurrency($unitPrice);
    }

    public function getLineTotalAttribute($total)
    {
        return Money::inDefaultCurrency($total);
    }

    public function getSkuAttribute()
    {
        // Убрали проверку на product_variant
        return $this->product->sku;
    }
}
