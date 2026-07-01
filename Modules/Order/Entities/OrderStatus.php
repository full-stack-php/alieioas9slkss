<?php

namespace Modules\Order\Entities;

use Modules\Order\Admin\OrderStatusTable;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;

class OrderStatus extends Model
{
    use Translatable;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['color', 'is_active'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = ['name'];


    protected static function booted()
    {
        static::addActiveGlobalScope();
    }


    public function table()
    {
        return new OrderStatusTable($this->newQuery()->withoutGlobalScope('active'));
    }

    public function translatedName()
    {
        return optional(
            $this->translations->where('locale', locale())->first()
        )->name ?? optional($this->translations->first())->name;
    }

    public static function list()
    {
        return static::query()
            ->where('is_active', true)
            ->with('translation')
            ->get()
            ->mapWithKeys(function ($status) {
                return [
                    $status->id => optional($status->translation)->name,
                ];
            })
            ->filter()
            ->toArray();
    }

    public static function listStatuses()
    {
        return static::select('id')->get()->pluck('name', 'id');
    }
}
