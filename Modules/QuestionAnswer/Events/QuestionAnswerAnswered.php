<?php

namespace Modules\QuestionAnswer\Events;

use Illuminate\Queue\SerializesModels;
use Modules\QuestionAnswer\Entities\QuestionAnswer;

class QuestionAnswerAnswered
{
    use SerializesModels;

    public function __construct(
        public QuestionAnswer $questionAnswer
    ) {
    }
}
