<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGift extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'gift_product_id',
        'price',
        'min_qty',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function giftProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'gift_product_id')
            ->where('is_active', true)
            ->where('in_stock', true);
    }
}
