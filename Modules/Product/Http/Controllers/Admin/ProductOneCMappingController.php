<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductPackaging;
use Modules\Product\Entities\ProductOneCMapping;
use Modules\Product\Admin\ProductOneCMappingTable;
use Modules\Product\Http\Requests\SaveProductOneCMappingRequest;

class ProductOneCMappingController
{
    public function index(Request $request)
    {
        if ($request->has('table')) {
            return $this->table();
        }

        return view('product::admin.product_one_c_mappings.index');
    }

    public function create()
    {
        return view('product::admin.product_one_c_mappings.create', [
            'mapping' => new ProductOneCMapping(),
        ]);
    }

    public function store(SaveProductOneCMappingRequest $request)
    {
        ProductOneCMapping::create($this->prepareData($request));

        return redirect()
            ->route('admin.product_one_c_mappings.index')
            ->withSuccess('Запись создана');
    }

    public function edit($id)
    {
        $mapping = ProductOneCMapping::with([
            'product.translations',
            'packaging.translations',
        ])->findOrFail($id);

        return view('product::admin.product_one_c_mappings.edit', [
            'mapping' => $mapping,
        ]);
    }

    public function update(SaveProductOneCMappingRequest $request, $id)
    {
        $mapping = ProductOneCMapping::findOrFail($id);

        $mapping->update($this->prepareData($request));

        return redirect()
            ->route('admin.product_one_c_mappings.index')
            ->withSuccess('Запись обновлена');
    }

    public function destroy(string $ids): void
    {
        ProductOneCMapping::whereIn('id', explode(',', $ids))->delete();
    }

    public function table(): ProductOneCMappingTable
    {
        $query = ProductOneCMapping::with([
            'product.translations',
            'packaging.translations',
        ])->select('product_one_c_mappings.*');

        return new ProductOneCMappingTable($query);
    }

    public function productsSearch(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q'));

        if ($query === '') {
            return response()->json([]);
        }

        $products = Product::withoutGlobalScope('active')
            ->where(function ($builder) use ($query) {
                $builder
                    ->whereTranslationLike('name', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('1c_id', 'like', "%{$query}%");
            })
            ->limit(15)
            ->get();

        return response()->json(
            $products->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'text' => trim($product->name . ' / SKU: ' . $product->sku),
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'base_1c_id' => $product->getAttribute('1c_id'),
                ];
            })->values()
        );
    }

    public function productConfig($id): JsonResponse
    {
        $product = Product::withoutGlobalScope('active')
            ->with([
                'options.values',
                'adminPackagings',
            ])
            ->findOrFail($id);

        $packagings = ProductPackaging::withoutGlobalScope('locale')
            ->with([
                'translations' => function ($query) {
                    $query->withoutGlobalScope('locale');
                },
            ])
            ->where('product_id', $product->id)
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'base_1c_id' => $product->getAttribute('1c_id'),

            'packagings' => $packagings->map(function ($packaging) {
                return [
                    'id' => $packaging->id,
                    'name' => $packaging->name ?: 'Упаковка #' . $packaging->id,
                    'qty' => $packaging->qty,
                ];
            })->values(),

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
        ]);
    }

    private function prepareData(SaveProductOneCMappingRequest $request): array
    {
        $data = $request->validated();

        $data['product_packaging_id'] = $data['product_packaging_id'] ?: null;

        $options = collect($data['product_options'] ?? [])
            ->filter(fn ($value) => !is_null($value) && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        $data['product_options'] = empty($options) ? null : $options;

        return $data;
    }
}
