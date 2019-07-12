<?php

namespace App\Mail;

use App\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReplyToEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Email
     */
    private $emailToReply;

    /**
     * @var string
     */
    private $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Email $email, $message)
    {
        $this->emailToReply = $email;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailToReply = $this->emailToReply;
        $message = $this->message;

        $this->to($emailToReply->from);
        $this->from($emailToReply->to);
        $this->subject($emailToReply->subject);

        $userName = null;
        if ($emailToReply->model instanceof \App\Supplier) {
            $userName = $emailToReply->model->supplier;
        } elseif ($emailToReply->model instanceof \App\Customer) {
            $userName = $emailToReply->model->name;
        }

        $dateCreated = $emailToReply->created_at->format('D, d M Y');
        $timeCreated = $emailToReply->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, $userName <{$emailToReply->from}> wrote:";

        return $this->view('emails.reply-to-email', [
            'msg' => $message,
            'originalEmailMsg' => $emailToReply->message,
            'originalEmailInfo' => $originalEmailInfo
        ]);
    }
}
