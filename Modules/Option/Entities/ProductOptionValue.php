<?php

namespace Modules\Option\Entities;

use Modules\Support\Eloquent\Model;

class ProductOptionValue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_option_id',
        'option_id',
        'option_value_id',
        'price',
        'price_type',
        'special_price',
        'special_price_type',
        'position',
        'old_id'
    ];

    protected $with = ['optionValue'];

    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id');
    }

    public function getLabelAttribute()
    {
        return $this->optionValue->label ?? '';
    }
}
