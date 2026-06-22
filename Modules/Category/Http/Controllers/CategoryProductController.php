<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;

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
    public function index($slug, Product $model, ProductFilter $productFilter)
    {
        request()->merge(['category' => $slug]);


        if (request()->expectsJson()) {
            return $this->searchProducts($model, $productFilter);
        }

        $data = $this->searchProductsNonJSON($model, $productFilter);
        $data['category'] = Category::findBySlug($slug);
        $data['breadcrumbs'] = $this->parseBreadcrumbs($data['category']);

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
