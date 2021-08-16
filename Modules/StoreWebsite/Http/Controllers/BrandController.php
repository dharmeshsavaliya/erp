<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use App\Product;
use App\StoreWebsiteBrand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelper;
use App\Brand; 
use App\Category; 
use App\StoreWebsiteBrandHistory;
use GuzzleHttp\Client;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $id)
    {
        $title = "Attached Brand | Store Website";

        if ($request->ajax()) {
            // send response into the json
            $brands = \App\Brand::getBrands()->pluck("name", "id")->toArray();

            $storeWebsite = StoreWebsiteBrand::join("brands as b", "b.id", "store_website_brands.brand_id")
                ->where("store_website_id", $id)
                ->select(["store_website_brands.*", "b.name"])
                ->get();

            return response()->json([
                "code"             => 200,
                "store_website_id" => $id,
                "data"             => $storeWebsite,
                "brands"           => $brands,
            ]);
        }

        return view('storewebsite::index', compact('title'));
    }

    /**
     * store cateogories
     *
     */

    public function store(Request $request)
    {
        $storeWebsiteId = $request->get("store_website_id");
        $post           = $request->all();

        $validator = Validator::make($post, [
            'store_website_id' => 'required',
            'markup'           => 'required',
            'brand_id'         => 'unique:store_website_brands,brand_id,NULL,id,store_website_id,' . $storeWebsiteId . '|required',
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

        $storeWebsiteBrand = new StoreWebsiteBrand();
        $storeWebsiteBrand->fill($post);
        $storeWebsiteBrand->save();

        return response()->json(["code" => 200, "data" => $storeWebsiteBrand]);

    }

    public function delete(Request $request, $id, $store_brand_id)
    {
        $storeBrand = StoreWebsiteBrand::where("store_website_id", $id)->where("id", $store_brand_id)->first();
        if ($storeBrand) {
            $storeBrand->delete();
        }
        return response()->json(["code" => 200, "data" => []]);
    }

    public function list(Request $request) {
        $title = "Store Brand";

        if ($request->get("push") == 1) {
            // brand push changes 
            
            $brands = \App\Product::join("brands as b","b.id","products.brand")->groupBy("b.id")->select(["b.*"])->get();
            
            $webLimit = explode(",", $request->get("store_website_id"));
            
            $storeWebsites = StoreWebsite::whereIn("id",$webLimit)->where("api_token", "!=", "")->where("website_source", "magento")->get();

            if(!$brands->isEmpty()) {
                foreach($brands as $brand) {
                    if(!$storeWebsites->isEmpty()) {
                        foreach($storeWebsites as $storeWeb) {
                            $magentoBrandId = MagentoHelper::addBrand($brand,$storeWeb);
                            if(!empty($magentoBrandId)){
                                $brandStore = StoreWebsiteBrand::where("brand_id", $brand->id)->where("store_website_id", $storeWeb->id)->first();
                                if(!$brandStore) {
                                   $brandStore =  new \App\StoreWebsiteBrand;
                                   $brandStore->brand_id = $brand->id;
                                   $brandStore->store_website_id = $storeWeb->id;
                                }
                                $brandStore->magento_value = $magentoBrandId;
                                $brandStore->save();
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success','Brand Request finished successfully');;
        }
		$storeWebsite = StoreWebsite::pluck('title','id')->toArray();
		
        $query = Brand::leftJoin('products', 'products.brand', '=', 'brands.id')->groupBy('brands.id')->select('brands.*', DB::raw('count(products.id) as counts'));

        // $query = $query->whereNull("brands.deleted_at");
        
        if($request->keyword != null) {
            $query->where("brands.name","like","%".$request->keyword."%");
        }

        if($request->category_id != null) {
            $query->where("products.category",$request->category_id);
        }

        if($request->has('no-inventory')) {
            $query->having('counts', '=', 0);
        } else {
            $query->having('counts', '>', 0);
        }

        $query->orderBy('brands.name', 'asc');

        // echo "<pre>";
        // print_r($brands->toArray());
        // exit;
	
        $appliedQ      = StoreWebsiteBrand::all();
        
        $apppliedResult = [];

        if(!$appliedQ->isEmpty()){
            foreach($appliedQ as $raw) {
                $apppliedResult[$raw->brand_id][] = $raw->store_website_id;
            }
        }
		$limit = 30; 
		if ($request->ajax() && $request->pagination == null) {
			$brands = $query->limit($limit)->offset(($request->page - 1)*$limit)->get();
            return response()->json([
                'tbody' => view('storewebsite::brand.partials.brand_data', compact('brands', 'storeWebsite', 'apppliedResult'))->render(),
                'count' => count($brands)
            ], 200);
        } else{
			 $brands = $query->limit($limit)->get(); 
		}
       $categories = Category::join('products', 'products.category', '=', 'categories.id')->orderBy('categories.title','asc')->pluck('categories.title','categories.id');

        return view("storewebsite::brand.index", compact(['title', 'brands', 'storeWebsite','apppliedResult','categories']));
    }

    public function pushToStore(Request $request)
    {
        $user = auth()->user();
        if ($request->brand != null && $request->store != null) {
            try
            {
                $brandStore = StoreWebsiteBrand::where("brand_id", $request->brand)->where("store_website_id", $request->store)->first();
                $website = StoreWebsite::find($request->store);
                if($website){
                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $brand = \App\Brand::find($request->brand);
                        if (!$brandStore) {
                            $magentoBrandId = MagentoHelper::addBrand($brand,$website);
                            if($magentoBrandId) {
                                $brandStore = new StoreWebsiteBrand;
                                $brandStore->brand_id = $request->brand;
                                if(isset($magentoBrandId)){
                                    $brandStore->magento_value = $magentoBrandId;
                                }
                                $brandStore->store_website_id = $request->store;
                                $brandStore->save();
                                StoreWebsiteBrandHistory::create([
                                    'brand_id' => $request->brand,
                                    'store_website_id' => $request->store,
                                    'type' => "assign",
                                    'created_by' => $user->id,
                                    'message' => "{$website->title} assigned to {$brand->name} brand."
                                ]);
                                
                                return response()->json(["code" => 200 , "message" =>  "Brand is pushed to store successfully."]);
                            }
                            else
                            {
                                StoreWebsiteBrandHistory::create([
                                    'brand_id' => $request->brand,
                                    'store_website_id' => $request->store,
                                    'type' => "error",
                                    'created_by' => $user->id,
                                    'message' => "{$website->title} assigned to {$brand->name} brand failed."
                                ]);
                                return response()->json(["code" => 500 , "message" =>  "Brand is not pushed to store,please check history log."]);
                            }
                        }else{
                            $bID = $brandStore->magento_value;
                            if($bID){
                                $status = MagentoHelper::deleteBrand($bID,$website);
                                if($status) {
                                    $brandStore->delete();
                                    StoreWebsiteBrandHistory::create([
                                        'brand_id' => $request->brand,
                                        'store_website_id' => $request->store,
                                        'type' => "remove",
                                        'created_by' => $user->id,
                                        'message' => "{$brand->name} removed from {$website->title} store."
                                    ]);
                                    return response()->json(["code" => 200 , "message" => "Brand is removed from store successfully."]);
                                }else {
                                    StoreWebsiteBrandHistory::create([
                                        'brand_id' => $request->brand,
                                        'store_website_id' => $request->store,
                                        'type' => "error",
                                        'created_by' => $user->id,
                                        'message' => "{$brand->name} is not removed from {$website->title} store."
                                    ]);
                                    return response()->json(["code" => 500 , "message" => "Brand is not removed from store,please check history log."]);
                                }
                            }
                        }
                    }        

                }

                // if ($request->active == null || $request->active == "false") {
                //     if ($brandStore) {
                //         $brandStore->delete();
                //     }
                // } else {
                //     if (!$brandStore) {
                //         $brandStore = new \App\StoreWebsiteBrand;
                //     }
                //     $brandStore->brand_id = $request->brand;
                //     if(isset($magentoBrandId)){
                //         $brandStore->magento_value = $magentoBrandId;
                //     }
                //     $brandStore->store_website_id = $request->store;
                //     $brandStore->save();
                // }
            }
            catch(\Exception $e) {
                StoreWebsiteBrandHistory::create([
                    'brand_id' => $request->brand,
                    'store_website_id' => $request->store,
                    'type' => "error",
                    'created_by' => $user->id,
                    'message' => $e->getMessage()
                ]);
                return response()->json(["code" => 200, "data" => []]);
            }
        }

        return response()->json(["code" => 200, "data" => []]);

    }

    /**
     * run artisan command
     *
     */

    public function refreshMinMaxPrice()
    {
        try {
           
           \Artisan::call('brand:maxminprice'); 

            return response()->json('Console Commnad Ran',200);   
        
        } catch (\Exception $e) {
            
            return response()->json('Cannot call artisan command',200); 
        } 

    }

    public function history(Request $request){
        if ($request->brand != null && $request->store != null) {
            
            $StoreWebsiteBrandHistories = StoreWebsiteBrandHistory::leftJoin("users as u","u.id","store_website_brand_histories.created_by")
            ->where("brand_id", $request->brand)
            ->where("store_website_id", $request->store)
            ->select(["store_website_brand_histories.*","u.name as user_name"])
            ->get();

            return view("storewebsite::brand.history", compact(['StoreWebsiteBrandHistories']));
        }
        return response()->json(["code" => 200, "data" => []]);
    }

    public function liveBrands(Request $request)
    {
        $storeWebsite = \App\StoreWebsite::find($request->store_website_id);
        if($storeWebsite) {
            $client   = new Client();
            $response = $client->request('GET', $storeWebsite->magento_url."/rest/V1/brands/list", [
                'form_params' => [
                    
                ],
            ]);
            $brands = (string)$response->getBody()->getContents();
            $brands = json_decode($brands,true);
            $mangetoIds = [];

            if(!empty($brands)) {
                foreach($brands as $brand) {
                    $mangetoIds[] = $brand['attribute_id'];
                }
            }

            $availableBrands = \App\StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")
            ->where("store_website_id",$storeWebsite->id)
            ->whereIn("magento_value",$mangetoIds)
            ->groupBy("store_website_brands.magento_value")
            ->select(["b.*"])
            ->get();
            //$missingBrands   = \App\StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")->whereNotIn("magento_value",$mangetoIds)->get();

            $total = $availableBrands->count();

            return view("storewebsite::brand.live-compare",compact('availableBrands','total'));
        }
    }

    public function missingBrands(Request $request)
    {
        $storeWebsite = \App\StoreWebsite::find($request->store_website_id);
        if($storeWebsite) {
            $client   = new Client();
            $response = $client->request('GET', $storeWebsite->magento_url."/rest/V1/brands/list", [
                'form_params' => [
                    
                ],
            ]);
            $brands = (string)$response->getBody()->getContents();
            $brands = json_decode($brands,true);
            $mangetoIds = [];

            if(!empty($brands)) {
                foreach($brands as $brand) {
                    $mangetoIds[] = $brand['attribute_id'];
                }
            }

            $availableBrands = \App\StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")
            ->where("store_website_id",$storeWebsite->id)
            ->whereNotIn("magento_value",$mangetoIds)
            ->groupBy("store_website_brands.magento_value")
            ->select(["b.*"])
            ->get();

            $total = $availableBrands->count();

            return view("storewebsite::brand.live-compare",compact('availableBrands'));
        }
    }

    public function reconsileBrands(Request $request)
    {
        $storeWebsite = \App\StoreWebsite::find($request->store_website_id);
        if($storeWebsite) {
            $client   = new Client();
            $response = $client->request('GET', $storeWebsite->magento_url."/rest/V1/brands/list", [
                'form_params' => [
                ],
            ]);
            $brands = (string)$response->getBody()->getContents();
            $brands = json_decode($brands,true);
            $mangetoIds = [];
            if(!empty($brands)) {
                foreach($brands as $brand) {
                    $mangetoIds[] = $brand['attribute_id'];
                }
            }

            $rightBrand = \App\StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")
            ->where("store_website_id",$storeWebsite->id)
            ->where("magento_value",">",0)
            ->groupBy("store_website_brands.magento_value")
            ->select(["store_website_brands.*"])
            ->get();

            $assingedBrands = [];
            $noneedTodelete = [];
            if(!$rightBrand->isEmpty()) {
                foreach($rightBrand as $rb) {
                    $noneedTodelete[] = $rb->magento_value;
                    if($rb->magento_value > 0) {
                        $assingedBrands[$rb->magento_value] = $rb;
                    }
                }
            }

            $needDeleteRequest = array_diff($mangetoIds, $noneedTodelete);
            \Log::info(print_r(["Delete request IDS",$needDeleteRequest],true));

            // go for delete brands
            $userId = auth()->user()->id;
            if(!empty($needDeleteRequest)) {
                foreach($needDeleteRequest as $ndr) {
                    \Log::info("Request started for ".$ndr);
                    try{
                        \Log::info("Brand started for delete ".$ndr);
                        $status = MagentoHelper::deleteBrand($ndr,$storeWebsite);
                    }catch(Exception $e) {
                        \Log::info("Brand delete has error with id $ndr =>".$e->getMessage());
                    }
                    \Log::info("Brand check for delete ".$ndr);
                    if(isset($assingedBrands[$ndr])) {
                        \Log::info("Brand find for delete ".$ndr);
                        $brandStore = $assingedBrands[$ndr];
                        $brandStore->delete();
                        StoreWebsiteBrandHistory::create([
                            'brand_id' => $brandStore->brand_id,
                            'store_website_id' => $brandStore->store_website_id,
                            'type' => "remove",
                            'created_by' => $userId,
                            'message' => "{$brandStore->name} removed from {$storeWebsite->title} store."
                        ]);
                    }
                }
            }


            // this we need to push
            $availableBrands = \App\StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")
            ->where("store_website_id",$storeWebsite->id)
            ->whereNotIn("magento_value",$mangetoIds)
            ->groupBy("store_website_brands.magento_value")
            ->select(["b.*"])
            ->get();

            if(!$availableBrands->isEmpty()) {
                foreach($availableBrands as $avb) {
                    $magentoBrandId = MagentoHelper::addBrand($avb,$storeWebsite);
                    if(!empty($magentoBrandId)){
                        $brandStore = StoreWebsiteBrand::where("brand_id", $avb->id)->where("store_website_id", $storeWebsite->id)->first();
                        if(!$brandStore) {
                           $brandStore =  new \App\StoreWebsiteBrand;
                           $brandStore->brand_id = $avb->id;
                           $brandStore->store_website_id = $storeWebsite->id;
                        }
                        $brandStore->magento_value = $magentoBrandId;
                        $brandStore->save();
                    }
                }
            }

        }

        return response()->json(["code" => 200 , "message" => "Reconsile request has been finished successfully"]);
    }

}
