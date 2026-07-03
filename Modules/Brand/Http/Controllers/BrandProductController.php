<?php

namespace Modules\Brand\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Brand\Entities\Brand;
use Modules\Product\Entities\Product;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\SeoFilter\Services\ContactLensBrandLanding;

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

    public function index(
        $slug,
        Product $model,
        ProductFilter $productFilter,
        ContactLensBrandLanding $contactLensBrandLanding
    ) {
        $brand = Brand::where('slug', $slug)->first();

        if (!$brand || !$brand->exists) {
            abort(404);
        }

        if ($contactLensBrandLanding->shouldRenderBrandAsContactLensCategory($brand, request())) {
            $category = $contactLensBrandLanding->category();

            $contactLensBrandLanding->applyRequestContext($brand, $category, request());

            if (request()->expectsJson()) {
                return $this->searchProducts($model, $productFilter);
            }

            $data = $this->searchProductsNonJSON($model, $productFilter);

            $data['category'] = $category;
            $data['breadcrumbs'] = $contactLensBrandLanding->breadcrumbs($category);

            /*
             * SEO берем от бренда, но шаблон и товары остаются категорийными.
             */
            $data['brandLandingSeo'] = $contactLensBrandLanding->seoData($brand);
            $data['brandLandingResetUrl'] = $contactLensBrandLanding->resetUrl($category);
            $data['brandLandingBaseFilters'] = $contactLensBrandLanding->baseFilters($brand);

            return view('storefront::public.categories.show', $data);
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

//    public function index($slug, Product $model, ProductFilter $productFilter)
//    {
//        $brand = Brand::where('slug', $slug)->first();
//
//        if (!$brand || !$brand->exists) {
//            abort(404);
//        }
//
//        request()->merge(['brand' => $slug]);
//
//        request()->request->remove('category');
//
//        if (request()->expectsJson()) {
//            return $this->searchProducts($model, $productFilter);
//        }
//
//        $data = $this->searchProductsNonJSON($model, $productFilter);
//        $data['brand'] = $brand;
//
//        return view('storefront::public.brands.show', $data);
//    }
}
