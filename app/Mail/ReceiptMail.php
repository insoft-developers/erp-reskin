<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $reference;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $reference)
    {
        $this->data = $data;
        $this->reference = $reference;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = PDF::loadView('receipt', $this->data)->setPaper([0, 0, 165, 340], 'portrait');
        return $this->subject('Your Receipt')
                    ->view('emails.receipt')
                    ->attachData($pdf->output(), 'receipt.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
