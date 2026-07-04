<?php

namespace Modules\Order\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Order\Entities\Order;
use Modules\Checkout\Mail\Invoice;
use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class OrderEmailController
{
    public function store(Order $order)
    {
        $handled = app(EmailTemplateMailer::class)->send(
            EmailTemplateType::NEW_ORDER,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $order->customer_email,
            ['order' => $order]
        );

        if (!$handled) {
            Mail::to($order->customer_email)
                ->send(new Invoice($order));
        }

        return back()->with('success', trans('order::messages.invoice_sent'));
    }
}
