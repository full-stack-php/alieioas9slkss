<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\TranslationModel;

class ProductTranslation extends TranslationModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'h1_name', 'description', 'notice_message'];
}
