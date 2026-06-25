<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Product\Entities\ProductPackaging;
use Modules\Product\Http\Requests\SaveProductRequest;

class ProductController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected string $model = Product::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected string $label = 'product::products.product';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected string $viewPath = 'product::admin.products';

    /**
     * Form requests for the resource.
     *
     * @var array|string
     */
    protected string|array $validation = SaveProductRequest::class;



    public function index(Request $request)
    {
        if ($request->has('table')) {
            return $this->getModel()->table($request);
        }

        $statuses = [
            '' => trans('admin::admin.form.please_select'),
            '0' => trans('admin::admin.table.inactive'),
            '1' => trans('admin::admin.table.active'),
        ];

        if ($request->has('query')) {
            return $this->getModel()
                ->search($request->get('query'))
                ->query()
                ->limit($request->get('limit', 10))
                ->get();
        }


        return view("{$this->viewPath}.index", [
            'categories' => collect(Category::treeList())->prepend(trans('admin::admin.form.please_select'), ''),
            'brands' => collect(Brand::list())->prepend(trans('admin::admin.form.please_select'), ''),
            'statuses' => $statuses,

            'selectedFilters' => [
                'page_filter_category_id' => $request->query('page_filter_category_id'),
                'page_filter_brand_id' => $request->query('page_filter_brand_id'),
                'page_filter_is_active' => $request->query('page_filter_is_active'),
            ],
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $products = Product::withoutGlobalScope('active')
            ->whereTranslationLike('name', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->limit(15)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name,
                'price' => $product->price->amount(),
            ];
        });

        return response()->json($results);
    }

    public function update($id)
    {
        $entity = $this->getEntity($id);

        $this->disableSearchSyncing();

        $entity->update(
            $this->getRequest('update')->except(array_keys(request()->query()))
        );

        $entity->withoutEvents(function () use ($entity) {
            $entity->touch();
        });

        $this->searchable($entity);

        if (request()->wantsJson()) {
            return response()->json(
                [
                    'success' => true,
                    'message' => trans('admin::messages.resource_updated', [
                        'resource' => $this->getLabel(),
                    ]),
                ],
                200
            );
        }

        return redirect()
            ->route('admin.products.index', $this->indexFilters(request()))
            ->withSuccess(trans('admin::messages.resource_updated', [
                'resource' => $this->getLabel(),
            ]));
    }

    private function indexFilters(Request $request): array
    {
        return array_filter(
            $request->only([
                'page_filter_category_id',
                'page_filter_brand_id',
                'page_filter_is_active',
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
    }

    public function giftConfig(Product $product): JsonResponse
    {
        $product->load([
            'options.values',
        ]);

        $packagings = ProductPackaging::withoutGlobalScope('locale')
            ->with([
                'translations' => function ($query) {
                    $query->withoutGlobalScope('locale');
                },
            ])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,

            'options' => $product->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'option_id' => $option->option_id,
                    'name' => $option->name,
                    'type' => $option->type,
                    'is_required' => (bool) $option->is_required,
                    'values' => $option->values->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'option_value_id' => $value->option_value_id,
                            'label' => $value->label,
                        ];
                    })->values(),
                ];
            })->values(),

            'packagings' => $packagings->map(function ($packaging) {
                return [
                    'id' => $packaging->id,
                    'name' => sprintf($packaging->name, $packaging->qty),
                ];
            })->values(),
        ]);
    }

}
