<?php

namespace Modules\Order\Listeners;

use Modules\Order\Events\OrderStatusChanged;
use Modules\User\Entities\User;

class UpdateCustomerGroupByOrdersTotal
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;

        if (! $order || ! $order->customer_id) {
            return;
        }

        $customer = User::find($order->customer_id);

        if (! $customer) {
            return;
        }

        $customer->syncCustomerGroupByOrdersTotal();
    }
}
