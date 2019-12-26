<?php

namespace App\Http\Controllers;

use App\Customer;
use App\DeveloperTask;
use App\User;
use App\Vendor;
use App\Supplier;
use App\Task;
use App\Old;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatMessagesController extends Controller
{
    /**
     * Load more messages from chat_messages
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMoreMessages(Request $request)
    {
        // Set variables
        $limit = $request->get("limit", 3);
        $loadAttached = $request->get("load_attached", 0);
        $loadAllMessages = $request->get("load_all", 0);

        // Get object (customer, vendor, etc.)
        switch ($request->object) {
            case 'customer':
                $object = Customer::find($request->object_id);
                break;
            case 'user':
                $object = User::find($request->object_id);
                break;
            case 'vendor':
                $object = Vendor::find($request->object_id);
                break;
            case 'task':
                $object = Task::find($request->object_id);
                break;
            case 'developer_task':
                $object = DeveloperTask::find($request->object_id);
                break;
            case 'supplier':
                $object = Supplier::find($request->object_id);
                break;
            case 'old':
                $object = Old::find($request->object_id);
                break;
            default:
                $object = Customer::find($request->object);
        }

        // Set raw where query
        $rawWhere = "(message!='' or media_url!='')";

        // Do we want all?
        if ($loadAllMessages == 1) {
            $loadAttached = 1;
            $rawWhere = "1=1";
        }

        // Get chat messages
        $chatMessages = $object
            ->whatsappAll()
            ->whereRaw($rawWhere)
            ->where('status', '!=', 10)
            ->skip(0)->take($limit);

        $loadType = $request->get('load_type');
        switch ($loadType) {
            case 'text':
                $chatMessages = $chatMessages->whereNotNull("message")
                                             ->whereNull("media_url")
                                             ->whereRaw('id not in (select mediable_id from mediables WHERE mediable_type LIKE "App%ChatMessage")');
                break;
            case 'images':
                $chatMessages = $chatMessages->whereRaw("(media_url is not null or id in (
                    select 
                        mediable_id 
                    from 
                        mediables 
                        join media on id = media_id and extension != 'pdf'
                    WHERE 
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                break;
            case 'pdf':
                $chatMessages = $chatMessages->whereRaw("(id in (
                    select 
                        mediable_id 
                    from 
                        mediables 
                        join media on id = media_id and extension = 'pdf'
                    WHERE 
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                break;
        }
        $chatMessages = $chatMessages->get();
        // Set empty array with messages
        $messages = [];

        // Loop over ChatMessages
        foreach ($chatMessages as $chatMessage) {
            // Create empty media array
            $media = [];
            $mediaWithDetails = [];
            $productId = null;



            // Check for media
            if ($loadAttached == 1 && $chatMessage->hasMedia(config('constants.media_tags'))) {
                foreach ($chatMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                    // Supplier checkbox
                    if (in_array($request->object, ["supplier"])) {
                        $tempImage = [
                            'key' => $image->getKey(),
                            'image' => $image->getUrl(),
                            'product_id' => '',
                            'special_price' => '',
                            'size' => ''
                        ];

                        $imageKey = $image->getKey();
                        $mediableType = "Product";

                        $productImage = \App\Product::with('Media')
                            ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $imageKey AND mediables.mediable_type LIKE '%$mediableType%')")
                            ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                        if ($productImage) {
                            $tempImage[ 'product_id' ] = $productImage->id;
                            $tempImage[ 'special_price' ] = $productImage->price_inr_special;
                            $tempImage[ 'supplier_initials' ] = $this->getSupplierIntials($productImage->supplier);
                            $tempImage[ 'size' ] = $this->getSize($productImage);
                        }

                        $mediaWithDetails[] = $tempImage;
                    } else {
                        // Check for product
                        if (isset($image->id)) {
                            $product = DB::table('mediables')->where('mediable_type', 'App\Product')->where('media_id', $image->id)->get(['mediable_id'])->first();

                            if ($product != null) {
                                $productId = $product->mediable_id;
                            } else {
                                $productId = null;
                            }
                        }

                        // Get media URL
                        $media[] = [
                            'image' => $image->getUrl(),
                            'product_id' => $productId
                        ];
                    }

                }
            }

            $messages[] = [
                'id' => $chatMessage->id,
                'type' => $request->object,
                'inout' => $chatMessage->number != $object->phone ? 'out' : 'in',
                'message' => htmlentities($chatMessage->message),
                'media_url' => $chatMessage->media_url,
                'datetime' => $chatMessage->created_at,
                'media' => is_array($media) ? $media : null,
                'mediaWithDetails' => is_array($mediaWithDetails) ? $mediaWithDetails : null,
                'product_id' => !empty($productId) ? $productId : null,
                'status' => $chatMessage->status,
                'resent' => $chatMessage->resent,
                'customer_id' => $chatMessage->customer_id,
                'approved' => $chatMessage->approved,
                'error_status' => $chatMessage->error_status,
                'is_queue' => $chatMessage->is_queue
            ];
        }

        // Return JSON
        return response()->json([
            'messages' => $messages
        ]);
    }

    public function getSupplierIntials($string)
    {

        $expr = '/(?<=\s|^)[a-z]/i';
        preg_match_all($expr, $string, $matches);

        return strtoupper(implode('', $matches[ 0 ]));
    }

    public function getSize($productImage)
    {
        $size = null;

        if ($productImage->size != null) {
            $size = $productImage->size;
        } else {
            $size = (string)$productImage->lmeasurement . ', ' . (string)$productImage->hmeasurement . ', ' . (string)$productImage->dmeasurement;
        }

        return $size;

    }
}
