<?php

namespace Modules\Order\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderStatus;
use Modules\Admin\Traits\HasCrudActions;

class OrderController
{
    use HasCrudActions;

    protected $model = Order::class;

    protected $with = [
        'products',
        'products.children',
        'products.parent',
        'products.options.values',
        'products.packaging',
        'products.product',
        'coupon',
        'orderStatus.translation',
    ];

    protected $label = 'order::orders.order';

    protected $viewPath = 'order::admin.orders';

    public function show($id): Response
    {
        $order = $this->getEntity($id);

        return response()->view("{$this->viewPath}.show", [
            'order' => $order,
            'orderStatuses' => OrderStatus::list(),
        ]);
    }
}
