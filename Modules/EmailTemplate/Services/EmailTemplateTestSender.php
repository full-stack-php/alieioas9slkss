<?php

namespace Modules\EmailTemplate\Services;

use Throwable;
use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Mail\TemplateEmail;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateTestSender
{
    public function __construct(
        private EmailTemplateRenderer $renderer,
        private EmailTemplateDemoData $demoData,
        private EmailTemplateMailLogger $logger
    ) {
    }

    public function send(string $email, array $payload): void
    {
        try {
            $template = $this->makeTemplate($payload);

            $rendered = $this->renderer->render(
                $template,
                $this->demoData->forType($template->type, $payload)
            );

            Mail::to($email)->send(new TemplateEmail(
                $rendered['subject'],
                $rendered['html']
            ));
        } catch (Throwable $exception) {
            $this->logger->error('Email template test mail sending failed.', $exception, [
                'to' => $email,
                'type' => $payload['type'] ?? null,
                'recipient' => $payload['recipient'] ?? null,
                'status_key' => $payload['status_key'] ?? null,
            ]);

            throw $exception;
        }
    }

    private function makeTemplate(array $payload): EmailTemplate
    {
        $template = new EmailTemplate([
            'type' => $payload['type'],
            'recipient' => $payload['recipient'],
            'status_key' => $payload['status_key'] ?? null,
            'is_active' => true,
            'show_product_image' => (bool) ($payload['show_product_image'] ?? false),
            'product_image_max_width' => (int) ($payload['product_image_max_width'] ?? 80),
            'product_image_max_height' => (int) ($payload['product_image_max_height'] ?? 80),
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
        ]);

        foreach (supported_locales() as $locale => $language) {
            $translation = $template->translateOrNew($locale);

            $translation->name = $payload[$locale]['name'] ?? '';
            $translation->subject = $payload[$locale]['subject'] ?? '';
            $translation->content = $payload[$locale]['content'] ?? '';
        }

        return $template;
    }
}
