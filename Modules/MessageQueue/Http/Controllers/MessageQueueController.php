<?php

namespace Modules\MessageQueue\Http\Controllers;

use App\ChatMessage;
use App\Services\Whatsapp\ChatApi\ChatApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MessageQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $groupList = ChatMessage::pendingQueueGroupList([
            "is_queue" => 1,
        ]);

        $sendingLimit  = ChatMessage::getQueueLimit();
        $sendStartTime = ChatMessage::getStartTime();
        $sendEndTime   = ChatMessage::getEndTime();

        $allWhatsappNo = config("apiwha.instances");

        $waitingMessages = [];
        //if(env("APP_ENV") != "local") {
        if (!empty($allWhatsappNo)) {
            foreach ($allWhatsappNo as $no => $dataInstance) {
                $no                   = ($no == 0) ? $dataInstance["number"] : $no;
                $chatApi              = new ChatApi;
                $waitingMessage       = $chatApi->waitingLimit($no);
                $waitingMessages[$no] = $waitingMessage;
            }
        }
        //}

        $countQueue = ChatMessage::join("customers as c", "c.id", "chat_messages.customer_id")
            ->where("is_queue", ">", 0)
            ->where("customer_id", ">", 0)
            ->groupBy("c.whatsapp_number")
            ->select(\DB::raw("count(*) as total_message"), "c.whatsapp_number")->get();

        return view('messagequeue::index', compact('groupList', 'sendingLimit', 'sendStartTime', 'sendEndTime', 'waitingMessages', 'countQueue'));
    }

    /**
     * Display a listing of the resource.
     * @return Response Json
     */
    public function records()
    {
        $from         = request("from", "");
        $to           = request("to", "");
        $limit        = request("limit", config('erp-customer.pagination'));
        $customerName = request("customer_name", "");
        $groupId      = request("group_id", 0);

        $chatMessage = ChatMessage::join("customers as c", "c.id", "chat_messages.customer_id")
            ->where("is_queue", ">", 0)
            ->where("customer_id", ">", 0);

        if (!empty($from)) {
            $chatMessage = $chatMessage->where("c.whatsapp_number", "like", "%" . $from . "%");
        }

        if (!empty($to)) {
            $chatMessage = $chatMessage->where("c.phone", "like", "%" . $to . "%");
        }

        if ($groupId > 0) {
            $chatMessage = $chatMessage->where("chat_messages.group_id", $groupId);
        }

        if (!empty($customerName)) {
            $chatMessage = $chatMessage->where("c.name", "like", "%" . $customerName . "%");
        }

        $chatMessage = $chatMessage->select(["chat_messages.*", "c.phone", "c.whatsapp_number", "c.name as customer_name"]);

        $chatMessage = $chatMessage->orderby("chat_messages.id", "DESC")->paginate($limit);

        $itemsList = [];

        foreach ($chatMessage->items() as $key => $items) {
            //$inserted = $items->attribute();
            $media = [];
            if ($images = $items->getMedia(config('constants.attach_image_tag'))) {
                foreach ($images as $image) {
                    $media[] = $image->getUrl();
                }
            }
            $items->mediaList = $media;
            $itemsList[]      = $items;
        }

        return response()->json([
            "code"       => 200,
            "data"       => $itemsList,
            "pagination" => (string) $chatMessage->links(),
            "total"      => $chatMessage->total(),
            "page"       => $chatMessage->currentPage(),
        ]);
    }

    public function deleteRecord(Request $request, $id)
    {
        $message = ChatMessage::find($id);

        if (!empty($message)) {
            $message->delete();
            return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
        }

        return response()->json(["code" => 500, "message" => "Sorry no message found in records"]);

    }

    public function actionHandler(Request $request)
    {
        $action = $request->get("action", "");
        $ids    = $request->get("ids", []);

        switch ($action) {
            case 'change_to_broadcast':
                if (!empty($ids) && is_array($ids)) {
                    \DB::update("update chat_messages as cm join customers as c on c.id = cm.customer_id join whatsapp_configs as wc
                    on wc.number = c.broadcast_number set cm.is_queue = wc.id where cm.id in (" . implode(",", $ids) . ");");
                    return response()->json(["code" => 200, "message" => "Updated to broadcast Successfully"]);
                }
                break;
            case 'change_customer_number':

                if (!empty($ids) && is_array($ids)) {
                    $number = $request->get("send_number", "");
                    if (!empty($number)) {
                        \DB::update("update chat_messages as cm join customers as c on c.id = cm.customer_id set c.whatsapp_number = '" . $number . "' where cm.id in (" . implode(",", $ids) . ");");
                    }
                    return response()->json(["code" => 200, "message" => "Updated to broadcast Successfully"]);
                }
                break;
            case 'delete_records':

                if (!empty($ids) && is_array($ids)) {
                    ChatMessage::whereIn("id", $ids)->delete();
                    return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
                }

                break;
            case 'delete_all':
                ChatMessage::where("is_queue", ">", 0)->delete();
                return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
                break;
        }

        return response()->json(["code" => 500, "message" => "Please select fields before action"]);

    }

    public function updateLimit(Request $request)
    {
        $limit     = $request->get("message_sending_limit", []);
        $startTime = $request->get("send_start_time", "");
        $endTime   = $request->get("send_end_time", "");

        \App\Setting::updateOrCreate(
            ["name" => "is_queue_sending_limit"],
            ["val" => json_encode($limit), "type" => "str"]
        );

        if (!empty($startTime)) {
            \App\Setting::updateOrCreate(
                ["name" => "is_queue_send_start_time", "type" => "string"],
                ["val" => $startTime]
            );
        }

        if (!empty($endTime)) {
            \App\Setting::updateOrCreate(
                ["name" => "is_queue_send_end_time", "type" => "string"],
                ["val" => $endTime]
            );
        }

        return response()->json(["code" => 200, "message" => "Done!"]);

    }

    public function report(Request $request)
    {
        $customerRange = request("customrange");
        $starRange     = explode(" - ", $customerRange);

        $chatMessage = new ChatMessage;

        if (isset($starRange[0])) {
            $chatMessage = $chatMessage->where("created_at", ">=", $starRange[0]);
        }

        if (isset($starRange[1])) {
            $chatMessage = $chatMessage->where("created_at", "<=", $starRange[1]);
        }

        $response = $chatMessage->groupBy("group_id")->orderBy("created_at", "DESC")->select(["created_at", "group_id", \DB::raw("count(*) as total_sent")])->get();

        $total = 0;

        foreach ($response as $res) {
            $total += $res->total_sent;
        }

        return response()->json(["code" => 200, "data" => $response, 'total' => $total]);
    }

    public function recall(Request $request)
    {
        $no = $request->get("send_number");
        $i  = 0;
        if (!empty($no)) {
            $queue = ChatApi::chatQueue($no);
            if (!empty($queue) && !empty($queue["first100"])) {
                foreach ($queue["first100"] as $message) {
                    $messageID = json_decode($message["metadata"], true);
                    if (!empty($messageID["msgId"])) {
                        $chatMessage = ChatMessage::where("unique_id", $messageID["msgId"])->where("is_queue", 0)->first();
                        if ($chatMessage) {
                            $chatMessage->is_queue = 1;
                            $chatMessage->approved = 0;
                            $chatMessage->save();
                            $i++;
                        }
                    }
                }
               ChatApi::deleteQueues($no);
            }
        }

        return response()->json(["code" => 200, "message" => "{$i} Message has been recalled"]);

    }

    public function status()
    {
        $waitingMessages = [];
        $allWhatsappNo   = config("apiwha.instances");
        if (!empty($allWhatsappNo)) {
            foreach ($allWhatsappNo as $no => $dataInstance) {
                $no    = ($no == 0) ? $dataInstance["number"] : $no;
                $limit = (new ChatApi)->waitingLimit($no);
                if ($limit > config("apiwha.message_queue_limit")) {
                    $waitingMessages[$no] = $limit;
                }
            }
        }

        // check that if limit overflow then show notification

        if (!empty($waitingMessages)) {
            $msg = "Following number reached the queue limit : " . "</br>";
            foreach ($waitingMessages as $k => $wm) {
                $msg .= $k . " : " . $wm . "</br>";
            }
            return response()->json(["code" => 500, "data" => $waitingMessages, "message" => $msg]);
        }

        return response()->json(["code" => 200, "data" => [], "message" => "OK"]);
    }

}
