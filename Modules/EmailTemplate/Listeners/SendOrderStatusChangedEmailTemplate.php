<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\Order\Events\OrderStatusChanged;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendOrderStatusChangedEmailTemplate
{
    public function handle(OrderStatusChanged $event): void
    {
        if (!in_array($event->order->status, setting('email_order_statuses', []))) {
            return;
        }

        if (!$event->order->customer_email) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::ORDER_STATUS,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $event->order->customer_email,
            ['order' => $event->order],
            (string) $event->order->status
        );
    }
}
