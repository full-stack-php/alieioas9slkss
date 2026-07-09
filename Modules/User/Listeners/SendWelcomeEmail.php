<?php

namespace Modules\User\Listeners;

use Modules\User\Events\CustomerRegistered;

class SendWelcomeEmail
{
    public function handle(CustomerRegistered $event): void
    {
        // Email sending is handled by Modules\EmailTemplate\Listeners\SendCustomerRegistrationEmailTemplate.
    }
}
