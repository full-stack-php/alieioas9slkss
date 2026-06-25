<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGift extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_product_id',
        'parent_packaging_id',
        'gift_product_id',
        'gift_packaging_id',
        'price',
        'min_qty',
        'gift_qty',
        'is_repeatable',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_repeatable' => 'boolean',
        'price' => 'float',
        'min_qty' => 'integer',
        'gift_qty' => 'integer',
    ];

    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id')
            ->withoutGlobalScope('active');
    }

    public function product(): BelongsTo
    {
        return $this->parentProduct();
    }

    public function parentPackaging(): BelongsTo
    {
        return $this->belongsTo(ProductPackaging::class, 'parent_packaging_id');
    }

    public function giftProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'gift_product_id')
            ->withoutGlobalScope('active')
            ->where('products.is_active', true)
            ->where('products.in_stock', true);
    }

    public function giftPackaging(): BelongsTo
    {
        return $this->belongsTo(ProductPackaging::class, 'gift_packaging_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductGiftOption::class, 'gift_id');
    }

    public function selectedOptionsArray(): array
    {
        return $this->options
            ->pluck('product_option_value_id', 'product_option_id')
            ->toArray();
    }
}
