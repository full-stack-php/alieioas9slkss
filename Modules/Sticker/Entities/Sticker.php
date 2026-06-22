<?php

namespace Modules\Sticker\Entities;

use Modules\Media\Entities\File;
use Modules\Media\Eloquent\HasMedia;
use Modules\Product\Entities\Product;
use Modules\Sticker\Admin\StickerTable;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sticker extends Model
{
    use Translatable, HasMedia, SoftDeletes;

    /**
     * Text label sticker.
     */
    public const TYPE_LABEL = 'label';

    /**
     * Image sticker.
     */
    public const TYPE_IMAGE = 'image';

    /**
     * Extended information sticker.
     */
    public const TYPE_INFO = 'info';

    /**
     * Available sticker types.
     *
     * @var array
     */
    public const TYPES = [
        self::TYPE_LABEL,
        self::TYPE_IMAGE,
        self::TYPE_INFO,
    ];

    /**
     * Media zone used for the sticker image.
     */
    public const MEDIA_ZONE_IMAGE = 'image';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'translations',
        'files',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'text_color',
        'background_color',
        'image_background_color',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'translations',
        'files',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'name',
        'image_alt',
        'description',
        'popup_description',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image',
    ];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addActiveGlobalScope();
    }

    /**
     * Get products related to the sticker.
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_stickers'
        )
            ->withPivot('sort_order')
            ->orderBy('product_stickers.sort_order')
            ->orderBy('products.id');
    }

    /**
     * Get the sticker image.
     *
     * @return File
     */
    public function getImageAttribute(): File
    {
        return $this->files
            ->where('pivot.zone', self::MEDIA_ZONE_IMAGE)
            ->first() ?: new File;
    }

    /**
     * Determine whether the sticker is a text label.
     *
     * @return bool
     */
    public function isLabel(): bool
    {
        return $this->type === self::TYPE_LABEL;
    }

    /**
     * Determine whether the sticker is an image.
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * Determine whether the sticker is informational.
     *
     * @return bool
     */
    public function isInfo(): bool
    {
        return $this->type === self::TYPE_INFO;
    }

    /**
     * Get table data for the resource.
     *
     * @return StickerTable
     */
    public function table(): StickerTable
    {
        return new StickerTable(
            $this->newQuery()->withoutGlobalScope('active')
        );
    }
}
