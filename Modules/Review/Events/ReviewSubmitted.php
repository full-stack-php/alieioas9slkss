<?php

namespace Modules\Review\Events;

use Modules\Review\Entities\Review;
use Illuminate\Queue\SerializesModels;

class ReviewSubmitted
{
    use SerializesModels;

    public function __construct(
        public Review $review
    ) {
    }
}
