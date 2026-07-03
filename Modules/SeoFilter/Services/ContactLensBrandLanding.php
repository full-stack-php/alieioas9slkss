<?php

namespace Modules\SeoFilter\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\ManufacturerFilterCodec;

class ContactLensBrandLanding
{
    /*
     * Временная логика только для категории “Контактные линзы”.
     * ID берем из админки. На скрине это ID 429.
     * Когда логика больше не нужна — удаляем этот класс и подключения.
     */
    private const CONTACT_LENSES_CATEGORY_ID = 429;

    private array $ignoredKeys = [
        'page',
        'perPage',
        'sort',
        'query',
        'search',
    ];

    public function category(): ?Category
    {
        return Category::with(['files', 'parent', 'children'])
            ->find(self::CONTACT_LENSES_CATEGORY_ID);
    }

    public function shouldRedirectCategoryToBrand(Category $category, Request $request): bool
    {
        return $this->isContactLensCategory($category)
            && $this->brandFromManufacturerOnlyRequest($request) !== null;
    }

    public function brandFromManufacturerOnlyRequest(Request $request): ?Brand
    {
        if (!$this->hasOnlyManufacturerFilter($request)) {
            return null;
        }

        $manufacturerIds = ManufacturerFilterCodec::normalize(
            $request->query('manufacturers', $request->query('manufacturer'))
        );

        if (count($manufacturerIds) !== 1) {
            return null;
        }

        return Brand::find($manufacturerIds[0]);
    }

    public function shouldRenderBrandAsContactLensCategory(Brand $brand, Request $request): bool
    {
        if ($this->hasFilterQuery($request)) {
            return false;
        }

        $category = $this->category();

        if (!$category || !$category->exists) {
            return false;
        }

        return $this->brandHasProductsInCategory($brand, $category);
    }

    public function applyRequestContext(Brand $brand, Category $category, Request $request): void
    {
        $manufacturerToken = ManufacturerFilterCodec::encode([(int) $brand->id]);

        /*
         * ProductFilter и ProductFilterComposer читают request()->query(),
         * поэтому ставим значения именно в query bag.
         */
        $request->query->set('category', $category->slug);
        $request->query->set('manufacturers', $manufacturerToken);

        $request->merge([
            'category' => $category->slug,
            'manufacturers' => $manufacturerToken,
        ]);
    }

    public function brandUrl(Brand $brand): string
    {
        return LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            route('brands.products.index', $brand->slug)
        );
    }

    public function resetUrl(Category $category): string
    {
        return LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            url($category->getFullPath())
        );
    }

    public function baseFilters(Brand $brand): array
    {
        return [
            'attribute' => '',
            'manufacturers' => ManufacturerFilterCodec::encode([(int) $brand->id]),
            'has_discount' => '',
            'price_min' => '',
            'price_max' => '',
        ];
    }

    public function seoData(Brand $brand): array
    {
        return [
            'meta_title' => $brand->meta->meta_title ?: $brand->name,
            'meta_description' => $brand->meta->meta_description ?: '',
            'h1' => $brand->h1_name ?: $brand->name,
            'description' => $brand->description,
            'image' => $brand->logo->path,
        ];
    }

    public function breadcrumbs(Category $category)
    {
        $crumbs = collect();
        $current = $category;

        while ($current) {
            $crumbs->prepend($current);
            $current = $current->parent;
        }

        return $crumbs;
    }

    private function hasOnlyManufacturerFilter(Request $request): bool
    {
        $query = Arr::except($request->query(), $this->ignoredKeys);

        $hasManufacturer = array_key_exists('manufacturers', $query)
            || array_key_exists('manufacturer', $query);

        if (!$hasManufacturer) {
            return false;
        }

        foreach (Arr::except($query, ['manufacturers', 'manufacturer']) as $value) {
            if ($value !== null && $value !== '' && $value !== []) {
                return false;
            }
        }

        return true;
    }

    private function hasFilterQuery(Request $request): bool
    {
        $query = Arr::except($request->query(), $this->ignoredKeys);

        foreach ($query as $value) {
            if ($value !== null && $value !== '' && $value !== []) {
                return true;
            }
        }

        return false;
    }

    private function isContactLensCategory(Category $category): bool
    {
        return $category->exists
            && (int) $category->id === self::CONTACT_LENSES_CATEGORY_ID;
    }

    private function brandHasProductsInCategory(Brand $brand, Category $category): bool
    {
        $categoryIds = app(CategoryDescendantIds::class)->forCategory($category);

        return Product::query()
            ->where(function ($query) use ($brand) {
                $query
                    ->where('products.brand_id', $brand->id)
                    ->orWhere('products.manufacturer_id', $brand->id);
            })
            ->where(function ($query) use ($categoryIds) {
                $query
                    ->whereIn('products.main_category_id', $categoryIds)
                    ->orWhereHas('categories', function ($categoryQuery) use ($categoryIds) {
                        $categoryQuery->whereIn('categories.id', $categoryIds);
                    });
            })
            ->exists();
    }
}
