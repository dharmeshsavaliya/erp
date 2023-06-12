<?php

namespace Modules\ChatBot\Http\Controllers;

use App\BugTracker;
use App\ChatMessage;
use App\ChatbotCategory;
use App\Customer;
use App\CustomerCharity;
use App\DeveloperTask;
use App\Document;
use App\Email;
use App\Learning;
use App\Models\DialogflowEntityType;
use App\Models\TmpReplay;
use App\Old;
use App\Order;
use App\PaymentReceipt;
use App\PublicKey;
use App\SiteDevelopment;
use App\SocialStrategy;
use App\StoreSocialContent;
use App\SuggestedProduct;
use App\Supplier;
use App\Task;
use App\TestCase;
use App\TestSuites;
use App\Tickets;
use App\Uicheck;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        if (!empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where('cr.question', 'like', '%' . $search . '%')->orWhere('cr.answer', 'Like', '%' . $search . '%');
            });
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (!empty($unreplied_msg)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where('cm1.message', null);
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where('chat_messages.approved', $status);
            });
        }

        if (request('unread_message') == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->where('cr.is_read', 0);
            });
        }

        if (request('message_type') != null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (request('message_type') == 'email') {
                    $q->where('chat_messages.message_type', 'email');
                    $q->orWhere('chat_messages.is_email', '>', 0);
                }
                if (request('message_type') == 'task') {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
                if (request('message_type') == 'dev_task') {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (request('message_type') == 'ticket') {
                    $q->orWhere('chat_messages.ticket_id', '>', 0);
                }
            });
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (in_array('customer', request('search_type'))) {
                    $q->where('chat_messages.customer_id', '>', 0);
                }
                if (in_array('vendor', request('search_type'))) {
                    $q->orWhere('chat_messages.vendor_id', '>', 0);
                }
                if (in_array('supplier', request('search_type'))) {
                    $q->orWhere('chat_messages.supplier_id', '>', 0);
                }
                if (in_array('dev_task', request('search_type'))) {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (in_array('task', request('search_type'))) {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
            });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select(['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer',
            'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name', 'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject'])
            ->orderByRaw("cr.id DESC, chat_messages.id DESC")
            ->paginate(20);
        // dd($pendingApprovalMsg);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (!$allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        $reply_categories = \App\ReplyCategory::with('approval_leads')->orderby('name')->get();

        if ($request->ajax()) {
            $tml = (string)view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();

        //dd($pendingApprovalMsg);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType'));
    }

    public function approve()
    {
        $id = request('id');

        $messageId = 0;

        if ($id > 0) {
            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $id]);

            $chatMEssage = \app\ChatMessage::find($id);

            $type = '';
            if ($chatMEssage->task_id > 0) {
                $type = 'task';
            } elseif ($chatMEssage->developer_tasK_id > 0) {
                $type = 'issue';
            } elseif ($chatMEssage->vendor_id > 0) {
                $type = 'vendor';
            } elseif ($chatMEssage->user_id > 0) {
                $type = 'user';
            } elseif ($chatMEssage->supplier_id > 0) {
                $type = 'supplier';
            } elseif ($chatMEssage->customer_id > 0) {
                $type = 'customer';
            } elseif ($chatMEssage->message_type == 'email') {
                $type = 'email';
                $messageId = $id;
            }

            app(\App\Http\Controllers\WhatsAppController::class)->approveMessage($type, $myRequest, $messageId);
        }

        return response()->json(['code' => 200, 'message' => 'Messsage Send Successfully']);
    }

    /**
     * [removeImages description]
     *
     * @return [type] [description]
     */
    public function removeImages(Request $request)
    {
        $deleteImages = $request->get('delete_images', []);

        if (!empty($deleteImages)) {
            foreach ($deleteImages as $image) {
                [$mediableId, $mediaId] = explode('_', $image);
                if (!empty($mediaId) && !empty($mediableId)) {
                    \Db::statement('delete from mediables where mediable_id = ? and media_id = ? limit 1', [$mediableId, $mediaId]);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Image has been removed now']);
    }

    public function attachImages(Request $request)
    {
        $id = $request->get('chat_id', 0);

        $data = [];
        $ids = [];
        $images = [];

        if ($id > 0) {
            // find the chat message
            $chatMessages = ChatMessage::where('id', $id)->first();

            if ($chatMessages) {
                $chatsuggestion = $chatMessages->suggestion;
                if ($chatsuggestion) {
                    $data = SuggestedProduct::attachMoreProducts($chatsuggestion);
                    $code = 500;
                    $message = 'Sorry no images found!';
                    if (count($data) > 0) {
                        $code = 200;
                        $message = 'More images attached Successfully';
                    }

                    return response()->json(['code' => $code, 'data' => $data, 'message' => $message]);
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Sorry , There is not avaialble images']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'It looks like there is not validate id']);
    }

    public function forwardToCustomer(Request $request)
    {
        $customer = $request->get('customer');
        $images = $request->get('images');

        if ($customer > 0 && !empty($images)) {
            $params = request()->all();
            $params['user_id'] = \Auth::id();
            $params['is_queue'] = 0;
            $params['status'] = \App\ChatMessage::CHAT_MESSAGE_APPROVED;
            $params['customer_ids'] = is_array($customer) ? $customer : [$customer];
            $groupId = \DB::table('chat_messages')->max('group_id');
            $params['group_id'] = ($groupId > 0) ? $groupId + 1 : 1;
            $params['images'] = $images;

            \App\Jobs\SendMessageToCustomer::dispatch($params);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message forward to customer(s)']);
    }

    public function resendToBot(Request $request)
    {
        $chatId = $request->get('chat_id');

        if (!empty($chatId)) {
            $chatMessage = \App\ChatMessage::find($chatId);
            if ($chatMessage) {
                $customer = $chatMessage->customer;
                if ($customer) {
                    $params = $chatMessage->getAttributes();

                    \App\Helpers\MessageHelper::whatsAppSend($customer, $chatMessage->message, null, $chatMessage);

                    $data = [
                        'model' => \App\Customer::class,
                        'model_id' => $customer->id,
                        'chat_message_id' => $chatId,
                        'message' => $chatMessage->message,
                        'status' => 'started',
                    ];
                    $chat_message_log_id = \App\ChatbotMessageLog::generateLog($data);
                    $params['chat_message_log_id'] = $chat_message_log_id;
                    \App\Helpers\MessageHelper::sendwatson($customer, $chatMessage->message, null, $chatMessage, $params, false, 'customer');

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent Successfully']);
                }
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Message not exist in record']);
    }

    public function updateReadStatus(Request $request)
    {
        $chatId = $request->get('chat_id');
        $value = $request->get('value');

        $reply = \App\ChatbotReply::find($chatId);

        if ($reply) {
            $reply->is_read = $value;
            $reply->save();

            $status = ($value == 1) ? 'read' : 'unread';

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Marked as ' . $status]);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'Message not exist in record']);
    }

    public function stopReminder(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');

        if ($type == 'developer_task') {
            $task = \App\DeveloperTask::find($id);
        } else {
            $task = \App\Task::find($id);
        }

        if ($task) {
            $task->frequency = 0;
            $task->save();

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Reminder turned off']);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'No task found']);
    }

    public function updateEmailAddress(Request $request)
    {
        $chat_id = $request->chat_id;
        $fromemail = $request->fromemail;
        $toemail = $request->toemail;
        $ccemail = $request->ccemail;
        if ($chat_id > 0) {
            ChatMessage::where('id', $chat_id)
                ->update(['from_email' => $fromemail, 'to_email' => $toemail, 'cc_email' => $ccemail]);

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Record Updated Successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function updateSimulator(Request $request)
    {
        $requestMessage = \Request::create('/', 'GET', [
            'limit' => 1,
            'object' => $request->object,
            'object_id' => $request->objectId,
            'order' => 'asc',
            'for_simulator' => true
        ]);
        $response = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);
        if ($response[0] && $response[0]['id'] > 0) {
            $id = $response[0]['id'];
            $update_chat_message = ChatMessage::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            return response()->json(['code' => 200, 'data' => [$update_chat_message, $id], 'messages' => 'Auto simulator on successfully']);

        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function chatBotReplayList(Request $request)
    {
    }

    public function sendSuggestedMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $tmp_replay_id = $request->get('tmp_reply_id');
        $value = $request->get('value');
        $reply = TmpReplay::find($tmp_replay_id);
        if ($reply) {
            if ($value == 1) {
                $reply['is_approved'] = 1;
            } else {
                $reply['is_reject'] = 1;
            }
            $reply->save();
            if ($value == 1) {
                $chatMessage = ChatMessage::find($reply->chat_message_id);
                $requestData = [
                    'chat_id' => $chatMessage->id,
                    'customer_id' => $chatMessage->customer_id,
                    'supplier_id' => $chatMessage->supplier_id,
                    'vendor_id' => $chatMessage->vendor_id,
                    'task_id' => $chatMessage->task_id,
                    'is_email' => $chatMessage->is_email,
                    'erp_user' => $chatMessage->erp_user,
                    'status' => $chatMessage->status,
                    'assigned_to' => $chatMessage->assigned_to,
                    'lawyer_id' => $chatMessage->lawyer_id,
                    'case_id' => $chatMessage->case_id,
                    'blogger_id' => $chatMessage->blogger_id,
                    'quicksell_id' => $chatMessage->quicksell_id,
                    'old_id' => $chatMessage->old_id,
                    'site_development_id' => $chatMessage->site_development_id,
                    'social_strategy_id' => $chatMessage->social_strategy_id,
                    'store_social_content_id' => $chatMessage->store_social_content_id,
                    'payment_receipt_id' => $chatMessage->store_social_content_id,
                    'developer_task_id' => $chatMessage->developer_task_id,
                    'ticket_id' => $chatMessage->ticket_id,
                    'user_id' => $chatMessage->user_id,
                    'send_by_simulator' => true,
                ];
                $requestData['message'] = $reply->suggested_replay;
                $requestData = \Request::create('/', 'POST', $requestData);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, $reply->type);
            }
            $status = ($value == 1) ? 'send message Successfully' : 'Suggested message rejected';

            return response()->json(['code' => 200, 'data' => [], 'messages' => $status]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Suggested replay does not exist in record']);
        }

    }
}
