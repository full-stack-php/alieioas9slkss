<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\User\Events\CustomerPasswordResetRequested;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendCustomerPasswordResetEmailTemplate
{
    public function handle(CustomerPasswordResetRequested $event): void
    {
        if (!$event->user->email) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::CUSTOMER_PASSWORD_RESET,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $event->user->email,
            [
                'user' => $event->user,
                'reset_url' => $event->resetUrl,
                'reset_code' => $event->code,
            ]
        );
    }
}
