<?php

namespace Modules\Blog\Entities;

use Carbon\Carbon;
use Modules\Faq\Eloquent\HasFaq;
use Spatie\Sitemap\Tags\Url;
use Modules\User\Entities\User;
use Modules\Media\Entities\File;
use Modules\Support\Eloquent\Model;
use Modules\Media\Eloquent\HasMedia;
use Modules\Blog\Admin\BlogPostTable;
use Modules\Meta\Eloquent\HasMetaData;
use Modules\Support\Search\Searchable;
use Modules\Support\Eloquent\Sluggable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Modules\Support\Eloquent\Translatable;

class BlogPost extends Model implements Sitemapable
{
    use Translatable, Sluggable, HasMedia, Searchable, HasMetaData, HasFaq;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that are mass assignable.
     * removed 'user_id'
     * @var array
     */
    protected $fillable = ['slug', 'blog_category_id', 'is_active'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_image', 'preview'];


    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addActiveGlobalScope();

        static::deleting(function (self $model) {
            $entityType = $model::class;
            $entityId = $model->id;

            \DB::table('entity_files')
                ->where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->delete();
        });

    }


    public function table()
    {
        return new BlogPostTable($this->newQuery()->withoutGlobalScope('active'));
    }


    /**
     * Find a specific blog by the given slug.
     *
     * @param string $slug
     *
     * @return self
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', $slug);
    }

    /**
     * Get related category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Get the indexable data array for the product.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        # MySQL Full-Text search handles indexing automatically.
        if (config('scout.driver') === 'mysql') {
            return [];
        }

        $translations = $this->translations()
            ->withoutGlobalScope('locale')
            ->get(['name', 'description']);

        return [
            'id' => $this->id,
            'translations' => $translations,
        ];
    }


    public function searchTable(): string
    {
        return 'blog_post_translations';
    }


    public function searchKey(): string
    {
        return 'blog_post_id';
    }


    public function searchColumns(): array
    {
        return ['name'];
    }



    /**
     * Get the brand's preview.
     *
     * @return File
     */
    public function getPreviewAttribute()
    {
        return $this->files->where('pivot.zone', 'preview')->first() ?: new File;
    }


    /**
     * Get the brand's full_image.
     *
     * @return File
     */
    public function getFullImageAttribute()
    {
        return $this->files->where('pivot.zone', 'full_image')->first() ?: new File;
    }


//    public function url(): string
//    {
//        return route('blog_posts.show', ['slug' => $this->slug]);
//    }

    public function url(): string
    {
        if ($this->category) {
            return url($this->category->getFullPath() . '/' . $this->slug);
        }

        return url($this->slug);
    }

    public function extractMediaFromRequest(): mixed
    {
        $media = collect(request('files', []));

        return [
            'preview' => $media->first(),
            'full_image' => $media->except(
                $media->keys()->first()
            )->flatten()->first(),
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }


    public function toSitemapTag(): Url|string|array
    {
        return Url::create($this->url())
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.1);
    }
}
