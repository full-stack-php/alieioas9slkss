<?php

namespace Modules\Order\Admin;

use Modules\Admin\Ui\AdminTable;

class OrderStatusTable extends AdminTable
{
    /**
     * Raw columns that will not be escaped.
     *
     * @var array
     */
    protected array $defaultRawColumns = ['name', 'status'];

    /**
     * Make table structure and resource formatting.
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function make()
    {
        return $this->newTable()
            ->addColumn('name', function ($orderStatus) {
                return '<span class="badge" style="background-color: ' . e($orderStatus->color) . '; color: #fff; padding: 6px 10px;">'
                    . e($orderStatus->name)
                    . '</span>';
            });
    }
}
