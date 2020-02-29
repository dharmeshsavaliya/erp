<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use App\StoreWebsiteCategory;
use App\StoreWebsiteBrand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

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
            $brands = \App\Brand::getBrands()->pluck("name","id")->toArray();

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
            'markup'        => 'required',
            'brand_id'      => 'unique:store_website_brands,brand_id,NULL,id,store_website_id,' . $storeWebsiteId . '|required',
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

}
