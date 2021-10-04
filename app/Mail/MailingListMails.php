<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\MailinglistTemplate;

class MailingListMails extends Mailable
{
    use Queueable, SerializesModels;
    public $template;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailinglistTemplate $template)
    {
        $this->template = $template;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$template = $this->template;
		$customer   = $template->customer;
		if (!empty($template->mail_tpl)) {
                    // need to fix the all email address
            $html = view($template->mail_tpl, compact(
                'customer'
            ));
        } else{
			$html = $template['static_template'];
		} 
		if($template->from_email != null) {
			$this->fromMailer = $template->from_email;
		}

        return $this->from($this->fromMailer)->html($html, 'text/html');
    }
}
