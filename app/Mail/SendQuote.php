<?php

namespace App\Mail;

use App\Models\Quote;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use LasseRafn\Initials\Initials;

class SendQuote extends Mailable
{
    use Queueable, SerializesModels;

    private Quote $quote;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quote '.$this->quote->serial,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-quote',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $company = Filament::getTenant();

        $name = 'quote_'.(new Initials)->name($company->name)->length(str_word_count($company->name))->generate().'_'.str_pad($this->quote->serial_number, 5, '0', STR_PAD_LEFT).'.pdf';

        return [
            Attachment::fromPath(storage_path('app/public/quotes/'.$name))
                ->withMime('application/pdf'),
        ];
    }
}
