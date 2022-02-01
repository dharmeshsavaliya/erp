<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\OrderHelper;
use App\OrderStatus;
use App\OrderReport;
use App\Order;
use App\Refund;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class OrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'status_id' => 'required',
        'completion_date' => 'required'
      ]);

      $report = new OrderReport;

      $report->status_id = $request->status_id;
      $report->user_id = Auth::id();

      if ($request->order_id)
        $report->order_id = $request->order_id;
      else
        $report->customer_id = $request->customer_id;

      $report->completion_date = $request->completion_date;

      $report->save();

      // $order = Order::find($report->order_id);
      //
      // if ($order->sales_person) {
      //   NotificationQueueController::createNewNotification([
        //      'type' => 'button',
        //      'message' => $order->client_name . ' - ' . $report->status,
        //      'timestamps' => ['+0 minutes'],
        //      'model_type' => Order::class,
        //      'model_id' =>  $report->order_id,
        //      'user_id' => \Auth::id(),
        //      'sent_to' => $order->sales_person,
        //      'role' => '',
        //  ]);
      // }

        // NotificationQueueController::createNewNotification([
        //  'message' => $order->client_name . ' - ' . $report->status,
        //  'timestamps' => ['+0 minutes'],
        //  'model_type' => Order::class,
        //  'model_id' =>  $report->order_id,
        //  'user_id' => \Auth::id(),
        //  'sent_to' => '',
        //  'role' => 'Admin',
        // ]);

      return redirect()->back()->with('message', 'Order action was created successfully');
    }

    public function statusStore(Request $request) {
        $this->validate($request, [
            'status'    => 'required'
        ]);

        $status = new OrderStatus;

        $status->status = $request->status;

        $status->save();

        return redirect()->back()->with('message', 'Order status was created successfully');
    }

    public function orderRefundStatusMessage(Request $request){

      $page = $request->page;
      $paginate = 10;
      //return 'asdfsfg';
      $orders = DB::table('orders')
      ->join('customers','orders.customer_id','customers.id')
      ->select('orders.id', 'orders.is_flag', 'customer_id','orders.created_at as date', DB::raw("'order' as type"),'customers.phone', 'customers.name', 'customers.email','order_status_id','estimated_delivery_date');

      if($request->order_id && $request->order_id != null){
            $orders->where('orders.id', $request->order_id);
        }
      if($request->customer_name && $request->customer_name != null){
         $orders->where('customers.name','LIKE','%'.$request->customer_name.'%');
        }

        if($request->flt_order_status && $request->flt_order_status != null){
            $orders->where('order_status_id',$request->flt_order_status);
        }
        if($request->flt_estimate_date && $request->flt_estimate_date != null){
            $orders->where('estimated_delivery_date','LIKE',$request->flt_estimate_date);
        }


      $order_n_refunds = DB::table('return_exchanges')
      ->join('customers','return_exchanges.customer_id','customers.id')
      ->select('return_exchanges.id', 'is_flag','customer_id','return_exchanges.created_at as date', DB::raw("'refund' as type"),'customers.phone','customers.name','customers.email',DB::raw("'' as order_status_id"),DB::raw("return_exchanges.est_completion_date as estimated_delivery_date"));
         if($request->order_id && $request->order_id != null){
             $order_n_refunds->where('return_exchanges.id', $request->order_id);
         }
        if($request->customer_name && $request->customer_name != null){
         $order_n_refunds->where('customers.name','LIKE','%'.$request->customer_name.'%');
        }
      $order_n_refunds = $order_n_refunds->union($orders)->orderBy('date',"DESC")->get();
        // dd($order_n_refunds);
      $orderStatusList = OrderStatus::all();
      // $slice = array_slice($refunds->toArray(), $paginate * ($page - 1), $paginate);
      // $order_n_refunds = Paginator::make($slice, count($refunds), $paginate);
      $order_status_list = OrderHelper::getStatus();
      return view("orders.status-history", compact('order_n_refunds','order_status_list','orderStatusList'));
    }

    public function setFlag(Request $request)
    {
        $return_exchanges = Order::find($request->id);

        if ($return_exchanges->is_flag == 0) {
            $return_exchanges->is_flag = 1;
        } else {
            $return_exchanges->is_flag = 0;
        }

        $return_exchanges->save();

        return response()->json(['is_flagged' => $return_exchanges->is_flag]);
    }

    public function lastCommunicated($type = "any")
    {
        $q = $this->chatMessage()->whereNotIn("status", ['7', '8', '9', '10']);

        if (in_array($type, ['unread', 'unapproved','chatbot_unapproved'])) {
            if($type == 'unread')  {
              $type = 0;
            }else if($type == 'chatbot_unapproved')  {
              $type = 11;
            }else{
              $type = 1;
            }
            $q = $q->where('chat_messages.status', $type);
        }else if ($type == "last_communicated") {
            $q = $q->where('chat_messages.message','!=', '')->where(function($q){
              $q->where("group_id","<",0)->orWhere("group_id","")->orWhereNull("group_id");
            });
        }else if ($type == "last_received") {
            $q = $q->where('chat_messages.number','=', $this->phone)->where('chat_messages.message','!=', '');
        } 

        return $q->orderBy("created_at", "DESC")->first();
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
