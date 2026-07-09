<?php

namespace Modules\EmailTemplate\Providers;

use Modules\Checkout\Events\OrderPlaced;
use Modules\Contact\Events\ContactSubmissionCreated;
use Modules\EmailTemplate\Listeners\SendContactFormEmailTemplate;
use Modules\Order\Events\OrderStatusChanged;
use Modules\User\Events\CustomerRegistered;
use Modules\Review\Events\ReviewSubmitted;
use Modules\QuestionAnswer\Events\QuestionAnswerAnswered;
use Modules\QuestionAnswer\Events\QuestionAnswerSubmitted;
use Modules\User\Events\CustomerPasswordResetRequested;
use Modules\EmailTemplate\Listeners\SendReviewEmailTemplate;
use Modules\EmailTemplate\Listeners\SendNewOrderEmailTemplates;
use Modules\EmailTemplate\Listeners\SendQuestionAnswerEmailTemplate;
use Modules\EmailTemplate\Listeners\SendCustomerRegistrationEmailTemplate;
use Modules\EmailTemplate\Listeners\SendOrderStatusChangedEmailTemplate;
use Modules\EmailTemplate\Listeners\SendCustomerPasswordResetEmailTemplate;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendNewOrderEmailTemplates::class,
        ],

        OrderStatusChanged::class => [
            SendOrderStatusChangedEmailTemplate::class,
        ],

        CustomerRegistered::class => [
            SendCustomerRegistrationEmailTemplate::class,
        ],

        CustomerPasswordResetRequested::class => [
            SendCustomerPasswordResetEmailTemplate::class,
        ],

        ReviewSubmitted::class => [
            SendReviewEmailTemplate::class,
        ],

        QuestionAnswerSubmitted::class => [
            SendQuestionAnswerEmailTemplate::class,
        ],

        QuestionAnswerAnswered::class => [
            SendQuestionAnswerEmailTemplate::class,
        ],

        ContactSubmissionCreated::class => [
            SendContactFormEmailTemplate::class,
        ],
    ];
}
