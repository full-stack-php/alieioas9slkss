<?php

namespace Modules\SeoFilter\Http\Controllers;

use Illuminate\Support\Arr;
use Modules\Product\Filters\AttributeFilterCodec;
use Modules\Product\Filters\ManufacturerFilterCodec;
use Illuminate\Http\Request;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\SeoFilter\Entities\SeoFilter;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\SeoFilter\Services\SeoRobots;
class SeoFilterController
{
    use ProductSearch;

    public function show(
        int $id,
        Request $request,
        Product $model,
        ProductFilter $productFilter,
        SeoRobots $seoRobots
    )
    {


        $seoFilter = SeoFilter::active()
            ->with('category')
            ->findOrFail($id);

        $robotsMeta = $seoRobots->forQuery($request->query->all());

        $this->applySeoFilterQuery($request, $seoFilter);

        $data = $this->searchProductsNonJSON($model, $productFilter);

        $data['seoFilter'] = $seoFilter;
        $data['seoFilterResetUrl'] = $this->resetUrl($seoFilter);
        $data['seoFilterBaseFilters'] = $this->seoFilterBaseFilters($seoFilter);
        $data['robotsMeta'] = $robotsMeta;

        if ($seoFilter->category_id && $seoFilter->category->exists) {
            $data['category'] = $seoFilter->category;
            $data['breadcrumbs'] = $this->parseBreadcrumbs($seoFilter->category);

            return view('storefront::public.categories.show', $data);
        }

        return view('storefront::public.products.index', $data);
    }

    private function applySeoFilterQuery(Request $request, SeoFilter $seoFilter): void
    {
        parse_str($seoFilter->query_string, $seoQuery);

        if ($seoFilter->category_id && $seoFilter->category->exists) {
            $seoQuery['category'] = $seoFilter->category->slug;
        }

        $request->query->replace(
            array_replace_recursive(
                $seoQuery,
                $request->query->all()
            )
        );
    }

    private function resetUrl(SeoFilter $seoFilter): string
    {
        if ($seoFilter->category_id && $seoFilter->category->exists) {
            return LaravelLocalization::getLocalizedURL(
                LaravelLocalization::getCurrentLocale(),
                url($seoFilter->category->getFullPath())
            );
        }

        return LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            route('products.index')
        );
    }

    private function parseBreadcrumbs($category)
    {
        $crumbs = collect();
        $current = $category;

        while ($current) {
            $crumbs->prepend($current);
            $current = $current->parent;
        }

        return $crumbs;
    }

    private function seoFilterBaseFilters(SeoFilter $seoFilter): array
    {
        parse_str((string) $seoFilter->query_string, $query);

        $attribute = $query['attribute'] ?? $query['attributes'] ?? null;
        $manufacturers = $query['manufacturers'] ?? $query['manufacturer'] ?? null;

        $price = is_array($query['price'] ?? null) ? $query['price'] : [];

        return [
            'attribute' => AttributeFilterCodec::encode(
                AttributeFilterCodec::normalize($attribute)
            ),

            'manufacturers' => ManufacturerFilterCodec::encode(
                ManufacturerFilterCodec::normalize($manufacturers)
            ),

            'has_discount' => $this->truthy($query['has_discount'] ?? $query['specials'] ?? null) ? '1' : '',

            'price_min' => Arr::get($price, 'min', $query['fromPrice'] ?? ''),
            'price_max' => Arr::get($price, 'max', $query['toPrice'] ?? ''),
        ];
    }

    private function truthy($value): bool
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return in_array($value, [1, '1', true, 'true', 'on', 'yes'], true);
    }
}
