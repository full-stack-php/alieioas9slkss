<?php

namespace Modules\Slider\Entities;

use Modules\Media\Entities\File;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;

class SliderSlide extends Model
{
    use Translatable;

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [
        'file_id',
        'title',
        'price_from',
        'price_text',
        'sub_title',
    ];
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations', 'file'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title_color', 'sub_title_color', 'price_text_color', 'price_color', 'call_to_action_url', 'open_in_new_window', 'position'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'open_in_new_window' => 'boolean',
    ];


    public function file()
    {
        return $this->belongsTo(File::class)->withDefault();
    }
}
