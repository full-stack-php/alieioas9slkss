<?php

namespace Modules\EmailTemplate\Services;

use Throwable;
use Modules\EmailTemplate\Jobs\SendEmailTemplate;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateMailer
{
    public function __construct(
        private EmailTemplateMailLogger $logger
    ) {
    }

    public function send(string $type, string $recipient, mixed $to, array $data = [], ?string $statusKey = null): bool
    {
        try {
            $template = $this->findTemplate($type, $recipient, $statusKey);

            if (!$template || !$template->is_active) {
                return false;
            }

            SendEmailTemplate::dispatch(
                $template->id,
                $this->normalizeRecipientForQueue($to),
                $data
            );

            return true;
        } catch (Throwable $exception) {
            $this->logger->error('Email template queue dispatch failed.', $exception, [
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

    private function normalizeRecipientForQueue(mixed $to): mixed
    {
        if (is_string($to) || is_array($to) || is_null($to)) {
            return $to;
        }

        if (is_object($to) && isset($to->email)) {
            return $to->email;
        }

        return $to;
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
