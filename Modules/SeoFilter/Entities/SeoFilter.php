<?php

namespace Modules\SeoFilter\Entities;

use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
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

    public function fullPath(): string
    {
        $path = trim((string) $this->path, '/');

        if (!$this->category_id || !$this->category->exists) {
            return $path;
        }

        $categoryPath = trim($this->category->getFullPath(), '/');

        if ($path === '') {
            return $categoryPath;
        }

        if ($path === $categoryPath || Str::startsWith($path, $categoryPath . '/')) {
            return $path;
        }

        return trim($categoryPath . '/' . $path, '/');
    }

    public function url(?string $locale = null): string
    {
        $locale = $locale ?: LaravelLocalization::getCurrentLocale();

        return LaravelLocalization::getLocalizedURL(
            $locale,
            url($this->fullPath())
        );
    }
}
