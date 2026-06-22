<?php

namespace Modules\Faq\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;

class Faq extends Model
{

    use Translatable;

    protected $table = 'faq';

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
    protected $fillable = ['entity_id', 'entity_type', 'position'];


    /**
     * The attributes that are translatable.
     *
     * @var array
     */

    protected $translatedAttributes = ['question', 'answer'];


    /**
     * Get parent Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
