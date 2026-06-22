<?php

namespace Modules\Option\Entities;

use Modules\Support\Eloquent\Model;

class ProductOption extends Model
{
    public $timestamps = false;
    protected $with = ['option', 'values'];
    protected $fillable = ['product_id', 'option_id', 'is_required', 'position'];
    protected $appends = ['name', 'type'];

    public function option() {
        return $this->belongsTo(Option::class);
    }

    public function values() {
        return $this->hasMany(ProductOptionValue::class, 'product_option_id')
            ->orderBy('position');
    }

    public function getNameAttribute() {
        return $this->option->name ?? '';
    }

    public function getTypeAttribute() {
        return $this->option->type ?? '';
    }

    public function saveValues($values = [])
    {
        $values = array_reset_index($values);
        $incomingOptionValueIds = array_filter(array_column($values, 'option_value_id'));

        $this->values()->whereNotIn('option_value_id', $incomingOptionValueIds)->delete();

        foreach ($values as $k => $attributes) {
            if(is_null($attributes['option_value_id'])){
                continue;
            }
            $attributes['product_id'] = $this->product_id;
            $attributes['option_id'] = $this->option_id;
            $attributes['position'] = $k;

            $this->values()->updateOrCreate(
                ['option_value_id' => $attributes['option_value_id']],
                $attributes
            );
        }
    }
}
