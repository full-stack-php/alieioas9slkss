<?php

namespace Modules\User\Listeners;

use Swift_TransportException;
use Modules\User\Mail\Welcome;
use Illuminate\Support\Facades\Mail;
use Modules\User\Events\CustomerRegistered;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendWelcomeEmail
{
    public function handle(CustomerRegistered $event)
    {
        try {
            if (!setting('welcome_email')) {
                return;
            }

            $handled = app(EmailTemplateMailer::class)->send(
                EmailTemplateType::CUSTOMER_REGISTRATION,
                EmailTemplateType::RECIPIENT_CUSTOMER,
                $event->user->email,
                ['user' => $event->user]
            );

            if (!$handled) {
                Mail::to($event->user->email)
                    ->send(new Welcome($event->user->first_name));
            }
        } catch (Swift_TransportException $e) {
            //
        }
    }
}
