<?php

namespace Modules\Product\Admin;

use Modules\Admin\Ui\AdminTable;
use Modules\Product\Entities\ProductOneCMapping;

class ProductOneCMappingTable extends AdminTable
{
    protected array $rawColumns = [
        'target',
        'one_c_id',
    ];

    public function make()
    {
        return $this->newTable()
            ->addColumn('product', function (ProductOneCMapping $mapping) {
                return $mapping->product->name ?: '#' . $mapping->product_id;
            })
            ->addColumn('target', function (ProductOneCMapping $mapping) {
                return e($mapping->target_label);
            })
            ->editColumn('external_id', function (ProductOneCMapping $mapping) {
                return e($mapping->external_id);
            })
            ->editColumn('one_c_id', function (ProductOneCMapping $mapping) {
                return '<code>' . e($mapping->one_c_id) . '</code>';
            });
    }
}
