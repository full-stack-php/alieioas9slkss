<?php

namespace Modules\Storefront\Http\ViewComposers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Attribute\Entities\Attribute;
use Modules\Brand\Entities\Brand;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\AttributeFilterCodec;
use Modules\Product\Filters\ManufacturerFilterCodec;
use Modules\Product\Filters\QueryStringFilter;
use Modules\Support\Money;

class ProductFilterComposer
{
    private QueryStringFilter $queryStringFilter;

    public function __construct(QueryStringFilter $queryStringFilter)
    {
        $this->queryStringFilter = $queryStringFilter;
    }

    public function compose(View $view): void
    {
        $attributes = $this->attributes($view);
        $selectedAttributeFilters = AttributeFilterCodec::normalize(request('attribute'));

        $this->appendAttributeSelections($attributes, $selectedAttributeFilters);
        $this->appendAttributeBaseCounts($attributes);
        $this->appendAttributeCounts($attributes);

        $attributes = $this->filterVisibleAttributes($attributes);

        $manufacturers = $this->manufacturers();
        $this->appendManufacturerSelections($manufacturers);

        $view->with([
            'attributes' => $attributes,
            'filterManufacturers' => $manufacturers,
            'filterPrice' => $this->priceRange(),
            'filterDiscountCount' => $this->discountCount(),
            'filterDiscountSelected' => request()->has('has_discount') || request()->has('specials'),
            'filterHasActiveFilters' => request()->hasAny([
                'price',
                'manufacturers',
                'manufacturer',
                'attribute',
                'attributes',
                'has_discount',
                'specials',
            ]),
        ]);
    }

    private function manufacturers()
    {
        $baseQuery = $this->queryForFilters($this->baseContextFilters());

        $brandIds = (clone $baseQuery)
            ->whereNotNull('products.brand_id')
            ->distinct()
            ->pluck('products.brand_id');

        $manufacturerIds = (clone $baseQuery)
            ->whereNotNull('products.manufacturer_id')
            ->distinct()
            ->pluck('products.manufacturer_id');

        $ids = $brandIds
            ->merge($manufacturerIds)
            ->merge($this->selectedManufacturerIds())
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Brand::whereIn('id', $ids)
            ->get()
            ->map(function ($manufacturer) {
                $manufacturer->filter_count = $this->manufacturerCount((int) $manufacturer->id);

                return $manufacturer;
            })
            ->values();
    }

    private function manufacturerCount(int $manufacturerId): int
    {
        $filters = request()->query();

        $manufacturerIds = ManufacturerFilterCodec::normalize(
            $filters['manufacturers'] ?? $filters['manufacturer'] ?? null
        );

        unset($filters['manufacturer']);

        if (!in_array($manufacturerId, $manufacturerIds, true)) {
            $manufacturerIds[] = $manufacturerId;
        }

        $manufacturerToken = ManufacturerFilterCodec::encode($manufacturerIds);

        if ($manufacturerToken === '') {
            unset($filters['manufacturers']);
        } else {
            $filters['manufacturers'] = $manufacturerToken;
        }

        return $this->countProducts(
            $this->queryForFilters($filters)
        );
    }

    private function discountCount(): int
    {
        $filters = $this->filtersExcept(['has_discount', 'specials']);
        $filters['has_discount'] = 1;

        return $this->countProducts($this->queryForFilters($filters));
    }

