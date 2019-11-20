<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\ChatMessage;
use App\Customer;
use App\ImQueue;

class InstantMessagingController extends Controller
{
    /**
     * Send Message Queue Result For API Call
     *
     * @param $client
     * @param $numberFrom
     * @return void
     */
    public function getMessage($client, $numberFrom, Request $request)
    {
        // Get client class
        $clientClass = '\\App\\Marketing\\' . ucfirst($client) . 'Config';

        // Check credentials
        $whatsappConfig = $clientClass::where('number', $numberFrom)->first();

        // Nothing found
        if ($whatsappConfig == null || Crypt::decrypt($whatsappConfig->password) != $request->token) {
            $message = ['error' => 'Invalid token'];
            return json_encode($message, 400);
        }

        // Get next messsage from queue
        $queue = ImQueue::select('id', 'text', 'image', 'number_to')
            ->where('im_client', $client)
            ->where('number_from', $numberFrom)
            ->whereNull('sent_at')
            ->where(function ($query) {
                $query->where('send_after', '<', Carbon::now())
                    ->orWhereNull('send_after');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        // Return error if no message is found
        if ($queue == null) {
            $message = ['error' => 'The queue is empty'];
            return json_encode($message, 400);
        }

        // Set output
        if ($queue->image != null) {
//            $output = ['queueNumber' => $queue->id, 'phone' => $queue->number_to, 'body' => $queue->image, 'filename' => urlencode(substr($queue->image, strrpos($queue->image, '/') + 1)), 'caption' => $queue->text];
            $output = ['queueNumber' => $queue->id, 'phone' => '31629987287', 'body' => $queue->image, 'filename' => urlencode(substr($queue->image, strrpos($queue->image, '/') + 1)), 'caption' => $queue->text];
        } else {
//            $output = ['queueNumber' => $queue->id, 'phone' => $queue->number_to, 'body' => $queue->text];
            $output = ['queueNumber' => $queue->id, 'phone' => '31629987287', 'body' => $queue->text];
        }

        // Return output
        if (isset($output)) {
            return json_encode($output, 200);
        } else {
            return json_encode(['error' => 'The queue is empty'], 400);
        }
    }

    public function processWebhook(Request $request)
    {
        // Get raw JSON
        $receivedJson = json_decode($request->getContent());

        // Valid json?
        if ($receivedJson !== null && is_object($receivedJson)) {
            // Get message from queue
            $imQueue = ImQueue::where(['id' => $receivedJson->queueNumber])->first();

            // message found in the queue
            //if ($imQueue !== null && empty($imQueue->sent_at)) {
            if ( $imQueue !== null ) {
                // Update status in im_queues
                $imQueue->sent_at = $receivedJson->sent == true ? date('Y-m-d H:i:s', Carbon::now()->timestamp) : '2002-20-02 20:02:00';
                $imQueue->save();

                // Find customer for this number
                $customer = Customer::where('phone', '=', $imQueue->number_to)->first();

                // Add to chat_messages if we have a customer
                $params = [
                    'unique_id' => $receivedJson->id,
                    'message' => $imQueue->text,
                    'customer_id' => $customer != null ? $customer->id : null,
                    'approved' => 1,
                    'status' => 2
                ];

                // Create chat message
                $chatMessage = ChatMessage::create($params);

                // TODO: Attach images to chatMessage
            }
        }

        // Return json ack
        return json_encode('ack', 200);
    }
}
