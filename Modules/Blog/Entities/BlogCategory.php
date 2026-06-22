<?php

namespace Modules\Blog\Entities;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Media\Eloquent\HasMedia;
use Modules\Media\Entities\File;
use Spatie\Sitemap\Tags\Url;
use Modules\Support\Eloquent\Model;
use Modules\Meta\Eloquent\HasMetaData;
use Modules\Support\Eloquent\Sluggable;
use Modules\Blog\Admin\BlogCategoryTable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Modules\Support\Eloquent\Translatable;
use TypiCMS\NestableTrait;

class BlogCategory extends Model implements Sitemapable
{
    use Translatable, Sluggable, HasMetaData, HasMedia, NestableTrait;

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
    protected $fillable = ['parent_id', 'slug', 'position', 'is_active'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['translations'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = ['name', 'h1_name', 'description'];

    /**
     * The attribute that will be slugged.
     *
     * @var string
     */
    protected $slugAttribute = 'name';


    public static function findBySlug($slug)
    {
        return static::with('files')->where('slug', $slug)->firstOrNew([]);
    }


    public static function tree()
    {
        return Cache::tags('blog_categories')
            ->rememberForever(md5('blog_categories.tree:' . locale()), function () {
                return static::with('files')
                    ->orderByRaw('-position DESC')
                    ->get()
                    ->nest();
            });
    }

    public static function treeList()
    {
        return Cache::tags('blog_categories')->rememberForever(md5('blog_categories.tree_list:' . locale()), function () {
            return static::orderByRaw('-position DESC')
                ->get()
                ->nest()
                ->setIndent('¦–– ')
                ->listsFlattened('name');
        });
    }

    public static function keyValuedTreeList()
    {
        return Cache::tags('blog_categories')->rememberForever(md5('blog_categories.key_valued_tree_list:' . locale()), function () {
            $categories = static::orderByRaw('-position DESC')
                ->get()
                ->nest()
                ->setIndent('¦–– ')
                ->listsFlattened('name');

            return collect($categories)
                ->map(function ($key, $value) {
                    return [
                        'name' => $key,
                        'value' => $value,
                    ];
                })
                ->values();
        });
    }


    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    protected static function booted()
    {
        static::addActiveGlobalScope();
    }


    public function isRoot()
    {
        return $this->exists && is_null($this->parent_id);
    }

//    public function url()
//    {
//        return route('blog_category.blog_posts.index', ['category' => $this->slug]);
//    }

    public function url()
    {
        // Вместо использования route() просто возвращаем полный путь
        return url($this->getFullPath());
    }

    public function getBannerAttribute()
    {
        return $this->files->where('pivot.zone', 'banner')->first() ?: new File;
    }

    public function toArray()
    {
        $attributes = parent::toArray();

        if ($this->relationLoaded('files')) {
            $attributes += [
                'banner' => [
                    'id' => $this->banner->id,
                    'path' => $this->banner->path,
                    'exists' => $this->banner->exists,
                ],
            ];
        }

        return $attributes;
    }

    public function getAllChildrenIds()
    {
        $ids = [$this->id];

        foreach ($this->items as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }

    public function getDescendantsIds()
    {
        return static::where('parent_id', $this->id)
            ->get()
            ->flatMap(function ($category) {
                return array_merge([$category->id], $category->getDescendantsIds());
            })
            ->toArray();
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    public function getFullPath()
    {
        $slugs = [$this->slug];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($slugs, $parent->slug);
            $parent = $parent->parent;
        }

        return implode('/', $slugs);
    }


    public function toSitemapTag(): Url|string|array
    {
        return Url::create($this->url())
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.1);
    }
}
