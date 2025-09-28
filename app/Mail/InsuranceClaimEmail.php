<?php

namespace App\Mail;

use App\Models\InsuranceClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InsuranceClaimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public InsuranceClaim $claim;

    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(InsuranceClaim $claim, string $pdfPath)
    {
        $this->claim = $claim;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Insurance Claim for Invoice '.$this->claim->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: '<p>Dear Sir/Madam,</p><p>Please find attached the insurance claim for invoice '.$this->claim->invoice->invoice_number.'.</p><p>Regards,<br>Geraye Healthcare</p>',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('insurance_claim_'.$this->claim->invoice->invoice_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
