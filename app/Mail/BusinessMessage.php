<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BusinessMessage extends Mailable
{
    protected string $subjectText;

    public function __construct(
        public string $businessName,
        string $subject,
        public string $body,
    ) {
        $this->subjectText = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('mail.business-message', [
                'businessName' => $this->businessName,
                'body' => nl2br(e($this->body)),
            ])->render(),
        );
    }
}
