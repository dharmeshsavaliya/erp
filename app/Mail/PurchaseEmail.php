<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject;
    public $message;

    public function __construct(string $subject, string $message)
    {
      $this->subject = $subject;
      $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('buying@amourint.com')
                    ->bcc('customercare@sololuxury.co.in')
                    ->subject($this->subject)
                    ->markdown('emails.customers.email');
    }
}
