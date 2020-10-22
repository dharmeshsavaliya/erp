<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PushFcmNotification;
use App\StoreWebsite;
use Auth;

class FcmNotificationController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        
        $query = PushFcmNotification::query();

		if($request->id){
			$query = $query->where('id', $request->id);
		}
		if($request->term){
            $query = $query->where('title', 'LIKE','%'.$request->term.'%')
                    ->orWhere('token', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('url', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('created_by', 'LIKE', '%'.$request->term.'%');
		}

        $data = $query->leftJoin('users as usr','usr.id','push_fcm_notifications.created_by')
        ->select('push_fcm_notifications.*','usr.name as username')->orderBy('id', 'DESC')->paginate(25)->appends(request()->except(['page']));
		if ($request->ajax()) {
            return response()->json([
                'tbody' => view('pushfcmnotification.partials.list-notification', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$data->render(),
                'count' => $data->total(),
            ], 200);
        }
		return view('pushfcmnotification.index', compact('data'))
			->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$StoreWebsite = StoreWebsite::select('id','website')->groupBy('website')->get();
		return view('pushfcmnotification.create', compact('StoreWebsite'));
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
			'title' => 'required',
			'url' => 'required|exists:store_websites,website',
			'token' => 'required',
            'body' => 'required|string',
		]);
        $StoreWebsiteId = StoreWebsite::where('website',$request->input('url'))->first()->id; 
        $input = $request->all();
        $input['store_website_id'] = $StoreWebsiteId;
        $input['created_by'] = Auth::id();
		$insert = PushFcmNotification::create($input);

		return redirect()->route('pushfcmnotification.list')->with('success', 'Notification created successfully');
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
    /* public function edit($id)
    {
        $StoreWebsite = StoreWebsite::select('id','website')->groupBy('website')->get();
        $ReferralProgram = PushFcmNotification::where('id',$id)->first();
		return view('referralprogram.edit', compact('StoreWebsite','ReferralProgram'));
    } */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /* public function update(Request $request)
    {
        $this->validate($request, [
			'name' => 'required',
			'uri' => 'required|exists:store_websites,website',
			'credit' => 'required|integer',
            'currency' => 'required|string',
            'lifetime_minutes'=>'integer',
        ]);
        $id = $request->input('id');
        $StoreWebsiteId = StoreWebsite::where('website',$request->input('uri'))->first()->id; 
        $input = $request->except('_token');
        $input['store_website_id'] = $StoreWebsiteId;
		$insert = PushFcmNotification::where('id',$id)->update($input);

		return redirect()->back()->with('success', 'Notification updated successfully');
    } */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $PushFcmNotification = PushFcmNotification::find($id);
		$PushFcmNotification->delete();

		return redirect()->route('pushfcmnotification.list')
			->with('success', 'Notification deleted successfully');
    }
}
