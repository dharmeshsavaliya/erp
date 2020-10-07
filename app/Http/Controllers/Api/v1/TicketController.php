<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'email' => 'required|email',
            'order_no' => ['required', 'exists:orders,order_id'],
            'type_of_inquiry' => 'required',
            'country' => 'required',
            'subject' => 'required|max:80',
            'message' => 'required',
            'source_of_ticket' => 'in:live_chat,customer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check the errors in validation!', 'errors' => $validator->errors()], 400);
        }
        $success = Tickets::create($request->all());
        if (!is_null($success)) {
            return response()->json(['status' => 'success', 'message' => 'Ticket created successfully'], 200);
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
}
