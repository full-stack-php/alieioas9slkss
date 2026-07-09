<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\Contact\Events\ContactSubmissionCreated;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendContactFormEmailTemplate
{
    public function handle(ContactSubmissionCreated $event): void
    {
        $submission = $event->submission;

        if (!setting('store_email')) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::CONTACT_FORM,
            EmailTemplateType::RECIPIENT_ADMIN,
            setting('store_email'),
            [
                'fullname' => $submission->name,
                'email' => $submission->email,
                'phone' => $submission->phone,
                'message' => $this->message($submission),
            ]
        );
    }

    private function message($submission): string
    {
        return implode("\n", array_filter([
            $submission->type === 'callback'
                ? 'Новая заявка на обратный звонок'
                : 'Новая заявка со страницы контактов',

            $submission->topic ? 'Тема: ' . $submission->topic : null,
            $submission->message ? 'Комментарий: ' . $submission->message : null,
            $submission->preferred_call_at ? 'Желаемое время звонка: ' . $submission->preferred_call_at->format('Y-m-d H:i:s') : null,
            $submission->source_url ? 'Страница: ' . $submission->source_url : null,
            $submission->ip_address ? 'IP: ' . $submission->ip_address : null,
        ]));
    }
}
