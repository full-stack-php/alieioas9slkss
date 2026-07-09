<?php

namespace Modules\Order\Listeners;

use Modules\Order\Events\OrderStatusChanged;

class SendOrderStatusChangedEmail
{
    public function handle(OrderStatusChanged $event): void
    {
        // Email sending is handled by Modules\EmailTemplate\Listeners\SendOrderStatusChangedEmailTemplate.
    }
}
