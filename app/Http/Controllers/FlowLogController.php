<?php

namespace App\Http\Controllers;

use App\Loggers\FlowLog;

use App\Loggers\FlowLogMessages;
use App\Setting;
use App\User;
use App\LogKeyword;
use File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Illuminate\Support\Carbon;

class FlowLogController extends Controller
{
    public $channel_filter = [];
    public function index(Request $request)
    {

        $page = isset($request->page) ? $request->page : 1;

        $logs = FlowLog::orderby('updated_at', 'desc')->select(
            [
                'flow_logs.*',
                'flows.flow_name', 
                'flows.flow_description', 
                'flows.store_website_id', 
                'store_websites.website', 
                'flow_log_messages.modalType',
                'flow_log_messages.created_at',
                'customers.name as lead_name'
            ])
        ->join('flows','flows.id', 'flow_logs.flow_id')
        ->join('flow_log_messages', 'flow_log_messages.flow_log_id', 'flow_logs.id')
        ->leftJoin('customers', 'customers.id', '=', 'flow_log_messages.leads')
        ->join('store_websites', 'flows.store_website_id', 'store_websites.id');

        // dd($logs->limit(10)->get());

        if ($request->term || $request->created_at) {

            
            if (request('term') != null) {
                $logs = $logs->where(function ($query) use ($request) {
                    $query->where('flows.flow_name', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('flow_logs.messages', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('flows.flow_description', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('flow_log_messages.modalType', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('customers.name', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('store_websites.website', 'LIKE', '%'.$request->term.'%');
                });
            }
            
            if (request('created_at') != null) {
                $logs = $logs->whereDate('flow_logs.created_at', request('created_at'));
            }
        }
        
        $logs = $logs->orderby('flow_logs.created_at', 'desc')->groupBy('flow_logs.id');

        $logs  = $logs->paginate(50);

        if ($request->ajax()) {
            
            $page = $request->input('page', 1);
            $page_count = $page > 1 ? ($request->input('page', 1) - 1) * 50 : $request->input('page', 1) * 50;

            return response()->json([
                'tbody' => view('logging.partials.flowlogdata', compact('logs', 'page'))->with('i', $page_count)->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }
        
        $title = 'Flow Log List';
        return view('logging.flowlog', compact('logs', 'title', 'page'));
    }
    public function details(Request $request){
        
		$messageLogs =  [];
		if(isset($request->id) || isset($request->scraper_id)) {
			if(isset($request->id) and $request->id != 0){
				$messageLogs = FlowLogMessages::where('flow_log_id', $request->id);
				$messageLogs = $messageLogs->leftJoin('store_websites as sw', 'sw.id', '=', 'flow_log_messages.store_website_id')
                ->leftJoin('customers', 'customers.id', '=', 'flow_log_messages.leads')
                ->leftJoin('flow_logs', 'flow_logs.id', '=', 'flow_log_messages.flow_log_id' )
                ->leftJoin('flows', 'flows.id', '=', 'flow_logs.flow_id' )
                ->leftJoin('store_websites', 'store_websites.id', '=', 'flows.store_website_id' )
				->select('flow_log_messages.*', 'customers.name as lead_name', 'store_websites.website')->get();

			} else if (isset($request->scraper_id) and $request->scraper_id != 0){

				$messageLogs = FlowLogMessages::where('scraper_id', $request->scraper_id);
                if($request->assigned){
                    $messageLogs = $messageLogs->where('flow_log_messages.leads',$request->assigned);
                }
				$messageLogs = $messageLogs->leftJoin('store_websites as sw', 'sw.id', '=', 'flow_log_messages.store_website_id')->leftJoin('users', 'users.id', '=', 'flow_log_messages.leads')
				->select('flow_log_messages.*','sw.website as website', 'customers.name as lead_name')->get();
        	}
        }
        return view('logging.partials.flow_detail_data', compact('messageLogs'));
    }

    
}