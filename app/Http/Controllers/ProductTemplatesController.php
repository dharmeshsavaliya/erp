<?php

namespace App\Http\Controllers;

use App\Agent;
use File;
use Illuminate\Http\Request;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productTemplates = \App\ProductTemplate::orderBy("id", "desc")->paginate(10);
        return view("product-template.index");
    }

    public function response()
    {
        $records = \App\ProductTemplate::orderBy("id", "desc")->paginate(5);
        return response()->json([
            "code"       => 1,
            "result"     => $records,
            "pagination" => (string) $records->links(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template = new \App\ProductTemplate;
        $template->fill(request()->all());

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('product-template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return response()->json(["code" => 1, "message" => "Product Template Created successfully!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Agent::find($id)->delete();

        return redirect()->back()->withSuccess('You have successfully deleted and agent!');
    }

    public function apiIndex(Request $request)
    {
        $limit   = $request->get("limit", 10);
        $records = \App\ProductTemplate::leftJoin("brands as b", "b.id", "product_templates.brand_id")
            ->select(["product_templates.*", "b.name as brand_name"]);

        if ($request->get("id", null) != null) {
            $records->where("id",$request->get("id"));
        }

        if ($request->get("productTitle", null) != null) {
            $q = $request->get('productTitle');
            $records->where("product_title", "like", "%$q%");
        }

        if ($request->get("productBrand", null) != null) {
            $q = $request->get('productBrand');
            $records->where("b.name", "like", "%$q%");
        }

        $records = $records->orderBy("product_templates.id", "desc")->paginate($limit);

        $data = [];
        foreach ($records as $record) {
            $array = [
                "id"                     => $record->id,
                "productTitle"           => $record->product_title,
                "productBrand"           => $record->brand_name,
                "productPrice"           => $record->price,
                "productDiscountedPrice" => $record->discounted_price,
                "productCurrency"        => $record->currency,
            ];

            if ($record->hasMedia(config('constants.media_tags'))) {
                foreach ($record->getMedia(config('constants.media_tags')) as $i => $media) {
                    $array["image" . ($i + 1)] = $media->getUrl();
                }
            }

            $data[] = $array;

        }

        return response()->json(["code" => 1, "data" => $data]);

    }
}
