<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\StoreWebsitePage;
use App\Language;
use App\GoogleTranslate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Pages | Store Website";

        $storeWebsites = StoreWebsite::all()->pluck("website", "id");
        $pages         = StoreWebsitePage::join("store_websites as  sw", "sw.id", "store_website_pages.store_website_id")
            ->select([\DB::raw("concat(store_website_pages.title,'-',sw.title) as page_name"), "store_website_pages.id"])
            ->pluck('page_name', 'id');

        $languages = Language::pluck('locale', 'code')->toArray(); //

        return view('storewebsite::page.index', [
            'title'         => $title,
            'storeWebsites' => $storeWebsites,
            'pages'         => $pages,
            'languages'     => $languages,
        ]);
    }

    public function records(Request $request)
    {
        $pages = StoreWebsitePage::leftJoin('store_websites as sw', 'sw.id', 'store_website_pages.store_website_id');

        // Check for keyword search
        if ($request->keyword != null) {
            $pages = $pages->where(function ($q) use ($request) {
                $q->where("store_website_pages.title", "like", "%" . $request->keyword . "%")
                    ->orWhere("store_website_pages.content", "like", "%" . $request->keyword . "%");
            });
        }

        if ($request->store_website_id != null) {
            $pages = $pages->where("store_website_pages.store_website_id", $request->store_website_id);
        }

        $pages = $pages->select(["store_website_pages.*", "sw.website as store_website_name"])->paginate();

        $items = $pages->items();

        return response()->json(["code" => 200, "data" => $items, "total" => $pages->total(), 
            "pagination" => (string)$pages->links()
        ]);
    }

    public function store(Request $request)
    {
        $post = $request->all();
        $id   = $request->get("id", 0);

        $params = [
            'title'   => 'required',
            'content' => 'required',
            //'stores'           => 'required',
            //'store_website_id' => 'required',
        ];

        if (empty($id)) {
            $params['stores']           = 'required';
            $params['store_website_id'] = 'required';
        }

        $validator = Validator::make($post, $params);

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

        $records = StoreWebsitePage::find($id);

        if (!$records) {
            $records = new StoreWebsitePage;
        }

        if (empty($id)) {
            $post["stores"] = implode(",", $post["stores"]);
            $string = str_replace(' ', '-', strtolower($post["title"])); // Replaces all spaces with hyphens.
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $post["url_key"] = $string;
        }

        $records->fill($post);

        // if records has been save then call a request to push
        if ($records->save()) {

        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $page = StoreWebsitePage::where("id", $id)->first();

        if ($page) {
            return response()->json(["code" => 200, "data" => $page]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $page = StoreWebsitePage::where("id", $id)->first();

        if ($page) {
            $page->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    public function push(Request $request, $id)
    {
        $page = StoreWebsitePage::where("id", $id)->first();

        if ($page) {
            \App\Jobs\PushPageToMagento::dispatch($page)->onQueue('mageone');
            return response()->json(["code" => 200, 'message' => "Website send for push"]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    public function getStores(Request $request, $id)
    {
        $stores = \App\StoreWebsite::join("websites as w", "w.store_website_id", "store_websites.id")
            ->join("website_stores as ws", "ws.website_id", "w.id")
            ->join("website_store_views as wsv", "wsv.website_store_id", "ws.id")
            ->where("w.store_website_id", $id)
            ->select("wsv.*")
            ->get();

        return response()->json(["code" => 200, "stores" => $stores]);
    }

    public function loadPage(Request $request, $id)
    {
        $page = \App\StoreWebsitePage::find($id);

        if($page) {

            $language = $request->language;

            // do by lanuage 
            if(!empty($language)) {

                $translateDescription = \App\Http\Controllers\GoogleTranslateController::translateProducts(
                    new GoogleTranslate,
                    $language,
                    [$page->content]
                );

                return response()->json(["code" => 200, "content" => !empty($translateDescription) ? $translateDescription : $page->content]);

            }else{
                return response()->json(["code" => 200, "content" => $page->content]);
            }
        }

    }

    public function pageHistory(Request $request, $page)
    {

    }

}
