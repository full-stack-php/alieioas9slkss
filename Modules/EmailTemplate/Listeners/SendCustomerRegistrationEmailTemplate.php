<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\User\Events\CustomerRegistered;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendCustomerRegistrationEmailTemplate
{
    public function handle(CustomerRegistered $event): void
    {
        if (!setting('welcome_email')) {
            return;
        }

        if (!$event->user->email) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::CUSTOMER_REGISTRATION,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $event->user->email,
            ['user' => $event->user]
        );
    }
}
