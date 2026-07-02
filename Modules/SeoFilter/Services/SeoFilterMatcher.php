<?php

namespace Modules\SeoFilter\Services;

use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Product\Filters\AttributeFilterCodec;
use Modules\Product\Filters\ManufacturerFilterCodec;
use Modules\SeoFilter\Entities\SeoFilter;

class SeoFilterMatcher
{
    public function findByRequest(?Category $category = null): ?SeoFilter
    {
        $currentQuery = $this->canonicalFromArray(request()->query());

        if ($currentQuery === '') {
            return null;
        }

        $query = SeoFilter::active()->with('category');

        if ($category && $category->exists) {
            $query->where('category_id', $category->id);
        } else {
            $query->whereNull('category_id');
        }

        return $query
            ->get()
            ->first(function (SeoFilter $seoFilter) use ($currentQuery) {
                return $this->canonicalFromString($seoFilter->query_string) === $currentQuery;
            });
    }

    public function canonicalFromString(?string $queryString): string
    {
        parse_str((string) $queryString, $query);

        return $this->canonicalFromArray($query);
    }

    public function canonicalFromArray(array $query): string
    {
        $query = Arr::only($query, [
            'price',
            'fromPrice',
            'toPrice',
            'manufacturers',
            'manufacturer',
            'attribute',
            'attributes',
            'has_discount',
            'specials',
        ]);

        $normalized = [];

        $attribute = $query['attribute'] ?? $query['attributes'] ?? null;

        if ($attribute) {
            $attributeToken = AttributeFilterCodec::encode(
                AttributeFilterCodec::normalize($attribute)
            );

            if ($attributeToken !== '') {
                $normalized['attribute'] = $attributeToken;
            }
        }

        $manufacturers = $query['manufacturers'] ?? $query['manufacturer'] ?? null;

        if ($manufacturers) {
            $manufacturerToken = ManufacturerFilterCodec::encode(
                ManufacturerFilterCodec::normalize($manufacturers)
            );

            if ($manufacturerToken !== '') {
                $normalized['manufacturers'] = $manufacturerToken;
            }
        }

        $discount = $query['has_discount'] ?? $query['specials'] ?? null;

        if ($this->truthy($discount)) {
            $normalized['has_discount'] = '1';
        }

        $price = [];

        if (isset($query['price']) && is_array($query['price'])) {
            if (isset($query['price']['min']) && $query['price']['min'] !== '') {
                $price['min'] = $query['price']['min'];
            }

            if (isset($query['price']['max']) && $query['price']['max'] !== '') {
                $price['max'] = $query['price']['max'];
            }
        }

        if (isset($query['fromPrice']) && $query['fromPrice'] !== '') {
            $price['min'] = $query['fromPrice'];
        }

        if (isset($query['toPrice']) && $query['toPrice'] !== '') {
            $price['max'] = $query['toPrice'];
        }

        if (!empty($price)) {
            ksort($price);
            $normalized['price'] = $price;
        }

        ksort($normalized);

        return http_build_query($normalized, '', '&', PHP_QUERY_RFC3986);
    }

    private function truthy($value): bool
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return in_array($value, [1, '1', true, 'true', 'on', 'yes'], true);
    }
}
