<?php

namespace Modules\Preorder\Admin;

use Modules\Admin\Ui\AdminTable;
use Modules\Preorder\Entities\Preorder;

class PreorderTable extends AdminTable
{
    protected array $rawColumns = [
        'product',
        'phone',
    ];

    public function make()
    {
        return $this->newTable()
            ->addColumn('product', function (Preorder $preorder) {
                if (!$preorder->product) {
                    return trans('preorder::preorders.product_deleted');
                }

                $productName = e(
                    $preorder->product->name
                        ?: trans('preorder::preorders.empty')
                );

                $sku = e(
                    $preorder->product->sku
                        ?: trans('preorder::preorders.empty')
                );

                $url = route(
                    'admin.products.edit',
                    $preorder->product->id
                );

                return '<a href="' . e($url) . '">'
                    . $productName
                    . '</a>'
                    . '<div class="text-muted">'
                    . trans('preorder::preorders.table.sku')
                    . ': '
                    . $sku
                    . '</div>';
            })
            ->editColumn('phone', function (Preorder $preorder) {
                $phone = e($preorder->phone);

                return '<a href="tel:' . $phone . '">'
                    . $phone
                    . '</a>';
            });
    }
}
