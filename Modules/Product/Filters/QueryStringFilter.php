<?php

namespace Modules\Product\Filters;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;
use Modules\Category\Entities\Category;
use Modules\Support\Money;

class QueryStringFilter
{
    private array $sorts = [
        'relevance',
        'alphabetic',
        'toprated',
        'latest',
        'pricelowtohigh',
        'pricehightolow',
    ];

    private array $groupColumns = [
        'products.id',
        'slug',
        'price',
        'selling_price',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'in_stock',
        'manage_stock',
        'qty',
        'new_from',
        'new_to',
    ];

    public function sort($query, $sortType): void
    {
        if ($this->sortTypeExists($sortType)) {
            $this->{$sortType}($query);
        }
    }

    public function relevance(): void
    {
        //
    }

    public function alphabetic($query): void
    {
        $query->join('product_translations', function (JoinClause $join) {
            $join->on('products.id', '=', 'product_translations.product_id');
        })
            ->groupBy(array_merge($this->groupColumns, ['product_translations.name']))
            ->orderBy('product_translations.name');
    }

    public function topRated($query): void
    {
        $query->selectRaw('AVG(reviews.rating) as avg_rating')
            ->leftJoin('reviews', function (JoinClause $join) {
                $join->on('products.id', '=', 'reviews.product_id');
                $join->on('reviews.is_approved', '=', DB::raw('1'));
            })
            ->groupBy($this->groupColumns)
            ->orderByDesc('avg_rating');
    }

    public function latest($query): void
    {
        $query->latest();
    }

    public function priceLowToHigh($query): void
    {
        $query->orderBy('selling_price');
    }

    public function priceHighToLow($query): void
    {
        $query->orderByDesc('selling_price');
    }

    /**
     * Новый формат:
     * price[min]=100&price[max]=1000
     */
    public function price($query, array $range): void
    {
        $from = $range['min'] ?? $range['from'] ?? null;
        $to = $range['max'] ?? $range['to'] ?? null;

        if ($from !== null && $from !== '') {
            $this->fromPrice($query, $from);
        }

        if ($to !== null && $to !== '') {
            $this->toPrice($query, $to);
        }
    }

    /**
     * Старый формат оставляем:
     * fromPrice=100
     */
    public function fromPrice($query, $price): void
    {
        $this->whereEffectivePrice($query, '>=', $this->convertPrice($price));
    }

    /**
     * Старый формат оставляем:
     * toPrice=1000
     */
    public function toPrice($query, $price): void
    {
        $this->whereEffectivePrice($query, '<=', $this->convertPrice($price));
    }

    /**
     * Старый бренд по slug оставляем, чтобы ничего не сломать.
     */
    public function brand($query, $value): void
    {
        if (is_array($value)) {
            $this->manufacturers($query, $value);
            return;
        }

        if (is_numeric($value)) {
            $query->where('products.manufacturer_id', (int) $value);
            return;
        }

        $query->whereHas('manufacturer', function ($brandQuery) use ($value) {
            $brandQuery->where('slug', $value);
        });
    }

    public function manufacturers($query, $manufacturerIds): void
    {
        $manufacturerIds = ManufacturerFilterCodec::normalize($manufacturerIds);

        if (empty($manufacturerIds)) {
            return;
        }

        $query->where(function ($query) use ($manufacturerIds) {
            $query
                ->whereIn('products.brand_id', $manufacturerIds)
                ->orWhereIn('products.manufacturer_id', $manufacturerIds);
        });
    }

    public function manufacturer($query, $manufacturerIds): void
    {
        $this->manufacturers($query, $manufacturerIds);
    }


