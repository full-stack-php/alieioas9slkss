<?php

namespace Modules\Product\Entities;

use Illuminate\Http\Request;
use Modules\Faq\Eloquent\HasFaq;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Carbon;
use Modules\Support\Eloquent\Model;
use Modules\Media\Eloquent\HasMedia;
use Modules\Meta\Eloquent\HasMetaData;
use Modules\Support\Search\Searchable;
use Modules\Product\Admin\ProductTable;
use Modules\Support\Eloquent\Sluggable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Modules\Support\Eloquent\Translatable;
use Modules\Product\Entities\Concerns\IsNew;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Entities\Concerns\HasStock;
use Modules\Product\Entities\Concerns\Predicates;
use Modules\Product\Entities\Concerns\Filterable;
use Modules\Product\Entities\Concerns\QueryScopes;
use Modules\Product\Entities\Concerns\ModelMutators;
use Modules\Product\Entities\Concerns\ModelAccessors;
use Modules\Product\Entities\Concerns\HasSpecialPrice;
use Modules\Product\Entities\Concerns\EloquentRelations;
use function Symfony\Component\Translation\t;

class Product extends Model implements Sitemapable
{
    public const STOCK_STATUS_NOT_TRACKED = 0;
    public const STOCK_STATUS_TRACKED = 1;
    public const STOCK_STATUS_PREORDER = 2;
    public const STOCK_STATUS_DISCONTINUED = 3;

    public const STOCK_STATUSES = [
        self::STOCK_STATUS_NOT_TRACKED,
        self::STOCK_STATUS_TRACKED,
        self::STOCK_STATUS_PREORDER,
        self::STOCK_STATUS_DISCONTINUED,
    ];

    use Translatable,
        Searchable,
        Filterable,
        Sluggable,
        HasMedia,
        HasMetaData,
        HasSpecialPrice,
        HasStock,
        SoftDeletes,
        IsNew,
        QueryScopes,
        ModelAccessors,
        ModelMutators,
        Predicates,
        HasFaq,
        EloquentRelations;

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
    protected $fillable = [
        'brand_id',
        'main_category_id',
        'manufacturer_id',
        'slug',
        'sku',
        'price',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'selling_price',
        'manage_stock',
        'stock_status',
        'qty',
        'in_stock',
        'is_mirrored',
        'is_active',
        'new_from',
        'new_to',
        'old_id',
        '1c_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'manage_stock' => 'boolean',
        'stock_status' => 'integer',
        'qty' => 'integer',
        'in_stock' => 'boolean',
        'is_mirrored' => 'boolean',
        'is_active' => 'boolean',
        'special_price_start' => 'datetime',
        'special_price_end' => 'datetime',
        'new_from' => 'datetime',
        'new_to' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'base_image',
        'additional_images',
        'media',
        'formatted_price',
        'has_percentage_special_price',
        'special_price_percent',
        'rating_percent',
        'does_manage_stock',
        'is_in_stock',
        'is_out_of_stock',
        'is_new',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public array $translatedAttributes = [
        'name',
        'h1_name',
        'description',
        'notice_message',
    ];


    /**
     * The attribute that will be slugged.
     *
     * @var string
     */
    protected string $slugAttribute = 'name';


    public array $availableRelationForExport = [
        'brand',
        'manufacturer',
        'categories',
        'allPackagings',
        'mainCategory',
        'bundles',
        'colorProducts',
        'crossSellProducts',
        'relatedProducts',
        'productGifts',
        'attributes',
        'options',
        'meta',
        'media'
    ];

    public function exportFieldsForMedia(): array
    {
        return [
            'url' => 'Ссылки на изображения (Все фото товара)',
        ];
    }
    public function exportFieldsForMeta(): array
    {
        return [
            'meta_title' => 'Meta Title (Перевод)',
            'meta_description' => 'Meta Description (Перевод)',
        ];
    }

    public function exportFieldsForAttributes(): array
    {
        return [
            'attribute.name' => 'Название атрибута',
            'values.*.attributeValue.value' => 'Значения атрибута (Все)',
        ];
    }

    /**
     * Поля для связи Опции
     */
    public function exportFieldsForOptions(): array
    {
        return [
            'option.name' => 'Название опции',
            'option.type' => 'Тип опции',
            'values.*.optionValue.label' => 'Значения опции (Все)',
            'values.*.price' => 'Цены значений',
            'values.*.price_type' => 'Типы цен (+, -, =)',
        ];
    }
    /**
     * Поля для связи Опции
     */
    public function exportFieldsForProductGifts(): array
    {
        return [
            'id' => 'ID связи',
            'gift_product_id' => 'ID товара-подарка',
            'giftProduct.name' => 'Название подарка (Перевод)', // Достаем мультиязычное название!
            'giftProduct.sku' => 'Артикул подарка',
            'price' => 'Цена (если платная)',
            'min_qty' => 'Мин. кол-во для подарка',
        ];
    }

    public function exportFieldsForColorProducts(): array
    {
        return [
            'id' => 'ID цвета',
            'name' => 'Название (Перевод)',
        ];
    }

    /**
     * Поля для связи Кросс-селлы
     */
    public function exportFieldsForCrossSellProducts(): array
    {
        return [
            'id' => 'ID товара',
            'name' => 'Название (Перевод)',
        ];
    }

    /**
     * Поля для связи Сопутствующие товары
     */
    public function exportFieldsForRelatedProducts(): array
    {
        return [
            'id' => 'ID товара',
            'name' => 'Название (Перевод)',
        ];
    }

    /**
     * Поля для связи Бренд
     */
    public function exportFieldsForBrand(): array
    {
        return [
            'id' => 'ID бренда',
            'name' => 'Название бренда',
        ];
    }

    /**
     * Поля для связи Категории
     */
    public function exportFieldsForCategories(): array
    {
        return [
            'id' => 'ID категории',
            'name' => 'Название категории',
        ];
    }

    public function exportFieldsForAllPackagings(): array
    {
        return [
            'id' => 'ID упаковки',
            'name' => 'Название (Перевод)',
            'price' => 'Цена',
            'special_price' => 'Акционная цена',
            'qty' => 'Количество (множитель)',
            'is_active' => 'Активна? (1 / 0)',
            'formatted_price' => 'Форматированная цена',
        ];
    }

    public function exportFieldsForMainCategory(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название (Перевод)',
        ];
    }

