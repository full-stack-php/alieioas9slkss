<?php

namespace Modules\Order\Entities;

use Modules\Option\Entities\Option;
use Illuminate\Database\Eloquent\Model;
use Modules\Option\Entities\OptionValue;

class OrderProductOption extends Model
{
    public $timestamps = false;

    protected $with = ['option', 'values'];

    protected $guarded = [];

    public function option()
    {
        return $this->belongsTo(Option::class)->withTrashed();
    }

    public function getNameAttribute()
    {
        return $this->option->name;
    }

    public function isFieldType()
    {
        return $this->option->isFieldType();
    }

    /**
     * Теперь метод принимает сырые значения из корзины, а не объекты моделей.
     */
    public function storeValues($values)
    {
        $syncData = [];

        foreach ($values as $val) {
            $valueId = is_object($val) ? $val->id : $val['id'];

            // Берем цену прямо из слепка корзины
            $price = is_object($val) ? $val->price : $val['price'];
            $priceType = is_object($val) ? $val->price_type : $val['price_type'];

            $syncData[$valueId] = [
                'price' => (float) $price,
                'price_type' => $priceType, // Если вы добавили это поле в миграцию (шаг выше)
            ];
        }

        $this->values()->attach($syncData);
    }

    public function values()
    {
        return $this->belongsToMany(OptionValue::class, 'order_product_option_values')
            ->using(OrderProductOptionValue::class)
            ->withPivot(['price', 'price_type']); // Добавили price_type
    }
}
