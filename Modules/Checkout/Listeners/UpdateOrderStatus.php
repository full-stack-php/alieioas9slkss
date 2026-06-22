<?php

namespace Modules\Checkout\Listeners;

use Modules\Checkout\Events\OrderPlaced;

class UpdateOrderStatus
{
    public function handle(OrderPlaced $event): void
    {
        // Статус заказа теперь управляется:
        // 1. OrderService при создании заказа
        // 2. PaymentStatusService при изменении статуса оплаты
    }
}
