<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
use App\StoreWebsiteAttributes;
use App\LogStoreWebsiteAttributes;
use App\StoreWebsite;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\SocialStrategySubject;
use App\Setting;
use App\User;
use App\SocialStrategy;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Support\Facades\DB;

class SiteAttributesControllers extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Site Attributes";

        return view('storewebsite::site-attributes.index', compact('title'));
    }

    public function log($log_case_id,$attribute_id,$attribute_key,$attribute_val,$store_website_id,$log_msg)
    {
        $log = New LogStoreWebsiteAttributes();
        $log->log_case_id = $log_case_id;
        $log->attribute_id = $attribute_id;
        $log->attribute_key = $attribute_key;
        $log->attribute_val = $attribute_val;
        $log->store_website_id = $store_website_id;
        $log->log_msg = $log_msg;
        $log->save();
    }
    public function attributesHistory(request $request)
    {
        $id = $request->id;
        $html = '';
        $paymentData = LogStoreWebsiteAttributes::where('attribute_id', $id)
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td>' . $history->log_case_id . '</td>';
                $html .= '<td>' . $history->attribute_id . '</td>';
                $html .= '<td>' . $history->attribute_key . '</td>';
                $html .= '<td>' . $history->attribute_val . '</td>';
                $html .= '<td>' . $history->store_website_id . '</td>';
                $html .= '<td>' . $history->log_msg . '</td>';
                $html .= '<td>' . $history->updated_at . '</td>';
                $html .= '</tr>';

                $i++;
            }
            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html, 'success' => true], 200);

    }
    /**
     * Store Page
     * @param  Request $request [description]
     * @return
     */
    public function store(Request $request)
    {
        
        $post = $request->all();
        $validator = Validator::make($post, [
            'attribute_key'       => 'required',
            'attribute_val'       => 'required',
            'store_website_id' => 'required',
        ]);



        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }
        

        $id = $request->get("id", 0);
        $records = StoreWebsiteAttributes::find($id);

        if (!$records) {
            $records = new StoreWebsiteAttributes;
           
        }

        $records->fill($post);
        // if records has been save then call a request to push
        if ($records->save()) 
        {
            if($id)
            {
                $this->log("#2",$records->id,$request->attribute_key,$request->attribute_val,$request->store_website_id,'Store Website Attribute has updated.');
            }
            else
            {
                
                $this->log("#1",$records->id,$request->attribute_key,$request->attribute_val,$request->store_website_id,'Store Website Attribute has stored.');
            }

        }
        
        return response()->json(["code" => 200, "data" => $records]);
    }

    /**
     * Index Page
     * @param  Request $request [description]
     * @return
     */
    public function records(Request $request)
    {

        $StoreWebsiteAttributesViews = StoreWebsiteAttributes::join('store_websites','store_websites.id','store_website_attributes.store_website_id');
        if ($request->keyword != null) {
            $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->where("store_website_id", "like", "%" . $request->keyword . "%");
        }

        $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->select(["store_website_attributes.*", "store_websites.website"])->paginate();

        return response()->json(["code" => 200, "data" => $StoreWebsiteAttributesViews->items(), "total" => $StoreWebsiteAttributesViews->count(),
            "pagination" => (string) $StoreWebsiteAttributesViews->render()
    ]);
    }

    /**
     * Add Page
     * @param  Request $request [description]
     * @return
     */
    public function list(Request $request)
    {

        $websitelist = StoreWebsite::all();

        return response()->json(["code" => 200, "data" => '', "websitelist" => $websitelist]);
    }


    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $StoreWebsiteAttributes = StoreWebsiteAttributes::where("id", $id)->first();

        if ($StoreWebsiteAttributes) {
            $StoreWebsiteAttributes->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong attribute id!"]);
    }

    
    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {

        $StoreWebsiteAttributes = StoreWebsiteAttributes::where("id", $id)->first();
        
        $websitelist = StoreWebsite::all();

        if ($StoreWebsiteAttributes) {
            return response()->json(["code" => 200, "data" => $StoreWebsiteAttributes, "websitelist" => $websitelist]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
   
}
