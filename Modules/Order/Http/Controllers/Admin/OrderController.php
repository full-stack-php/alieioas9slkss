<?php

namespace Modules\Order\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderStatus;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Order\Events\OrderStatusChanged;

class OrderController
{
    use HasCrudActions {
        update as performCrudUpdate;
    }

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

    public function update($id)
    {
        $order = $this->getEntity($id);
        $oldStatus = $order->status;

        $response = $this->performCrudUpdate($id);

        $order->refresh();

        if ((string) $oldStatus !== (string) $order->status) {
            event(new OrderStatusChanged($order));
        }

        return $response;
    }
}
