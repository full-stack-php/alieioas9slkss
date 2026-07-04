<?php

namespace Modules\EmailTemplate\Services;

use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Mail\TemplateEmail;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateMailer
{
    public function __construct(
        private EmailTemplateRenderer $renderer
    ) {
    }

    public function send(string $type, string $recipient, mixed $to, array $data = [], ?string $statusKey = null): bool
    {
        $template = $this->findTemplate($type, $recipient, $statusKey);

        if (!$template) {
            return false;
        }

        if (!$template->is_active) {
            return true;
        }

        $rendered = $this->renderer->render($template, $data);

        Mail::to($to)->send(new TemplateEmail(
            $rendered['subject'],
            $rendered['html']
        ));

        return true;
    }

    private function findTemplate(string $type, string $recipient, ?string $statusKey = null): ?EmailTemplate
    {
        $query = EmailTemplate::forMail($type, $recipient);

        if (!is_null($statusKey) && $statusKey !== '') {
            $template = (clone $query)
                ->where('status_key', $statusKey)
                ->first();

            if ($template) {
                return $template;
            }
        }

        return (clone $query)
            ->whereNull('status_key')
            ->first();
    }
}
