<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\Checkout\Events\OrderPlaced;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendNewOrderEmailTemplates
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->fresh() ?: $event->order;

        $statusKey = $order->status ? (string) $order->status : null;

        $mailer = app(EmailTemplateMailer::class);

        if (setting('admin_order_email') && setting('store_email')) {
            $mailer->send(
                EmailTemplateType::NEW_ORDER,
                EmailTemplateType::RECIPIENT_ADMIN,
                setting('store_email'),
                ['order' => $order],
                $statusKey
            );
        }

        if (setting('invoice_email') && $order->customer_email && !$this->shouldSkipCustomerMail($order)) {
            $mailer->send(
                EmailTemplateType::NEW_ORDER,
                EmailTemplateType::RECIPIENT_CUSTOMER,
                $order->customer_email,
                ['order' => $order],
                $statusKey
            );
        }
    }

    private function shouldSkipCustomerMail($order): bool
    {
        return (bool) ($order->is_quick_order_guest ?? false);
    }
}
