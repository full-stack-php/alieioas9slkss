<?php

namespace Modules\Checkout\Listeners;

use Exception;
use Modules\Checkout\Mail\Invoice;
use Modules\Checkout\Mail\NewOrder;
use Illuminate\Support\Facades\Mail;
use Modules\Checkout\Events\OrderPlaced;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendNewOrderEmails
{
    public function handle(OrderPlaced $event)
    {
        try {
            $mailer = app(EmailTemplateMailer::class);

            if (setting('admin_order_email')) {
                $handled = $mailer->send(
                    EmailTemplateType::NEW_ORDER,
                    EmailTemplateType::RECIPIENT_ADMIN,
                    setting('store_email'),
                    ['order' => $event->order]
                );

                if (!$handled) {
                    Mail::to(setting('store_email'))
                        ->send(new NewOrder($event->order));
                }
            }

            if (setting('invoice_email')) {
                $handled = $mailer->send(
                    EmailTemplateType::NEW_ORDER,
                    EmailTemplateType::RECIPIENT_CUSTOMER,
                    $event->order->customer_email,
                    ['order' => $event->order]
                );

                if (!$handled) {
                    Mail::to($event->order->customer_email)
                        ->send(new Invoice($event->order));
                }
            }
        } catch (Exception) {
            //TODO:handle exception
        }
    }
}
