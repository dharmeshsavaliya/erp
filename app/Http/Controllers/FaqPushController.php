<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProceesPushFaq;
use App\Jobs\ProcessTranslateReply;
use App\Jobs\ProcessAllFAQ;
use App\Reply;

class FaqPushController extends Controller
{	
	//This API will put the records in Queue 
    function 	pushFaq(Request 	$request){

    	$data 	=	$request->all();

    	if(empty($data['id'])) {
			return response()->json(['code' => 400, 'data' => [], 'message' => 'One of the api parameter is missing']);    		
    	}

    	try {

            //Add the data for queue
            $insertArray        =   [];
            $insertArray[]      =   $data['id'];
    
            $replyInfo          =   Reply::find($data['id']);

            if(!empty($replyInfo->is_translate)){   //if FAQ translate is  available then send for FAQ
	    	  ProceesPushFaq::dispatch($insertArray)->onQueue('faq_push');
            }
            else{   //If FAQ transation is not available then first set for translation
                ProcessTranslateReply::dispatch($replyInfo, \Auth::id())->onQueue('reply_translation');   //set for translation

                ProceesPushFaq::dispatch($insertArray)->onQueue('faq_push');
            }

			return response()->json(['code' => 200, 'data' => [], 'message' => 'FAQ added in queue']);

    	} catch (Exception $e) {
	    		return response()->json(['code' => 400, 'data' => [], 'message' => $e->getMessage()]);    		   		
    	}
    }   


    function    pushFaqAll(Request  $request,   Reply   $Reply){


        //get all reply without translate

        $replyInfo      =   $Reply->select('replies.id','magento_url','api_token','replies.is_translate')
                                ->join('store_websites','store_websites.id','=','replies.store_website_id')
                                ->join('reply_categories as rep_cat','rep_cat.id','=','replies.category_id')
                                ->whereNotNull('store_websites.magento_url')
                                ->whereNotNull('store_websites.api_token')
                                ->where('replies.is_translate' ,'!=', 1)
                                ->get();


        if(empty($replyInfo)) {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'No Record Found']);         
        }

        ProcessAllFAQ::dispatch($replyInfo, \Auth::id())->onQueue('faq_push');


        //get all reply with translate and set in chunks
        $replyInfo      =   $Reply->select('replies.id','magento_url','api_token','replies.is_translate')
                                ->join('store_websites','store_websites.id','=','replies.store_website_id')
                                ->join('reply_categories as rep_cat','rep_cat.id','=','replies.category_id')
                                ->whereNotNull('store_websites.magento_url')
                                ->whereNotNull('store_websites.api_token')
                                ->where('replies.is_translate' ,'=', 1)
                                ->get()
                                ->chunk(50);

        if(!empty($replyInfo)){
            foreach ($replyInfo as $key => $value) {
                    
                $insertArray    =   $value->pluck('id');
                $reqType        =   "pushFaqAll";
                ProceesPushFaq::dispatch($insertArray->toArray(),$reqType)->onQueue('faq_push');;


            }
        }        


        return response()->json(['code' => 200, 'data' => [], 'message' => 'All FAQ pushed in queue']);

         

    }
}
