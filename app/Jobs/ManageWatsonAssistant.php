<?php

namespace App\Jobs;

use App\Customer;
use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Model;
use App\WatsonAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManageWatsonAssistant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;
    protected $inputText;
    protected $contextReset;
    protected $message_application_id;
    protected $messageModel;
    protected $userType;
    protected $chat_message_log_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $inputText, $contextReset, $message_application_id, $messageModel = null, $userType = null, $chat_message_log_id = null)
    {
        $this->customer               = $customer;
        $this->inputText              = $inputText;
        $this->contextReset           = $contextReset;
        $this->message_application_id = $message_application_id;
        $this->messageModel           = $messageModel;
        $this->userType               = $userType;
        $this->chat_message_log_id    = $chat_message_log_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (isset($this->chat_message_log_id)) {
            \App\ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $this->chat_message_log_id,
                'request'                => "",
                'response'               => "Watson asistantant function job dispatched started",
                'status'                 => 'success',
            ]);
        }

        $store_website_id = ($this->customer->store_website_id > 0) ? $this->customer->store_website_id : 1;

        $account = WatsonAccount::where('store_website_id', $store_website_id)->first();
        if ($account) {
            $asistant = new AssistantService(
                "apiKey",
                $account->api_key
            );
            $asistant->set_url($account->url);

            if (isset($this->chat_message_log_id)) {
                \App\ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $this->chat_message_log_id,
                    'request'                => "",
                    'response'               => "Watson asistantant function send message from job started with account".$account->api_key,
                    'status'                 => 'success',
                ]);
            }

            Model::sendMessageFromJob($this->customer, $account, $asistant, $this->inputText, $this->contextReset, $this->message_application_id, $this->messageModel, $this->userType, $this->chat_message_log_id);
        } else {
            if (isset($this->chat_message_log_id)) {
                \App\ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $this->chat_message_log_id,
                    'request'                => "",
                    'response'               => "Watson asistantant function job account not found",
                    'status'                 => 'success',
                ]);
            }
        }
    }

    public function fail($exception = null)
    {
        $data = [
            'chatbot_message_log_id' => $this->chat_message_log_id,
            'request'                => "",
            'response'               => "Watson asistant queue failed.",
            'status'                 => 'failed',
        ];
        $chat_message_log = \App\ChatbotMessageLogResponse::StoreLogResponse($data);
        /* Remove data when job fail while creating..... */
        if ($this->method === 'create' && is_object($this->question)) {
            $this->question->delete();
        }
    }
}
