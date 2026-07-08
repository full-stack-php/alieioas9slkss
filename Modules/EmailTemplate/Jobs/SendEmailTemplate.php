<?php

namespace Modules\EmailTemplate\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Modules\EmailTemplate\Mail\TemplateEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Modules\EmailTemplate\Services\EmailTemplateRenderer;
use Modules\EmailTemplate\Services\EmailTemplateMailLogger;

class SendEmailTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $templateId,
        public mixed $to,
        public array $data = []
    ) {
    }

    public function handle(
        EmailTemplateRenderer $renderer,
        EmailTemplateMailLogger $logger
    ): void {
        $template = EmailTemplate::withoutGlobalScope('active')->find($this->templateId);

        if (!$template || !$template->is_active) {
            return;
        }

        try {
            $rendered = $renderer->render($template, $this->data);

            Mail::to($this->to)->send(new TemplateEmail(
                $rendered['subject'],
                $rendered['html']
            ));
        } catch (Throwable $exception) {
            $logger->error('Queued email template sending failed.', $exception, [
                'template_id' => $this->templateId,
                'to' => $this->to,
                'type' => $template->type ?? null,
                'recipient' => $template->recipient ?? null,
                'status_key' => $template->status_key ?? null,
            ]);

            throw $exception;
        }
    }
}
