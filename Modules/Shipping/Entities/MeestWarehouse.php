<?php

namespace Modules\Shipping\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MeestWarehouse extends Model
{
    use Translatable;

    public $translatedAttributes = ['name', 'type'];
    protected $with = ['translations'];
    protected $fillable = ['ref', 'city_id', 'type', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    /**
     * Связь с городом
     */
    public function city()
    {
        return $this->belongsTo(MeestCity::class, 'city_id');
    }

}
