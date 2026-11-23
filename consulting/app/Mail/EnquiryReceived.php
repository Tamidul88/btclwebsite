<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Enquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // The From address is ours. The submitter's address goes in
            // Reply-To only — never into From or any header we build by hand,
            // so a crafted value cannot forge the envelope.
            subject: 'New '.$this->enquiry->type.' enquiry',
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.enquiry-received');
    }
}
