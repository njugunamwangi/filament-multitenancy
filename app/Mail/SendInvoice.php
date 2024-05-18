<?php

namespace App\Mail;

use App\Models\Invoice;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use LasseRafn\Initials\Initials;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    private Invoice $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice '.$this->invoice->serial,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-invoice',
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

        $name = 'invoice_'.(new Initials)->name($company->name)->length(str_word_count($company->name))->generate().'_'.str_pad($this->invoice->serial_number, 5, '0', STR_PAD_LEFT).'.pdf';

        return [
            Attachment::fromPath(storage_path('app/public/invoices/'.$name))
                ->withMime('application/pdf'),
        ];
    }
}
