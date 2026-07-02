<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Events\ShowingProductList;
use Illuminate\Pagination\LengthAwarePaginator;

trait ProductSearch
{
    /**
     * Search products for the request.
     *
     * @param Product $model
     * @param ProductFilter $productFilter
     *
     * @return JsonResponse
     */
    public function searchProducts(Product $model, ProductFilter $productFilter)
    {
        $productIds = [];

        if (request()->filled('query')) {
            $model = $model->search(request('query'));
            $productIds = $model->keys();
        }

        $query = $model->filter($productFilter);
//
//        if (request()->filled('category')) {
//            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
//        }

        $products = $this->paginateDistinctProducts($query, (int) request('perPage', 30));

        event(new ShowingProductList($products));


        return response()->json([
            'products' => $products,
            'attributes' => $this->getAttributes($productIds),
        ]);
    }
    /**
     * Search products for the request.
     *
     * @param Product $model
     * @param ProductFilter $productFilter
     *
     * @return array
     */
    public function searchProductsNonJSON(Product $model, ProductFilter $productFilter)
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

        $products = $this->paginateDistinctProducts($query, (int) request('perPage', 15));

        event(new ShowingProductList($products));

        return [
            'products' => $products,
            'attributes' => $this->getAttributes($productIds),
        ];
    }


    private function getAttributes($productIds)
    {
        if (!request()->filled('category') || $this->filteringViaRootCategory()) {
            return collect();
        }

        return Attribute::with('values')
            ->where('is_filterable', true)
            ->whereHas('categories', function ($query) use ($productIds) {
                $query->whereIn('id', $this->getProductsCategoryIds($productIds));
            })
            ->get();
    }


    private function filteringViaRootCategory()
    {
        return Category::where('slug', request('category'))
            ->firstOrNew([])
            ->isRoot();
    }


    private function getProductsCategoryIds($productIds)
    {
        return DB::table('product_categories')
            ->whereIn('product_id', $productIds)
            ->distinct()
            ->pluck('category_id');
    }

    private function paginateDistinctProducts($query, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        /*
         * Считаем total явно по уникальным products.id.
         * Не доверяем paginator count на сложных фильтрах с distinct / whereExists.
         */
        $totalIdsQuery = (clone $query)
            ->select('products.id')
            ->distinct()
            ->reorder();

        $total = DB::query()
            ->fromSub($totalIdsQuery, 'filtered_products')
            ->count();

        /*
         * Для текущей страницы берем только ids.
         * Здесь порядок оставляем от оригинального query, чтобы sort работал.
         */
        $pageIds = (clone $query)
            ->select('products.id')
            ->distinct()
            ->forPage($page, $perPage)
            ->pluck('products.id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($pageIds->isEmpty()) {
            return new LengthAwarePaginator(
                collect(),
                $total,
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'query' => request()->query(),
                ]
            );
        }

        $products = (clone $query)
            ->whereIn('products.id', $pageIds)
            ->select('products.*')
            ->distinct()
            ->get()
            ->sortBy(fn ($product) => $pageIds->search((int) $product->id))
            ->values();

        return new LengthAwarePaginator(
            $products,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }
}
