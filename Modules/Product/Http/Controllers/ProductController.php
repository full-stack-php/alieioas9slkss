<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Category\Entities\Category;
use Modules\Product\Events\ShowingProductList;
use Modules\Review\Entities\Review;
use Illuminate\Contracts\View\View;
use Modules\Product\Entities\Product;
use Illuminate\Contracts\View\Factory;
use Modules\Product\Events\ProductViewed;
use Modules\Product\Filters\ProductFilter;
use Illuminate\Contracts\Foundation\Application;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Http\Middleware\SetProductSortOption;

class ProductController extends Controller
{
    use ProductSearch;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(SetProductSortOption::class)->only('index');
    }


    /**
     * Display a listing of the resource.
     *
     * @param Product $model
     * @param ProductFilter $productFilter
     *
     * @return JsonResponse|Application|Factory|View
     */
    public function index(Product $model, ProductFilter $productFilter)
    {
        $productIds = [];

        if (request()->filled('query')) {
            $model = $model->search(request('query'));
            $productIds = $model->keys();
        }

        $query = $model->filter($productFilter);

        if (request()->filled('category')) {
            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
        }

        $products = $query->paginate(request('perPage', 15));

        event(new ShowingProductList($products));

        return view('storefront::public.products.index', compact('products'));
    }


    /**
     * Show the specified resource.
     *
     * @param string $slug
     *
     * @return Response
     */
    public function show($slug)
    {
        $product = ProductRepository::findBySlug($slug);
        $relatedProducts = $product->relatedProducts()->forCard()->get();
        $colorProducts = $product->colorProducts()->forCard()->get();
        $review = $this->getReviewData($product);
        $product->append([
            'is_in_flash_sale',
        ]);

        $breadcrumbs = [];

        if(!is_null($product->main_category_id)){
            $category = Category::find($product->main_category_id);
            $breadcrumbs = $this->parseBreadcrumbs($category);
        }


        event(new ProductViewed($product));

        return view('storefront::public.products.show', compact('product', 'relatedProducts', 'colorProducts', 'review', 'breadcrumbs'));
    }


    private function getReviewData(Product $product)
    {
        if (!setting('reviews_enabled')) {
            return null;
        }

        return Review::countAndAvgRating($product);
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
