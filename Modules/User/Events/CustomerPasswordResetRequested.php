<?php

namespace Modules\User\Events;

use Modules\User\Entities\User;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordResetRequested
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $resetUrl,
        public string $code
    ) {
    }
}
