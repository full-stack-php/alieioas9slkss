<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\SeoFilter\Services\SeoRobots;
use Modules\SeoFilter\Services\SeoFilterMatcher;

class CategoryProductController
{
    use ProductSearch;

    /**
     * Display a listing of the resource.
     *
     * @param string $slug
     * @param Product $model
     * @param ProductFilter $productFilter
     *
     * @return Response
     */
    public function index(
        $slug,
        Product $model,
        ProductFilter $productFilter,
        SeoFilterMatcher $seoFilterMatcher,
        SeoRobots $seoRobots
    ) {
        request()->merge(['category' => $slug]);

        $category = Category::findBySlug($slug);

        $seoFilter = $seoFilterMatcher->findByRequest($category);

        if ($seoFilter) {
            return redirect()->to($seoFilter->url(), 302);
        }

        if (request()->expectsJson()) {
            return $this->searchProducts($model, $productFilter);
        }

        $data = $this->searchProductsNonJSON($model, $productFilter);
        $data['category'] = $category;
        $data['breadcrumbs'] = $this->parseBreadcrumbs($data['category']);

        $data['robotsMeta'] = $seoRobots->forQuery(request()->query());

        return view('storefront::public.categories.show', $data);
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
