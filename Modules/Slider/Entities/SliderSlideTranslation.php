<?php

namespace Modules\Slider\Entities;

use Modules\Support\Eloquent\TranslationModel;

class SliderSlideTranslation extends TranslationModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_id',
        'title',
        'price_from',
        'price_text',
        'sub_title',
    ];
}
