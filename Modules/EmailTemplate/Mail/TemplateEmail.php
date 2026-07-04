<?php

namespace Modules\EmailTemplate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $html;

    public function __construct(string $subjectText, string $html)
    {
        $this->subjectText = $subjectText;
        $this->html = $html;
    }

    public function build()
    {
        return $this
            ->subject($this->subjectText)
            ->view('emailtemplate::emails.raw');
    }
}
