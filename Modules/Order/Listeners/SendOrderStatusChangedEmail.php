<?php

namespace Modules\Order\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\Order\Events\OrderStatusChanged;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;
use Modules\Order\Mail\OrderStatusChanged as OrderStatusChangedEmail;

class SendOrderStatusChangedEmail
{
    public function handle(OrderStatusChanged $event)
    {
        if (!in_array($event->order->status, setting('email_order_statuses', []))) {
            return;
        }

        $handled = app(EmailTemplateMailer::class)->send(
            EmailTemplateType::ORDER_STATUS,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $event->order->customer_email,
            ['order' => $event->order],
            (string) $event->order->status
        );

        if (!$handled) {
            Mail::to($event->order->customer_email)
                ->send(new OrderStatusChangedEmail($event->order));
        }
    }
}
