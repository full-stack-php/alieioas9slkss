<?php

namespace Modules\Order\Entities;

use Modules\Support\Eloquent\TranslationModel;

class OrderStatusTranslation extends TranslationModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_status_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
