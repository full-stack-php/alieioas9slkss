<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\Checkout\Events\OrderPlaced;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendNewOrderEmailTemplates
{
    public function handle(OrderPlaced $event): void
    {
        $mailer = app(EmailTemplateMailer::class);

        if (setting('admin_order_email') && setting('store_email')) {
            $mailer->send(
                EmailTemplateType::NEW_ORDER,
                EmailTemplateType::RECIPIENT_ADMIN,
                setting('store_email'),
                ['order' => $event->order]
            );
        }

        if (setting('invoice_email') && $event->order->customer_email) {
            $mailer->send(
                EmailTemplateType::NEW_ORDER,
                EmailTemplateType::RECIPIENT_CUSTOMER,
                $event->order->customer_email,
                ['order' => $event->order]
            );
        }
    }
}
