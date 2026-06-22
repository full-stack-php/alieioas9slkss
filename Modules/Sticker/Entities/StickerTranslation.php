<?php

namespace Modules\Sticker\Entities;

use Modules\Support\Eloquent\TranslationModel;

class StickerTranslation extends TranslationModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image_alt',
        'description',
        'popup_description',
    ];
}