    /**
     * Поля для связи Комплекты (Bundles)
     */
    public function exportFieldsForBundles(): array
    {
        return [
            'id' => 'ID связи',
            'bundle_product_id' => 'ID связанного товара',
            'bundleProduct.name' => 'Название связанного товара (Перевод)', // Магия вложенных связей!
            'bundleProduct.sku' => 'Артикул связанного товара',
            'product_qty' => 'Кол-во основного товара',
            'bundle_qty' => 'Кол-во товара в комплекте',
            'product_price' => 'Цена основного товара',
            'bundle_price' => 'Цена товара в комплекте',
            'total_base_price' => 'Итоговая базовая цена',
            'total_special_price' => 'Итоговая цена со скидкой',
            'total_discount' => 'Размер скидки (%)',
        ];
    }

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addActiveGlobalScope();

        static::saved(function ($product) {
            $attributes = request()->all();

            if (!empty($attributes)) {
                $product->categories()->sync(array_get($attributes, 'categories', []));
//                $product->colorProducts()->sync(array_get($attributes, 'colors', []));
                $product->crossSellProducts()->sync(array_get($attributes, 'cross_sells', []));
                $product->relatedProducts()->sync(array_get($attributes, 'related_products', []));
                self::updatePackings($attributes, $product);
                self::updateProductGifts($attributes, $product);
                self::syncMirrorColors($product, array_get($attributes, 'colors', []));
                self::updateProductBundles($product, array_get($attributes, 'bundles', []));
                self::updateProductVideos($attributes, $product);
            }

            $product->withoutEvents(function () use ($product) {
                $product->update([
                    'selling_price' => ($product->hasSpecialPrice() ? $product->getSpecialPrice() : $product->price)->amount(),
                ]);
            });
        });
    }


    /**
     * Get table data for the resource
     *
     * @param Request $request
     *
     * @return ProductTable
     */
    public function table(Request $request): ProductTable
    {
        $query = $this->newQuery()
            ->withoutGlobalScope('active')
            ->withName()
            ->withBaseImage()
            ->withPrice()
            ->addSelect(['id', 'is_active', 'in_stock', 'manage_stock', 'stock_status', 'qty', 'created_at', 'updated_at'])
            ->when($request->has('except'), function ($query) use ($request) {
                $query->whereNotIn('id', explode(',', $request->except));
            });

        $selectedIds = collect((array) $request->input('selected_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if (!empty($selectedIds)) {
            $ids = implode(',', $selectedIds);

            $query->orderByRaw("FIELD(products.id, {$ids}) DESC");
        }

        $query->when($request->filled('page_filter_brand_id'), function ($q) use ($request) {
            $q->where('brand_id', $request->input('page_filter_brand_id'));
        });

        $query->when($request->filled('page_filter_is_active'), function ($q) use ($request) {
            $q->where('is_active', $request->input('page_filter_is_active'));
        });

        $query->when($request->filled('page_filter_category_id'), function ($q) use ($request) {
            $categoryId = $request->input('page_filter_category_id');

            $q->where(function ($subQuery) use ($categoryId) {
                $subQuery->where('products.main_category_id', $categoryId)
                    ->orWhereHas('categories', function ($catQuery) use ($categoryId) {
                        $catQuery->where('categories.id', $categoryId);
                    });
            });
        });

        return new ProductTable($query);
    }





    public function relatedProductList()
    {
        return $this->relatedProducts()
            ->withoutGlobalScope('active')
            ->pluck('related_product_id');
    }

    public function colorProductsList()
    {
        return $this->colorProducts()
            ->withoutGlobalScope('active')
            ->pluck('color_product_id');
    }

    public function crossSellProductList()
    {
        return $this->crossSellProducts()
            ->withoutGlobalScope('active')
            ->pluck('cross_sell_product_id');
    }


    public function clean(): array
    {
        $cleanExceptAttributes = [
            'description',
            'h1_name',
            'translations',
            'categories',
            'files',
            'in_stock',
            'brand_id',
            'viewed',
            'is_mirrored',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        return array_except(
            $this->toArray(),
            $cleanExceptAttributes
        );
    }


    public function url(): string
    {
        return route('products.show', ['slug' => $this->slug]);
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
            ->get(['name', 'description', 'short_description']);

        return [
            'id' => $this->id,
            'translations' => $translations,
        ];
    }


    public function searchTable(): string
    {
        return 'product_translations';
    }


    public function searchKey(): string
    {
        return 'product_id';
    }


    public function searchColumns(): array
    {
        return ['name'];
    }


    /**
     * Help HasMedia trait to extract media
     * for this model from the HTTP request.
     *
     * @return mixed
     */
    public function extractMediaFromRequest(): mixed
    {
        $media = collect(request('files', []));

        return [
            'base_image' => $media->first(),
            'additional_images' =>
                $media->except(
                    $media->keys()->first()
                )->flatten()->toArray(),
            'downloads' => request('downloads', []),
        ];
    }


    public function toSitemapTag(): Url|string|array
    {
        return Url::create($this->url())
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.1);
    }

    private static function updatePackings(array $attributes, Product $product)
    {
        $packagings = $attributes['packagings'] ?? [];

        $idsInForm = collect($packagings)
            ->pluck('id')
            ->filter()
            ->toArray();

        if (empty($packagings)) {
            $packagingIds = $product->allPackagings()->pluck('id')->toArray();

            ProductGift::where('parent_product_id', $product->id)
                ->whereIn('parent_packaging_id', $packagingIds)
                ->update(['parent_packaging_id' => null]);

            $product->allPackagings()->delete();

            return true;
        }

        $packagingIdsToDelete = $product->allPackagings()
            ->whereNotIn('id', $idsInForm)
            ->pluck('id')
            ->toArray();

        if (!empty($packagingIdsToDelete)) {
            ProductGift::where('parent_product_id', $product->id)
                ->whereIn('parent_packaging_id', $packagingIdsToDelete)
                ->update(['parent_packaging_id' => null]);

            $product->allPackagings()
                ->whereIn('id', $packagingIdsToDelete)
                ->delete();
        }

        foreach ($packagings as $data) {
            $saveData = array_except($data, [
                'gift_id',
                'is_gift',
                'gifts',
            ]);

            $product->allPackagings()->updateOrCreate(
                ['id' => array_get($data, 'id')],
                array_merge($saveData, [
                    'qty' => array_get($data, 'qty', 1),
                    'price' => array_get($data, 'price', 0),
                    'special_price' => array_get($data, 'special_price', 0),
                    'special_price_type' => array_get($data, 'special_price_type', 'fixed'),
                    'is_active' => array_get($data, 'is_active', 0) ? 1 : 0,
                ])
            );
        }

        return true;
    }


    private static function updateProductGifts(array $attributes, Product $product)
    {
        $gifts = array_get($attributes, 'product_gifts', []);
        $idsInForm = collect($gifts)->pluck('id')->filter()->toArray();

        if (empty($gifts)) {
            ProductGift::where('parent_product_id', $product->id)->delete();
            return;
        }

        ProductGift::where('parent_product_id', $product->id)
            ->whereNotIn('id', $idsInForm)
            ->delete();

        foreach ($gifts as $data) {
            $giftProductId = array_get($data, 'gift_product_id');

            if (!$giftProductId) {
                continue;
            }

            $parentPackagingId = array_get($data, 'parent_packaging_id') ?: null;
            $giftPackagingId = array_get($data, 'gift_packaging_id') ?: null;

            if ($parentPackagingId && !$product->allPackagings()->where('id', $parentPackagingId)->exists()) {
                $parentPackagingId = null;
            }

            if ($giftPackagingId) {
                $giftPackagingExists = ProductPackaging::where('id', $giftPackagingId)
                    ->where('product_id', $giftProductId)
                    ->exists();

                if (!$giftPackagingExists) {
                    $giftPackagingId = null;
                }
            }

            $giftData = [
                'parent_product_id' => $product->id,
                'parent_packaging_id' => $parentPackagingId,
                'gift_product_id' => $giftProductId,
                'gift_packaging_id' => $giftPackagingId,
                'price' => array_get($data, 'price', 0) ?: 0,
                'min_qty' => max(1, (int) array_get($data, 'min_qty', 1)),
                'gift_qty' => max(1, (int) array_get($data, 'gift_qty', 1)),
                'is_repeatable' => array_get($data, 'is_repeatable', 0) ? 1 : 0,
                'is_active' => array_get($data, 'is_active', 0) ? 1 : 0,
            ];

            $giftId = array_get($data, 'id');
            $gift = null;

            if ($giftId) {
                $gift = ProductGift::withTrashed()
                    ->where('parent_product_id', $product->id)
                    ->where('id', $giftId)
                    ->first();
            }

            if ($gift) {
                $gift->restore();
                $gift->update($giftData);
            } else {
                $gift = ProductGift::create($giftData);
            }

            self::syncGiftOptions($gift, array_get($data, 'options', []));
        }
    }

    private static function syncMirrorColors(Product $product, array $newColorIds)
    {

        $oldRelatedIds = $product->colorProducts()->pluck('color_product_id')->toArray();
        $allInvolvedIds = array_unique(array_merge([$product->id], $oldRelatedIds, $newColorIds));

        $finalGroup = array_unique(array_merge([$product->id], $newColorIds));

        foreach ($allInvolvedIds as $id) {
            $item = Product::withoutGlobalScopes()->find($id);
            if (!$item) continue;

            $item->withoutEvents(function () use ($item, $finalGroup) {
                if (in_array($item->id, $finalGroup)) {
                    $item->colorProducts()->sync(array_diff($finalGroup, [$item->id]));
                } else {
                    $item->colorProducts()->detach($finalGroup);
                }
            });
        }
    }

    private static function updateProductBundles(Product $product, array $bundles)
    {
        $idsInForm = collect($bundles)->pluck('id')->filter()->toArray();
        $product->bundles()->whereNotIn('id', $idsInForm)->delete();

        foreach ($bundles as $data) {
            $bundleProductId = array_get($data, 'bundle_product_id');
            if (!$bundleProductId) continue;

            $product->bundles()->updateOrCreate(
                ['id' => array_get($data, 'id')],
                [
                    'bundle_product_id' => $bundleProductId,
                    'product_qty' => array_get($data, 'product_qty', 1),
                    'product_price' => array_get($data, 'product_price'),
                    'special_price' => array_get($data, 'special_price'),
                    'special_price_type' => array_get($data, 'special_price_type'),
                    'bundle_qty' => array_get($data, 'bundle_qty', 1),
                    'bundle_price' => array_get($data, 'bundle_price'),
                    'special_bundle_price' => array_get($data, 'special_bundle_price'),
                    'special_bundle_price_type' => array_get($data, 'special_bundle_price_type'),
                ]
            );
        }
    }

    private static function updateProductVideos(array $attributes, Product $product): void
    {
        $videos = collect(array_get($attributes, 'videos', []))
            ->filter(function ($video) {
                return !empty($video['url']);
            })
            ->values();

        if ($videos->isEmpty()) {
            $product->videos()->delete();

            return;
        }

        $mainVideoIndex = array_get($attributes, 'main_video');

        $idsInForm = $videos
            ->pluck('id')
            ->filter()
            ->toArray();

        $product->videos()
            ->whereNotIn('id', $idsInForm)
            ->delete();

        $savedVideoIds = [];
        $mainVideoId = null;

        foreach ($videos as $index => $video) {
            $savedVideo = $product->videos()->updateOrCreate(
                ['id' => array_get($video, 'id')],
                [
                    'title' => array_get($video, 'title'),
                    'url' => array_get($video, 'url'),
                    'youtube_id' => ProductVideo::extractYoutubeId(array_get($video, 'url')),
                    'is_main' => false,
                    'sort_order' => array_get($video, 'sort_order', $index),
                ]
            );

            $savedVideoIds[] = $savedVideo->id;

            if ((string) $mainVideoIndex === (string) $index) {
                $mainVideoId = $savedVideo->id;
            }
        }

        if (!$mainVideoId) {
            $mainVideoId = $savedVideoIds[0] ?? null;
        }

        $product->videos()->update(['is_main' => false]);

        if ($mainVideoId) {
            $product->videos()
                ->where('id', $mainVideoId)
                ->update(['is_main' => true]);
        }
    }

    private static function syncGiftRules(Product $product, ?int $parentPackagingId, array $gifts): void
    {
        $idsInForm = collect($gifts)
            ->pluck('id')
            ->filter()
            ->toArray();

        $deleteQuery = ProductGift::where('parent_product_id', $product->id);

        if ($parentPackagingId) {
            $deleteQuery->where('parent_packaging_id', $parentPackagingId);
        } else {
            $deleteQuery->whereNull('parent_packaging_id');
        }

        if (empty($gifts)) {
            $deleteQuery->delete();
            return;
        }

        $deleteQuery->whereNotIn('id', $idsInForm)->delete();

        foreach ($gifts as $data) {
            $giftProductId = array_get($data, 'gift_product_id');

            if (!$giftProductId) {
                continue;
            }

            $giftData = [
                'parent_product_id' => $product->id,
                'parent_packaging_id' => $parentPackagingId,
                'gift_product_id' => $giftProductId,
                'gift_packaging_id' => array_get($data, 'gift_packaging_id') ?: null,
                'price' => array_get($data, 'price', 0) ?: 0,
                'min_qty' => max(1, (int) array_get($data, 'min_qty', 1)),
                'is_active' => array_get($data, 'is_active', 0) ? 1 : 0,
            ];

            $giftId = array_get($data, 'id');
            $gift = null;

            if ($giftId) {
                $gift = ProductGift::withTrashed()
                    ->where('parent_product_id', $product->id)
                    ->where('id', $giftId)
                    ->first();
            }

            if ($gift) {
                $gift->restore();
                $gift->update($giftData);
            } else {
                $gift = ProductGift::create($giftData);
            }

            self::syncGiftOptions($gift, array_get($data, 'options', []));
        }
    }

    private static function syncGiftOptions(ProductGift $gift, array $options): void
    {
        $options = collect($options)
            ->filter(fn ($value) => !is_null($value) && $value !== '')
            ->toArray();

        $optionIds = array_keys($options);

        if (empty($options)) {
            $gift->options()->delete();
            return;
        }

        $gift->options()
            ->whereNotIn('product_option_id', $optionIds)
            ->delete();

        foreach ($options as $productOptionId => $productOptionValueId) {
            $gift->options()->updateOrCreate(
                [
                    'product_option_id' => $productOptionId,
                ],
                [
                    'product_option_value_id' => $productOptionValueId,
                ]
            );
        }
    }
}
