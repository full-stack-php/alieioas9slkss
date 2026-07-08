<?php

namespace Modules\EmailTemplate\Services;

use Throwable;
use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Mail\TemplateEmail;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateMailer
{
    public function __construct(
        private EmailTemplateRenderer $renderer,
        private EmailTemplateMailLogger $logger
    ) {
    }

    public function send(string $type, string $recipient, mixed $to, array $data = [], ?string $statusKey = null): bool
    {
        try {
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
        } catch (Throwable $exception) {
            $this->logger->error('Email template mail sending failed.', $exception, [
                'type' => $type,
                'recipient' => $recipient,
                'to' => $this->normalizeRecipientForLog($to),
                'status_key' => $statusKey,
                'template_id' => isset($template) ? $template->id : null,
            ]);

            return false;
        }
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

    private function normalizeRecipientForLog(mixed $to): mixed
    {
        if (is_string($to) || is_array($to) || is_null($to)) {
            return $to;
        }

        if (is_object($to) && method_exists($to, 'getEmailForVerification')) {
            return $to->getEmailForVerification();
        }

        if (is_object($to) && isset($to->email)) {
            return $to->email;
        }

        return get_debug_type($to);
    }
}
