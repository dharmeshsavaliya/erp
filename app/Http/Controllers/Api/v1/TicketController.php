<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use Illuminate\Support\Facades\Validator;
use App\Tickets;

class TicketController extends Controller
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
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'email' => 'required|email',
            //'order_no' => ['required', 'exists:orders,order_id'],
            'type_of_inquiry' => 'required',
            //'country' => 'required',
            'subject' => 'required|max:80',
            'message' => 'required'
            //'source_of_ticket' => 'in:live_chat,customer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check the errors in validation!', 'errors' => $validator->errors()], 400);
        }

        $data = $request->all();
        $data['ticket_id'] = "T" . date("YmdHis");
        $data['status_id'] = 1;
        $success = Tickets::create($data);
        if (!is_null($success)) {
            return response()->json(['status' => 'success', 'data' => ["id" => $data['ticket_id']], 'message' => 'Ticket #' . $data['ticket_id'] . ' created successfully'], 200);
        }
        return response()->json(['status' => 'success', 'message' => 'Unable to create ticket'], 500);
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
    public function sendTicketsToCustomers(request $request)
    {
        $Validator = Validator::make($request->all(), [
            'website' => 'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $Validator->errors()], 400);
        }
        if(empty($request->email) && empty($request->ticket_id)){
            return response()->json(['status' => 'failed', 'message' => 'Please input either email or ticket_id !', ], 400);            
        }
        $tickets = Tickets::select('tickets.*','ts.name as status')->where('source_of_ticket',$request->website);
        if($request->email!=null){
            $tickets->where('email',$request->email);
        }
        if($request->ticket_id!=null){
            $tickets->where('ticket_id',$request->ticket_id);
        }
        $per_page='';
        if(!empty($request->per_page)){
            $per_page=$request->per_page;
        }
        $tickets = $tickets->join('ticket_statuses as ts','ts.id', 'tickets.status_id')->paginate($per_page);
        if(empty($tickets)){
            return response()->json(['status' => 'failed', 'message' => 'Tickets not found for customer !', ], 404);    
        }
        return response()->json(['status' => 'success', 'tickets' => $tickets ], 200);
    }
}
