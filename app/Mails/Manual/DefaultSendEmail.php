<?php

namespace App\Mails\Manual;

use App\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultSendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $attchments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $attchments = [])
    {
        $this->email      = $email;
        $this->attchments      = $attchments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    // public function build()
    // {
    //     $email   = $this->email;
    //     $content = $email->message;

    //     return $this->to($email->to)
    //     ->from($email->from)
    //     ->subject($email->subject)
    //     ->view('emails.blank_content', compact('content'));
    // }

    public function build()
    {
        $email   = $this->email;
        $content = $email->message;
		
		$headerData = [
            'unique_args' => [
                'email_id' =>$email->id 
            ]
        ];

        \App\EmailLog::create([
            'email_id'   => $email->id,
            'email_log' => 'Header Data being attached in email from DefaultSendeEmail',
            'message'       => json_encode($headerData)
            ]);

        $header = $this->asString($headerData);
        
        \App\EmailLog::create([
            'email_id'   => $email->id,
            'email_log' => 'Header Data attached in email',
            'message'       => $header
            ]);
            
        $this->withSwiftMessage(function ($message) use ($header) {
            $message->getHeaders()
                    ->addTextHeader('X-SMTPAPI', $header);
        });
        $mailObj =  $this->to($email->to)
        ->from($email->from)
        ->subject($email->subject)
        ->view('emails.blank_content', compact('content'));	//->with([ 'custom_args' => $this->email ]);
        
		\App\EmailLog::create([
            'email_id'   => $email->id,
            'email_log' => 'Mail Object Created in DefaultSendEmail',
            'message'       => json_encode($mailObj)
            ]);
		 
        foreach($this->attchments as $attchment){
            $mailObj->attachFromStorageDisk('files', $attchment);
            \App\EmailLog::create([
                'email_id'   => $email->id,
                'email_log' => 'attachment added in DefaultSendEmail',
                'message'       => $attchment
                ]);
        }
		
		
        return $mailObj;

    }
	private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

        return $json;
    }


    private function asString($data)
    {
        $json = $this->asJSON($data);

        return wordwrap($json, 76, "\n   ");
    }
}
