<?php

namespace Modules\Shipping\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpWarehouse extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    protected $with = ['translations'];
    protected $fillable = ['ref', 'city_id', 'number', 'is_postomat', 'max_weight', 'is_active'];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_postomat' => 'boolean',
    ];

    public static function findByRef($ref)
    {
        return self::where('ref', $ref)->first();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(NpCity::class, 'city_id');
    }
}
