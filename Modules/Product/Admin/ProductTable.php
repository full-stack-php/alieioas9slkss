<?php

namespace Modules\Product\Admin;

use Modules\Admin\Ui\AdminTable;
use Illuminate\Http\JsonResponse;
use Modules\Product\Entities\Product;

class ProductTable extends AdminTable
{
    /**
     * Raw columns that will not be escaped.
     *
     * @var array
     */
    protected array $rawColumns = ['price', 'in_stock'];


    /**
     * Make table response for the resource.
     *
     * @return JsonResponse
     */
    public function make()
    {
        return $this->newTable()
            ->editColumn('thumbnail', function ($product) {
                return view('admin::partials.table.image', [
                    'file' => $product->base_image,
                ]);
            })
            ->editColumn('price', function (Product $product) {
                return product_price_formatted($product, function ($price, $specialPrice) use ($product) {
                    if ($product->hasSpecialPrice()) {
                        return "<span class='m-r-5'>{$specialPrice}</span>
                            <del class='text-red'>{$price}</del>";
                    }

                    return "<span class='m-r-5'>{$price}</span>";
                });
            })
            ->editColumn('in_stock', function (Product $product) {
                if ($product->isPreorder()) {
                    return "<span class='badge badge-soft-warning rounded-pill me-1'>"
                        . trans('product::products.form.manage_stock_states.2')
                        . '</span>';
                }

                if ($product->isDiscontinued()) {
                    return "<span class='badge badge-soft-secondary rounded-pill me-1'>"
                        . trans('product::products.form.manage_stock_states.3')
                        . '</span>';
                }

                if ($product->isInStock()) {
                    return "<span class='badge badge-soft-success rounded-pill me-1'>"
                        . trans('product::products.form.stock_availability_states.1')
                        . '</span>';
                }

                return "<span class='badge badge-soft-danger rounded-pill me-1'>"
                    . trans('product::products.form.stock_availability_states.0')
                    . '</span>';
            });
    }
}
