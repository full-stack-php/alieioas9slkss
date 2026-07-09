<?php

namespace Modules\Checkout\Listeners;

use Modules\Checkout\Events\OrderPlaced;

class SendNewOrderEmails
{
    public function handle(OrderPlaced $event): void
    {
        // Email sending is handled by Modules\EmailTemplate\Listeners\SendNewOrderEmailTemplates.
    }
}
