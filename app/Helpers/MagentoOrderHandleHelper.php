<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProductAi;

use App\Http\Controllers\ProductInventoryController;
use App\Helpers\OrderHelper;
use App\StoreWebsiteOrder;
use App\Customer;
use App\Product;
use App\Colors;
use App\OrderProduct;
use App\AutoReply;
use Carbon\Carbon;
use App\ChatMessage;
use App\CommunicationHistory;
use App\Order;
use App\ProductSizes;
use App\Mails\Manual\OrderConfirmation;
use App\Email;
use seo2websites\MagentoHelper\MagentoHelperv2 as MagentoHelper;

class MagentoOrderHandleHelper extends Model
{

    
    /**
     * Create magento order 
     * @param Order [ object ], Website [ object ]
     * @return response
     */
    public static function createOrder( $orders , $website ){
        
        try {
            if (isset($orders->items)) {

                \Log::info("Item found");

                $totalOrders = $orders->items;
                foreach ($totalOrders as $order) {
                    //Checking in order table
                    $checkIfOrderExist = StoreWebsiteOrder::where('platform_order_id', $order->increment_id)->where('website_id', $website->id)->first();
                    \Log::info($checkIfOrderExist . " Order not exist");
                    //Checkoing in Website Order Table
                    if ($checkIfOrderExist) {
                        continue;
                    }

                    $balance_amount = 0;
                    $firstName      = isset($order->customer_firstname) ? $order->customer_firstname : "N/A";
                    $lastName       = isset($order->customer_lastname) ? $order->customer_lastname : "N/A";

                    $full_name      = $firstName . ' ' . $lastName;
                    $customer_phone = '';

                    $customer = Customer::where('email', $order->customer_email)->where('store_website_id', $website->id)->first();
                    if (!$customer) {
                        $customer = new Customer;
                    }

                    $customer->name             = $full_name;
                    $customer->email            = $order->customer_email;
                    $customer->address          = $order->billing_address->street[0];
                    $customer->city             = $order->billing_address->city;
                    $customer->country          = $order->billing_address->country_id;
                    $customer->pincode          = $order->billing_address->postcode;
                    $customer->pincode          = $order->billing_address->postcode;
                    $customer->store_website_id = $website->id;
                    $customer->save();

                    $customer_id    = $customer->id;
                    $order_status   = OrderHelper::$orderRecieved;
                    $payment_method = '';

                    if ($order->payment->method == 'paypal') {
                        if ($order->state == 'processing') {
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }

                        $payment_method = 'paypal';
                    } elseif ($order->payment->method == 'banktransfer') {
                        if ($order->state == 'processing') {
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }
                        $payment_method = 'banktransfer';
                    } elseif ($order->payment->method == 'cashondelivery') {
                        if ($order->state == 'processing') {
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }
                        $payment_method = 'cashondelivery';
                    }
                    $magentoId = $order->increment_id;
                    $id        = \DB::table('orders')->insertGetId(
                        array(
                            'customer_id'     => $customer_id,
                            'order_id'        => $order->increment_id,
                            'order_type'      => 'online',
                            'order_status'    => $order_status,
                            'order_status_id' => $order_status,
                            'payment_mode'    => $payment_method,
                            'order_date'      => $order->created_at,
                            'client_name'     => $order->billing_address->firstname . ' ' . $order->billing_address->lastname,
                            'city'            => $order->billing_address->city,
                            'advance_detail'  => 0,
                            'contact_detail'  => $order->billing_address->telephone,
                            'balance_amount'  => $balance_amount,
                            'created_at'      => $order->created_at,
                            'updated_at'      => $order->created_at,
                        )
                    );

                    \Log::info("Order id : " . $id);

                    $items = $order->items;
                    foreach ($items as $item) {
                        if (round($item->price) > 0) {
                            if ($item->product_type == 'configurable') {
                                //Pending
                                $size = '';
                            } else {
                                $size = '';
                            }
                            $skuAndColor = MagentoHelper::getSkuAndColor($item->sku);
                            \Log::info("skuAndColor : " . json_encode($skuAndColor));
                            
                            DB::table('order_products')->insert(
                                array(
                                    'order_id'      => $id,
                                    'product_id'    => !empty($skuAndColor['product_id']) ? $skuAndColor['product_id'] : null,
                                    'sku'           => $skuAndColor['sku'],
                                    'product_price' => round($item->price),
                                    'qty'           => round($item->qty_ordered),
                                    'size'          => $size,
                                    'color'         => $skuAndColor['color'],
                                    'created_at'    => $order->created_at,
                                    'updated_at'    => $order->created_at,
                                )
                            );
                        }
                    }

                    $orderSaved = Order::find($id);
                    if ($order->payment->method == 'cashondelivery') {
                        $product_names = '';
                        foreach (OrderProduct::where('order_id', $id)->get() as $order_product) {
                            $product_names .= $order_product->product ? $order_product->product->name . ", " : '';
                        }

                        $delivery_time = $orderSaved->estimated_delivery_date ? Carbon::parse($orderSaved->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($orderSaved->order_date)->addDays(15)->format('d \of\ F');

                        $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();

                        $auto_message = preg_replace("/{product_names}/i", $product_names, $auto_reply->reply);
                        $auto_message = preg_replace("/{delivery_time}/i", $delivery_time, $auto_message);

                        $params = [
                            'number'      => null,
                            'user_id'     => 6,
                            'approved'    => 1,
                            'status'      => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message'     => $auto_message,
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;

                        $params['message'] = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;

                        $chat_message = ChatMessage::create($params);

                        CommunicationHistory::create([
                            'model_id'   => $orderSaved->id,
                            'model_type' => Order::class,
                            'type'       => 'initial-advance',
                            'method'     => 'whatsapp',
                        ]);
                    } elseif ($orderSaved->order_status_id == \App\Helpers\OrderHelper::$prepaid && $results['state'] == 'processing') {
                        $params = [
                            'number'      => null,
                            'user_id'     => 6,
                            'approved'    => 1,
                            'status'      => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message'     => AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply,
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;

                        CommunicationHistory::create([
                            'model_id'   => $orderSaved->id,
                            'model_type' => Order::class,
                            'type'       => 'online-confirmation',
                            'method'     => 'whatsapp',
                        ]);
                    }

                    if ($order->state != 'processing' && $order->payment->method != 'cashondelivery') {
                        $autoReplyMsg = AutoReply::where('type', 'auto-reply')->where('keyword', 'order-payment-not-processed')->first();
                        $params       = [
                            'number'      => null,
                            'user_id'     => 6,
                            'approved'    => 1,
                            'status'      => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message'     => ($autoReplyMsg) ? $autoReplyMsg->reply : "",
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;
                    }

                    //Store Order Id Website ID and Magento ID

                    $websiteOrder                    = new StoreWebsiteOrder();
                    $websiteOrder->website_id        = $website->id;
                    $websiteOrder->status_id         = $order_status;
                    $websiteOrder->order_id          = $orderSaved->id;
                    $websiteOrder->platform_order_id = $magentoId;
                    $websiteOrder->save();

                    $customer = $orderSaved->customer;

                    Mail::to($customer->email)->send(new OrderConfirmation($orderSaved));
                    $view   = (new OrderConfirmation($orderSaved))->render();
                    $params = [
                        'model_id'        => $customer->id,
                        'model_type'      => Customer::class,
                        'from'            => 'customercare@sololuxury.co.in',
                        'to'              => $customer->email,
                        'subject'         => "New Order # " . $orderSaved->order_id,
                        'message'         => $view,
                        'template'        => 'order-confirmation',
                        'additional_data' => $orderSaved->id,
                    ];
                    Email::create($params);

                    \Log::info("Order is finished" . json_encode($websiteOrder));
                }
                /**Ajay singh */
                $orders = OrderProduct::with('order')->whereHas('order',function($query){
                    $query->whereIn('order_status_id',[1,13]);
                })->get();
                foreach($orders as $order){
                    // if order 1 and 13
                    $size = $order->size;
                    $total_size = $order->qty;
                    $product_id = $order->product_id;
                    $productSizes = ProductSizes::where('product_id', $product_id)->where('size', $size)->get();
                    if($productSizes->count() > 0){
                        $size = 0;
                        foreach($productSizes as $product){
                            $size = $size + $product->quantity;
                        }
                        if($total_size >= $size)
                        {
                            $product = Product::find($product_id);
                            //make product outofstock
                            $ProductInventoryController = ProductInventoryController::magentoSoapUpdateStock($product,0);
                        }
                    }
                }
                
                /**Ajay singh */
                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
        return false;

    }
}
