<?php

namespace Modules\Contact\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Contact\Entities\ContactSubmission;

class ContactSubmissionCreated
{
    use SerializesModels;

    public function __construct(
        public ContactSubmission $submission
    ) {
    }
}
