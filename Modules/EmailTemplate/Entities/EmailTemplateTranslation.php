<?php

namespace Modules\EmailTemplate\Entities;

use Modules\Support\Eloquent\Model;

class EmailTemplateTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'subject',
        'content',
    ];
}
