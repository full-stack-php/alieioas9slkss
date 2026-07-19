<?php

namespace Modules\Product\Entities\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Option\Entities\ProductOption;
use Modules\Product\Entities\ProductBundle;
use Modules\Product\Entities\ProductGift;
use Modules\Product\Entities\ProductPackaging;
use Modules\Brand\Entities\Brand;
use Modules\Option\Entities\Option;
use Modules\Product\Entities\ProductVideo;
use Modules\QuestionAnswer\Entities\QuestionAnswer;
use Modules\Review\Entities\Review;
use Modules\Category\Entities\Category;
use Modules\Attribute\Entities\ProductAttribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Sticker\Entities\Sticker;

trait EloquentRelations
{
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class)->withDefault();
    }


    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'manufacturer_id')->withDefault();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Связь с главной категорией
     */
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }


    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function questionsanswers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }


    public function attributes(): HasMany
    {
        return $this
            ->hasMany(ProductAttribute::class)
            ->orderBy('position')
            ->orderBy('id');
    }

    public function options(): HasMany
    {
        return $this->HasMany(ProductOption::class)
            ->orderBy('position');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'related_products', 'product_id', 'related_product_id');
    }


    public function colorProducts(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'color_products', 'product_id', 'color_product_id');
    }


    public function crossSellProducts(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'cross_sell_products', 'product_id', 'cross_sell_product_id');
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(ProductBundle::class, 'product_id')->with('bundleProduct');
    }

    /**
     * Get all stickers assigned to the product.
     *
     * @return BelongsToMany
     */
    public function stickers(): BelongsToMany
    {
        return $this->belongsToMany(
            Sticker::class,
            'product_stickers'
        )
            ->withPivot('sort_order')
            ->orderBy('product_stickers.sort_order')
            ->orderBy('stickers.sort_order')
            ->orderBy('stickers.id');
    }


    /**
     * Get text label stickers assigned to the product.
     *
     * @return BelongsToMany
     */
    public function labelStickers(): BelongsToMany
    {
        return $this->stickers()
            ->where(
                'stickers.type',
                Sticker::TYPE_LABEL
            );
    }

    /**
     * Get image stickers assigned to the product.
     *
     * @return BelongsToMany
     */
    public function imageStickers(): BelongsToMany
    {
        return $this->stickers()
            ->where(
                'stickers.type',
                Sticker::TYPE_IMAGE
            );
    }


    /**
     * Get informational stickers assigned to the product.
     *
     * @return BelongsToMany
     */
    public function infoStickers(): BelongsToMany
    {
        return $this->stickers()
            ->where(
                'stickers.type',
                Sticker::TYPE_INFO
            );
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ProductVideo::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function mainVideo(): HasOne
    {
        return $this->hasOne(ProductVideo::class)
            ->where('is_main', true);
    }

    public function gifts(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'product_gifts', 'parent_product_id', 'gift_product_id')
            ->withoutGlobalScope('active')
            ->withPivot([
                'parent_packaging_id',
                'gift_packaging_id',
                'price',
                'min_qty',
                'is_active',
                'deleted_at',
            ])
            ->wherePivotNull('deleted_at')
            ->where('products.is_active', true)
            ->where('products.in_stock', true)
            ->withTimestamps();
    }

    public function productGifts(): HasMany
    {
        return $this->hasMany(ProductGift::class, 'parent_product_id')
            ->with([
                'giftProduct.translations',
                'giftPackaging.translations',
                'options.productOption',
                'options.productOptionValue',
            ]);
    }

    public function activeGifts(): HasMany
    {
        return $this->hasMany(ProductGift::class, 'parent_product_id')
            ->where('product_gifts.is_active', true)
            ->whereHas('giftProduct', function ($query) {
                $query->withoutGlobalScope('active')
                    ->where('products.is_active', true)
                    ->where('products.in_stock', true);
            });
    }

    public function allPackagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class)
            ->with([
                'translations' => function ($query) {
                    $query->withoutGlobalScope('locale');
                },
                'gifts.giftProduct.translations',
                'gifts.giftPackaging.translations',
                'gifts.options.productOption',
                'gifts.options.productOptionValue',
            ]);
    }

    public function packagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class)
            ->withoutGlobalScope('locale')
            ->with([
                'translations' => function ($query) {
                    $query->withoutGlobalScope('locale');
                },
                'gifts.giftProduct.translations',
                'gifts.giftPackaging.translations',
                'gifts.options.productOption',
                'gifts.options.productOptionValue',
            ])
            ->where('is_active', true)
            ->orderBy('price', 'asc');
    }

    public function adminPackagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class)
            ->withoutGlobalScope('locale')
            ->with([
                'translations' => function ($query) {
                    $query->withoutGlobalScope('locale');
                },
                'gifts.giftProduct.translations',
                'gifts.giftPackaging.translations',
                'gifts.options.productOption',
                'gifts.options.productOptionValue',
            ])
            ->orderBy('price', 'asc');
    }

}
