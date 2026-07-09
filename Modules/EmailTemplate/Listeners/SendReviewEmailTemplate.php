<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\Review\Events\ReviewSubmitted;
use Modules\EmailTemplate\Services\EmailTemplateType;
use Modules\EmailTemplate\Services\EmailTemplateMailer;

class SendReviewEmailTemplate
{
    public function handle(ReviewSubmitted $event): void
    {
        if (!setting('store_email')) {
            return;
        }

        $review = $event->review->loadMissing(['product', 'reviewer']);

        app(EmailTemplateMailer::class)->send(
            EmailTemplateType::REVIEW,
            EmailTemplateType::RECIPIENT_ADMIN,
            setting('store_email'),
            [
                'user' => $review->reviewer,
                'fullname' => $review->reviewer_name,
                'email' => optional($review->reviewer)->email,
                'review_url' => route('admin.reviews.edit', $review->id),

                'product' => $review->product,
                'product_name' => optional($review->product)->name,
                'product_url' => $review->product ? route('products.show', ['slug' => $review->product->slug]) : '',

                'review_rating' => $review->rating,
                'review_plus' => $review->plus,
                'review_minus' => $review->minus,
                'review_comment' => $review->comment,
            ]
        );
    }
}
