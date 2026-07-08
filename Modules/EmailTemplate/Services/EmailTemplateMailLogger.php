<?php

namespace Modules\EmailTemplate\Services;

use Throwable;
use Illuminate\Support\Facades\Log;

class EmailTemplateMailLogger
{
    public function error(string $message, Throwable $exception, array $context = []): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/mail_erorr.log'),
        ])->error($message, array_merge($context, [
            'exception' => get_class($exception),
            'error_message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]));
    }
}
