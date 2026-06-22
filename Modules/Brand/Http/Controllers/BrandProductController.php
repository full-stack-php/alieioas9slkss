<?php

namespace Modules\Brand\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Brand\Entities\Brand;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;

class BrandProductController
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
        $brand = Brand::where('slug', $slug)->first();

        if (!$brand || !$brand->exists) {
            abort(404);
        }

        request()->merge(['brand' => $slug]);

        request()->request->remove('category');

        if (request()->expectsJson()) {
            return $this->searchProducts($model, $productFilter);
        }

        $data = $this->searchProductsNonJSON($model, $productFilter);
        $data['brand'] = $brand;

        return view('storefront::public.brands.show', $data);
    }
}