    private function attributes(View $view)
    {
        $viewAttributes = collect($view->getData()['attributes'] ?? []);

        if ($viewAttributes->isNotEmpty()) {
            return $viewAttributes;
        }

        $baseQuery = $this->queryForFilters(
            $this->filtersExcept([
                'price',
                'manufacturers',
                'manufacturer',
                'attribute',
                'attributes',
                'has_discount',
                'specials',
            ])
        );

        $productIds = (clone $baseQuery)
            ->select('products.id')
            ->distinct()
            ->pluck('products.id');

        if ($productIds->isEmpty()) {
            return collect();
        }

        $categoryIds = DB::table('product_categories')
            ->whereIn('product_id', $productIds)
            ->distinct()
            ->pluck('category_id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Attribute::with('values')
            ->where('is_filterable', true)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->get();
    }

    private function appendAttributeCounts($attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->values as $value) {
                $value->filter_count = $this->attributeValueCount(
                    (int) $attribute->id,
                    (int) $value->id,
                    $attribute->slug
                );
            }
        }
    }

    private function attributeValueCount(int $attributeId, int $valueId, ?string $attributeSlug = null): int
    {
        $filters = request()->query();

        $groups = AttributeFilterCodec::normalize(
            $filters['attribute'] ?? $filters['attributes'] ?? null
        );

        unset($filters['attributes']);

        $selectedValueIds = collect($groups[$attributeId] ?? [])
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        /*
         * Если значение еще не выбрано — считаем результат,
         * как если бы пользователь его добавил.
         *
         * Например:
         * сейчас: Объем = 10 ml
         * считаем 15 ml:
         * Объем = 10 ml OR 15 ml
         */
        if (!$selectedValueIds->contains($valueId)) {
            $selectedValueIds->push($valueId);
        }

        $selectedValueIds = $selectedValueIds
            ->unique()
            ->sort()
            ->values();

        if ($selectedValueIds->isEmpty()) {
            unset($groups[$attributeId]);
        } else {
            $groups[$attributeId] = $selectedValueIds->all();
        }

        $attributeToken = AttributeFilterCodec::encode($groups);

        if ($attributeToken === '') {
            unset($filters['attribute']);
        } else {
            $filters['attribute'] = $attributeToken;
        }

        return $this->countProducts(
            $this->queryForFilters($filters)
        );
    }

    private function priceRange(): array
    {
        $baseQuery = $this->queryForFilters(
            $this->filtersExcept(['price'])
        );

        $productMin = (clone $baseQuery)->min('products.selling_price');
        $productMax = (clone $baseQuery)->max('products.selling_price');

        $packagingExpression = "
            (
                CASE
                    WHEN pp.special_price IS NOT NULL
                         AND pp.special_price > 0
                         AND pp.special_price_type = 'percent'
                    THEN pp.price - (pp.price * pp.special_price / 100)

                    WHEN pp.special_price IS NOT NULL
                         AND pp.special_price > 0
                         AND pp.special_price < pp.price
                    THEN pp.special_price

                    ELSE pp.price
                END
            ) * COALESCE(pp.qty, 1)
        ";

        $idsQuery = (clone $baseQuery)
            ->select('products.id')
            ->distinct();

        $packagingRange = DB::table('product_packagings as pp')
            ->joinSub($idsQuery, 'filtered_products', function ($join) {
                $join->on('filtered_products.id', '=', 'pp.product_id');
            })
            ->where('pp.is_active', true)
            ->whereNull('pp.deleted_at')
            ->selectRaw("MIN({$packagingExpression}) as min_price")
            ->selectRaw("MAX({$packagingExpression}) as max_price")
            ->first();

        $min = collect([$productMin, $packagingRange->min_price ?? null])
            ->filter(fn ($value) => $value !== null)
            ->min();

        $max = collect([$productMax, $packagingRange->max_price ?? null])
            ->filter(fn ($value) => $value !== null)
            ->max();

        $min = Money::inDefaultCurrency($min ?: 0)
            ->convertToCurrentCurrency()
            ->floor()
            ->amount();

        $max = Money::inDefaultCurrency($max ?: 0)
            ->convertToCurrentCurrency()
            ->ceil()
            ->amount();

        return [
            'min' => $min,
            'max' => $max,
            'start_min' => request('price.min', $min),
            'start_max' => request('price.max', $max),
        ];
    }

    private function queryForFilters(array $filters): Builder
    {
        $query = Product::query();

        if (request()->filled('query')) {
            $keys = Product::search(request('query'))->keys();

            $query->whereIn('products.id', $keys);
        }

        $filters = Arr::except($filters, [
            'page',
            'perPage',
            'query',
            'search',
        ]);

        foreach ($filters as $name => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $method = $this->methodForFilter($name);

            if ($method) {
                $this->queryStringFilter->{$method}($query, $value);
            }
        }

        return $query;
    }

    private function countProducts(Builder $query): int
    {
        return (int) (clone $query)
            ->distinct('products.id')
            ->count('products.id');
    }

    private function filtersExcept(array $except): array
    {
        $filters = request()->query();

        foreach ($except as $key) {
            unset($filters[$key]);
        }

        return $filters;
    }

    private function filtersExceptAttribute(int $attributeId, ?string $attributeSlug = null): array
    {
        return AttributeFilterCodec::withoutAttribute(
            request()->query(),
            $attributeId,
            $attributeSlug
        );
    }

    private function methodForFilter(string $filter): ?string
    {
        foreach ([$filter, Str::camel($filter)] as $method) {
            if (
                method_exists($this->queryStringFilter, $method)
                && is_callable([$this->queryStringFilter, $method])
            ) {
                return $method;
            }
        }

        return null;
    }

    private function appendAttributeSelections($attributes, array $selectedAttributeFilters): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->values as $value) {
                $value->is_filter_selected = in_array(
                    (int) $value->id,
                    $selectedAttributeFilters[(int) $attribute->id] ?? [],
                    true
                );
            }
        }
    }

    private function appendManufacturerSelections($manufacturers): void
    {
        $selectedManufacturerIds = $this->selectedManufacturerIds();

        foreach ($manufacturers as $manufacturer) {
            $manufacturer->is_filter_selected = in_array(
                (int) $manufacturer->id,
                $selectedManufacturerIds,
                true
            );
        }
    }

    private function baseContextFilters(): array
    {
        return Arr::except(request()->query(), [
            'price',
            'manufacturers',
            'manufacturer',
            'attribute',
            'attributes',
            'has_discount',
            'specials',
            'page',
            'perPage',
        ]);
    }

    private function selectedManufacturerIds(): array
    {
        return ManufacturerFilterCodec::normalize(
            request('manufacturers', request('manufacturer', []))
        );
    }

    private function appendAttributeBaseCounts($attributes): void
    {
        foreach ($attributes as $attribute) {
            foreach ($attribute->values as $value) {
                $value->base_filter_count = $this->attributeValueBaseCount(
                    (int) $attribute->id,
                    (int) $value->id
                );

                $value->is_filter_visible = $value->base_filter_count > 0
                    || ($value->is_filter_selected ?? false);
            }
        }
    }

    private function attributeValueBaseCount(int $attributeId, int $valueId): int
    {
        $filters = $this->baseContextFilters();

        $filters['attribute'] = AttributeFilterCodec::encode([
            $attributeId => [$valueId],
        ]);

        return $this->countProducts(
            $this->queryForFilters($filters)
        );
    }

    private function filterVisibleAttributes($attributes)
    {
        return $attributes
            ->map(function ($attribute) {
                $attribute->setRelation(
                    'values',
                    $attribute->values
                        ->filter(fn ($value) => (bool) ($value->is_filter_visible ?? false))
                        ->values()
                );

                return $attribute;
            })
            ->filter(fn ($attribute) => $attribute->values->isNotEmpty())
            ->values();
    }
}
