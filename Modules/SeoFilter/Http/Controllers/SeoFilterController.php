<?php

namespace Modules\SeoFilter\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\SeoFilter\Entities\SeoFilter;

class SeoFilterController
{
    use ProductSearch;

    public function show(int $id, Request $request, Product $model, ProductFilter $productFilter)
    {
        $seoFilter = SeoFilter::active()
            ->with('category')
            ->findOrFail($id);

        $this->applySeoFilterQuery($request, $seoFilter);

        $data = $this->searchProductsNonJSON($model, $productFilter);

        $data['seoFilter'] = $seoFilter;
        $data['seoFilterResetUrl'] = $this->resetUrl($seoFilter);

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
            return url($seoFilter->category->getFullPath());
        }

        return route('products.index');
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
}
