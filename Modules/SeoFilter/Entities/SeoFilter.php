<?php

namespace Modules\SeoFilter\Entities;

use Modules\Category\Entities\Category;
use Modules\SeoFilter\Admin\SeoFilterTable;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Support\Facades\Cache;

class SeoFilter extends Model
{
    use Translatable;

    protected $fillable = [
        'category_id',
        'query_string',
        'path',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public array $translatedAttributes = [
        'h1',
        'meta_title',
        'meta_description',
        'description',
    ];

    protected $with = ['translations'];


    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_url_map');
        });

        static::deleted(function () {
            Cache::forget('app_url_map');
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function table()
    {
        return new SeoFilterTable(
            $this->newQuery()
                ->withoutGlobalScope('active')
                ->with('category.translations')
        );
    }

    public function url(): string
    {
        return url($this->path);
    }
}
