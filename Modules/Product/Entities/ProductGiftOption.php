<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Option\Entities\ProductOption;
use Modules\Option\Entities\ProductOptionValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGiftOption extends Model
{
    protected $fillable = [
        'gift_id',
        'product_option_id',
        'product_option_value_id',
    ];

    public function gift(): BelongsTo
    {
        return $this->belongsTo(ProductGift::class, 'gift_id');
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function productOptionValue(): BelongsTo
    {
        return $this->belongsTo(ProductOptionValue::class, 'product_option_value_id');
    }
}
