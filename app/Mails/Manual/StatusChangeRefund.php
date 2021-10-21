<?php

namespace App\Mails\Manual;

use App\Customer;
use App\ReturnExchange;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusChangeRefund extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $return;

    public function __construct(ReturnExchange $return)
    {
        $this->return = $return;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject = "Your refund request status has been changed";
        $return = $this->return;
        $customer = $return->customer;

        $this->subject = $subject;
        $this->fromMailer = "customercare@sololuxury.co.in";

        if ($customer) {

            if ($customer->store_website_id > 0) {
                $emailAddress = \App\EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                if ($emailAddress) {
                    $this->fromMailer = $emailAddress->from_address;
                }
                $template = \App\MailinglistTemplate::template("Status Change Refund", $customer->store_website_id);
            } else {
                $emailAddress = \App\EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
                if ($emailAddress) {
                    $this->fromMailer = $emailAddress->from_address;
                }
                $template = \App\MailinglistTemplate::template("Status Change Refund");
            }

            if ($template) {
                if ($template->from_email != '') {
                    $this->fromMailer = $template->from_email;
                }

                if (!empty($template->mail_tpl)) {
                    // need to fix the all email address
                    $this->subject = $template->subject;
                    return $this->subject($this->subject)
                        ->view($template->mail_tpl, compact(
                            'customer', 'return'
                        ));
                }
            }
        }

        return $this->subject($this->subject)->markdown('emails.customers.blank');
    }
}
