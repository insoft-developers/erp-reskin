<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class SalesReportExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $fileName;
    public $dateName;
    public $exportType;

    /**
     * Create a new message instance.
     */
    public function __construct($filePath, $fileName, $dateName, $exportType)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->dateName = $dateName;
        $this->exportType = $exportType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Penjualan Advance - ' . $this->dateName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sales-report-export',
            with: [
                'dateName' => $this->dateName,
                'exportType' => $this->exportType,
            ]
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
            Attachment::fromPath($this->filePath)
                ->as($this->fileName)
        ];
    }
}
