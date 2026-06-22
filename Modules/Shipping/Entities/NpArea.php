<?php

namespace Modules\Shipping\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NpArea extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    protected $with = ['translations'];
    protected $fillable = ['ref', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function findByRef($ref)
    {
        return self::where('ref', $ref)->first();
    }

    public function cities(): HasMany
    {
        return $this->hasMany(NpCity::class, 'area_id');
    }
}