    public function category($query, $slug): void
    {
        $categoryId = Category::where('slug', $slug)->value('id');

        if (!$categoryId) {
            return;
        }

        $query->where(function ($categoryQuery) use ($slug, $categoryId) {
            $categoryQuery->where('products.main_category_id', $categoryId)
                ->orWhereHas('categories', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
        });
    }

    public function attribute($query, $attributeFilters): void
    {
        $groups = AttributeFilterCodec::normalize($attributeFilters);

        foreach ($groups as $index => $value) {
            $attributeId = (int) $index;
            $valueIds = collect((array) $value)
                ->filter(fn ($id) => is_numeric($id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($attributeId < 1 || $valueIds->isEmpty()) {
                continue;
            }

            $alias = "pa_{$attributeId}";

            $query->join("product_attributes as {$alias}", 'products.id', '=', "{$alias}.product_id")
                ->where("{$alias}.attribute_id", $attributeId)
                ->whereExists(function ($subQuery) use ($alias, $valueIds) {
                    $subQuery->selectRaw(1)
                        ->from('product_attribute_values')
                        ->whereColumn("{$alias}.id", 'product_attribute_values.product_attribute_id')
                        ->whereIn('product_attribute_values.attribute_value_id', $valueIds->all());
                });
        }
    }

    public function attributes($query, $attributeFilters): void
    {
        $this->attribute($query, $attributeFilters);
    }


    /**
     * Только товары со скидкой:
     * has_discount=1
     */
    public function hasDiscount($query, $value): void
    {
        if (!$this->truthy($value)) {
            return;
        }

        $query->where(function ($q) {
            $this->whereActiveProductSpecial($q);

            $q->orWhereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('product_packagings as pp')
                    ->whereColumn('pp.product_id', 'products.id')
                    ->where('pp.is_active', true)
                    ->whereNull('pp.deleted_at')
                    ->whereNotNull('pp.special_price')
                    ->where('pp.special_price', '>', 0);
            });
        });
    }

    public function specials($query, $value): void
    {
        $this->hasDiscount($query, $value);
    }

    /**
     * Цена считается как:
     * - products.selling_price
     * - product_packagings.price * qty
     * - product_packagings.special_price * qty
     * - product_packagings special percent * qty
     */
    private function whereEffectivePrice($query, string $operator, int|float $price): void
    {
        $packagingPriceExpression = "
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

        $query->where(function ($productQuery) use ($operator, $price, $packagingPriceExpression) {
            $productQuery->where('products.selling_price', $operator, $price)
                ->orWhereExists(function ($subQuery) use ($operator, $price, $packagingPriceExpression) {
                    $subQuery->select(DB::raw(1))
                        ->from('product_packagings as pp')
                        ->whereColumn('pp.product_id', 'products.id')
                        ->where('pp.is_active', true)
                        ->whereNull('pp.deleted_at')
                        ->whereRaw("{$packagingPriceExpression} {$operator} ?", [$price]);
                });
        });
    }

    private function whereActiveProductSpecial($query): void
    {
        $query->where(function ($specialQuery) {
            $specialQuery
                ->whereNotNull('products.special_price')
                ->where('products.special_price', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('products.special_price_start')
                        ->orWhereDate('products.special_price_start', '<=', today());
                })
                ->where(function ($q) {
                    $q->whereNull('products.special_price_end')
                        ->orWhereDate('products.special_price_end', '>=', today());
                });
        });
    }

    private function sortTypeExists($sortType): bool
    {
        return in_array(strtolower($sortType), $this->sorts);
    }

    private function convertPrice($price): int|float
    {
        return Money::inCurrentCurrency($price)->convertToDefaultCurrency()->amount();
    }

    private function getAttributeIds($attributeFilters)
    {
        return Attribute::whereIn('slug', array_keys((array) $attributeFilters))->pluck('id');
    }

    private function getAttributeValueIds($attributeFilters): string
    {
        return once(function () use ($attributeFilters) {
            return AttributeValue::whereTranslationIn('value', array_flatten((array) $attributeFilters))
                ->pluck('id')
                ->implode(',') ?: 'null';
        });
    }

    private function integerArray($value): array
    {
        return collect((array) $value)
            ->flatten()
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function truthy($value): bool
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
