<?php

namespace App\Http\Controllers\Api\v1;

use App\GoogleScrapperContent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class GoogleScrapperController extends Controller
{   
    
    /**
     * @SWG\Post(
     *   path="/v1/account/create",
     *   tags={"Account"},
     *   summary="Create Account",
     *   operationId="create-account",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function extractedData(Request $request)
    {

       $ContentData = json_decode($request,true);

       $ContentData               = new GoogleScrapperContent;
       $ContentData->title      = isset($request->title) ? $request->title : '';
       $ContentData->date       =  isset($request->date) ? $request->date : '';
       $ContentData->image =  isset($request->image) ? $request->image : '';
       $ContentData->url =  isset($request->url) ? $request->url : '';
       $ContentData->email = isset($request->email) ? $request->email : '';
       $ContentData->number =  isset($request->number) ? $request->number : '';
       $ContentData->about_us =  isset($request->about_us) ? $request->about_us : '';
       $ContentData->facebook =  isset($request->facebook) ? $request->facebook : '';
       $ContentData->instagram =  isset($request->instagram) ? $request->instagram : '';

       $ContentData->save();

        return response()->json(["code" => 200, "message" => "Google Scrapper Data saved", "data" => $ContentData]);
    }
}
