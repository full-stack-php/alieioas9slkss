<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\QuestionAnswer\Events\QuestionAnswerAnswered;
use Modules\QuestionAnswer\Events\QuestionAnswerSubmitted;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendQuestionAnswerEmailTemplate
{
    public function handle(QuestionAnswerSubmitted|QuestionAnswerAnswered $event): void
    {
        $questionAnswer = $event->questionAnswer->loadMissing(['product', 'asker']);

        if ($event instanceof QuestionAnswerSubmitted) {
            $this->sendToAdmin($questionAnswer);

            return;
        }

        $this->sendToCustomer($questionAnswer);
    }

    private function sendToAdmin($questionAnswer): void
    {
        if (!setting('store_email')) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::CUSTOMER_QUESTION_ANSWER,
            EmailTemplateType::RECIPIENT_ADMIN,
            setting('store_email'),
            $this->data($questionAnswer)
        );
    }

    private function sendToCustomer($questionAnswer): void
    {
        if (!$questionAnswer->asker || !$questionAnswer->asker->email) {
            return;
        }

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::CUSTOMER_QUESTION_ANSWER,
            EmailTemplateType::RECIPIENT_CUSTOMER,
            $questionAnswer->asker->email,
            $this->data($questionAnswer)
        );
    }

    private function data($questionAnswer): array
    {
        return [
            'user' => $questionAnswer->asker,
            'fullname' => $questionAnswer->asker_name,
            'phone' => $questionAnswer->asker_phone,
            'email' => optional($questionAnswer->asker)->email,
            'question' => $questionAnswer->question,
            'answer' => $questionAnswer->answer,
            'product' => $questionAnswer->product,
            'product_name' => optional($questionAnswer->product)->name,
            'product_url' => $questionAnswer->product ? route('products.show', ['slug' => $questionAnswer->product->slug]) : '',
        ];
    }
}
