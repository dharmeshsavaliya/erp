<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Services\Whatsapp\ChatApi\ChatApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendQueuePendingChatMessagesGroup extends Command
{
    const BROADCAST_PRIORITY        = 8;
    const MARKETING_MESSAGE_TYPE_ID = 3;

    public $waitingMessages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:queue-pending-chat-group-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send queue pending chat group messages, run at every 3rd minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function getNumberList()
    {
        $allWhatsappNo = config("apiwha.instances");

        \Log::info(print_r(["No found From instance",$allWhatsappNo],true));

        ksort($allWhatsappNo);

        $noList = [];
        if (!empty($allWhatsappNo)) {
            foreach ($allWhatsappNo as $no => $dataInstance) {
                $no       = $dataInstance["number"];
                $noList[] = $no;
            }
        }

        return $noList;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //try {

        \Log::info("Queue chat for group job started");

        $report = \App\CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $numberList = array_unique(self::getNumberList());

        // get the status for approval
        $approveMessage = \App\Helpers\DevelopmentHelper::needToApproveMessage();
        $limit          = ChatMessage::getQueueLimit();

        // if message is approve then only need to run the queue
        if ($approveMessage == 1) {

            \Log::info("Queue chat for group job approve message turned on");

            $allWhatsappNo = config("apiwha.instances");

            $this->waitingMessages = [];
            if (!empty($numberList)) {
                foreach ($numberList as $no) {
                    $chatApi                    = new ChatApi;
                    $waitingMessage             = $chatApi->waitingLimit($no);
                    $this->waitingMessages[$no] = $waitingMessage;
                }
            }

            \Log::info(print_r($numberList,true));
            \Log::info("Above number found");

            if (!empty($numberList)) {
                $groups = ChatMessage::where('is_queue', ">", 0)->where("group_id", ">", 0)->groupBy("group_id")->pluck("group_id")->toArray();

                \Log::info(print_r($groups,true));

                foreach ($numberList as $number) {
                    $sendLimit = isset($limit[$number]) ? $limit[$number] : 0;
                    foreach ($groups as $group) {
                        // get the group list first
                        $chatMessage = ChatMessage::where('is_queue', ">", 0)
                            ->join("customers as c", "c.id", "chat_messages.customer_id")
                            ->where("chat_messages.group_id", $group)
                            ->where("c.whatsapp_number", $number)
                            ->select("chat_messages.*")
                            ->limit($sendLimit)
                            ->get();

                        \Log::info("Chat Message count found =>".$chatMessage->count());

                        if (!$chatMessage->isEmpty()) {
                            
                            foreach ($chatMessage as $value) {
                                // check first if message need to be send from broadcast
                                if ($value->is_queue > 1) {
                                    $sendNumber = \DB::table("whatsapp_configs")->where("id", $value->is_queue)->first();
                                    // if chat message has image then send as a multiple message
                                    if ($images = $value->getMedia(config('constants.media_tags'))) {
                                        foreach ($images as $k => $image) {
                                            \App\ImQueue::create([
                                                "im_client"                 => "whatsapp",
                                                "number_to"                 => $value->customer->phone,
                                                "number_from"               => ($sendNumber) ? $sendNumber->number : $value->customer->whatsapp_number,
                                                "text"                      => ($k == 0) ? $value->message : "",
                                                "image"                     => $image->getUrl(),
                                                "priority"                  => self::BROADCAST_PRIORITY,
                                                "marketing_message_type_id" => self::MARKETING_MESSAGE_TYPE_ID,
                                            ]);
                                        }
                                    } else {
                                        \App\ImQueue::create([
                                            "im_client"                 => "whatsapp",
                                            "number_to"                 => $value->customer->phone,
                                            "number_from"               => ($sendNumber) ? $sendNumber->number : $value->customer->whatsapp_number,
                                            "text"                      => $value->message,
                                            "priority"                  => self::BROADCAST_PRIORITY,
                                            "marketing_message_type_id" => self::MARKETING_MESSAGE_TYPE_ID,
                                        ]);
                                    }

                                    $value->is_queue = 0;
                                    $value->save();

                                } else {

                                    // check message is full or not
                                    $isSendingLimitFull = isset($this->waitingMessages[$value->customer->whatsapp_number])
                                    ? $this->waitingMessages[$value->customer->whatsapp_number] : 0;
                                    // if message queue is full then go for the next;
                                    if ($isSendingLimitFull >= config("apiwha.message_queue_limit", 100)) {
                                        continue;
                                    }

                                    $myRequest = new Request();
                                    $myRequest->setMethod('POST');
                                    $myRequest->request->add(['messageId' => $value->id]);
                                    app('App\Http\Controllers\WhatsAppController')->approveMessage('customer', $myRequest);
                                }
                            }
                        }
                    }

                }
            }
        }

        $report->update(['end_time' => Carbon::now()]);
        /*} catch (\Exception $e) {
    \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }*/

    }
}
