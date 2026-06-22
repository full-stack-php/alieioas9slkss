<?php

namespace Modules\Shipping\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NpCity extends Model
{
    use Translatable;

    public $translatedAttributes = ['name', 'type'];
    protected $with = ['translations'];
    protected $fillable = ['ref', 'area_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function findByRef($ref)
    {
        return self::where('ref', $ref)->first();
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(NpArea::class, 'area_id');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(NpWarehouse::class, 'city_id');
    }
}
