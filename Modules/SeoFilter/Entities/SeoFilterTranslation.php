<?php

namespace Modules\SeoFilter\Entities;

use Modules\Support\Eloquent\Model;

class SeoFilterTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'h1',
        'meta_title',
        'meta_description',
        'description',
    ];
}
