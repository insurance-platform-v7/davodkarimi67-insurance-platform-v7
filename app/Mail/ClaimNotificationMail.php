<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClaimNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $messageText,
    ) {}

    public function build(): static
    {
        return $this
            ->subject($this->subjectText)
            ->html(
                nl2br(
                    e($this->messageText)
                )
            );
    }
}
