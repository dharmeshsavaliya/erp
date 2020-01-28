<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CronJob;
use App\CronJobReport;

class CronController extends Controller
{
    public function index(Request $request)
    {
    	if($request->term != null || $request->date != null){

    		if($request->term != null &&  $request->date != null){
    			$crons = CronJob::where('signature', 'like', "%{$request->term}%")
    					->whereDate('created_at',$request->date)
    					->paginate(15);

    		}
    		if($request->date != null){
    		 	$crons = CronJob::whereDate('created_at',$request->date)->paginate(15);
    		}
    		if($request->term != null){
    			$crons = CronJob::where('signature', 'like', "%{$request->term}%")->paginate(15);
    		}

    	}else{
    		$crons = CronJob::paginate(15);
    	}
    	
    	return view('cron.index',['crons' => $crons]);
    }

    public function history($id , Request $request){

    	if($request->date != null){

    		
    		$reports = CronJobReport::where('signature', 'like', "%{$id}%")
    					->whereDate('created_at',$request->date)
    					->paginate(15);

    		
    	}else{
    		$reports = CronJobReport::where('signature',$id)->paginate(15);
    	}


    	
    	return view('cron.history', ['reports' => $reports , 'signature' => $id]);
    }

	public function historySearch(Request $request)
    {
    	
    	if($request->date != null){

    		
    		$reports = CronJobReport::where('signature', 'like', "%{$request->signature}%")
    					->whereDate('created_at',$request->date)
    					->paginate(15);

    		
    	}else{
    		//dd($request);
    		$reports = CronJobReport::where('signature',$request->signature)->paginate(15);
    	}
    	return view('cron.history', ['reports' => $reports , 'signature' => $request->signature]);
    }

    public function runCommand(Request $request)
    {
        $command = $request->get("name");
        
        if(!empty($command)) {
            \Artisan::call($command, []);
            return response()->json(["code" => 200 , "output" => \Artisan::output()]);
        }else {
            return response()->json(["code" => 500 , "output" => "Command name is wrong or not added correctly"]);
        }
    }   
}
