<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsite;
use Crypt;
use App\StoreSocialAccount;
use App\StoreSocialContentCategory;
use App\Setting;
use App\Role;
use DB;
use App\User;
use App\StoreSocialContentStatus;
use App\StoreSocialContent;
use App\StoreSocialContentHistory;
use Auth;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ContentManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "List | Content Management";

        $websites = StoreWebsite::whereNull("deleted_at");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $websites = $websites->where(function ($q) use ($keyword) {
                $q->where("website", "LIKE", "%$keyword%")
                    ->orWhere("title", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $websites = $websites->get();

        foreach($websites as $w) {
            $w->facebookAccount = StoreSocialAccount::where('platform','facebook')->where('store_website_id',$w->id)->first();
            // if($w->facebookAccount) {
            //     $password = $w->facebookAccount->password;
            // }
            $w->instagramAccount = StoreSocialAccount::where('platform','instagram')->where('store_website_id',$w->id)->first();

        }
        return view('content-management.index', compact('title','websites'));
    }


    public function viewAddSocialAccount() {
        $websites = StoreWebsite::whereNull("deleted_at")->get();
        return view('content-management.add-social-account', compact('websites'));
    }

    public function addSocialAccount(Request $request) {
        $this->validate( $request, [
			'store_website_id'    => 'required',
			'platform' => 'required',
			'url' => 'required',
			'username' => 'required',
			'password' => 'required',
        ] );
        
        $input = $request->except('_token');
        $input['password'] = Crypt::encrypt(request('password'));
        StoreSocialAccount::create($input);
        return redirect()->back();
    }

    public function manageContent($id, Request $request) {
    
        //Getting Website Details
        $website = StoreWebsite::find($id);

        $categories = StoreSocialContentCategory::orderBy('id', 'desc');

        if ($request->k != null) {
            $categories = $categories->where("title", "like", "%" . $request->k . "%");
        }
        $ignoredCategory = [];
        // $ignoredCategory = \App\SiteDevelopmentHiddenCategory::where("store_website_id", $id)->pluck("category_id")->toArray();

        // if (request('status') == "ignored") {
        //     $categories = $categories->whereIn('id', $ignoredCategory);
        // } else {
        //     $categories = $categories->whereNotIn('id', $ignoredCategory);
        // }

        $categories = $categories->paginate(Setting::get('pagination'));

        //Getting Roles Developer
        // $role = Role::where('name', 'LIKE', '%Developer%')->first();

        //User Roles with Developers
        // $roles = DB::table('role_user')->select('user_id')->where('role_id', $role->id)->get();

        // foreach ($roles as $role) {
        //     $userIDs[] = $role->user_id;
        // }

        if (!isset($userIDs)) {
            $userIDs = [];
        }

        $allStatus = StoreSocialContentStatus::all();

        $statusCount = [];
        // $statusCount = \App\SiteDevelopment::join("site_development_statuses as sds","sds.id","site_developments.status")
        // ->groupBy("sds.id")
        // ->select(["sds.name",\DB::raw("count(sds.id) as total")])
        // ->get();

        // $users     = User::select('id', 'name')->whereIn('id', $userIDs)->get();

        $users     = User::select('id', 'name')->get();


        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('content-management.data', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory', 'statusCount'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }
        return view('content-management.manage-content', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory','statusCount'));
    }

    public function saveContentCategory(Request $request) {
        $isExist = StoreSocialContentCategory::where('title',$request->title)->first();
        if(!$isExist) {
            StoreSocialContentCategory::create(['title' => $request->text]);
            return response()->json(['message' => 'Successful'],200);
        }
        return response()->json(['message' => 'Error'],500);
    }

    public function saveContent(Request $request) {
        $social_content = StoreSocialContent::where('store_social_content_category_id',$request->category)->where('store_website_id',$request->websiteId)->first();
        $msg = null;
        $type = null;
        if(!$social_content) {
            $social_content = new StoreSocialContent;
            $social_content->store_social_content_status_id = 0;
        }
        if ($request->type == 'status') {
            $social_content->store_social_content_status_id = $request->text;
        }
        if ($request->type == 'platform') {
            $social_content->platform = $request->text;
        }
        if ($request->type == 'creator') {
            $social_content->creator_id = $request->text;
        }
        if ($request->type == 'publisher') {
            $social_content->publisher_id = $request->text;
        }
        if ($request->type == 'request_date') {
            $newValue = $request->request_date;
            if($social_content->request_date) {
                $oldValue = $social_content->request_date;
            }
            else {
                $oldValue = '';
            }
            $msg = 'Request date changed from '.$oldValue.' to ' .$newValue;
            $type = 'request_date';
            $social_content->request_date = $request->request_date;
        }
        if ($request->type == 'due_date') {
            $newValue = $request->due_date;
            if($social_content->due_date) {
                $oldValue = $social_content->due_date;
            }
            else {
                $oldValue = '';
            }
            $msg = 'Due date changed from '.$oldValue.' to ' .$newValue;
            $type = 'due_date';
            $social_content->due_date = $request->due_date;
        }
        if ($request->type == 'publish_date') {
            $newValue = $request->publish_date;
            if($social_content->publish_date) {
                $oldValue = $social_content->publish_date;
            }
            else {
                $oldValue = '';
            }
            $msg = 'Publish date changed from '.$oldValue.' to ' .$newValue;
            $type = 'publish_date';
            $social_content->publish_date = $request->publish_date;
        }

      
        

        // if ($request->type == 'content') {
        //     $store_strategy->content_id = $request->text;
        // }


        $social_content->store_social_content_category_id = $request->category;
        $social_content->store_website_id   = $request->websiteId;
        $social_content->save();

        if($msg) {
            $h = new StoreSocialContentHistory;
            $h->type = $type;
            $h->store_social_content_id = $social_content->id;
            $h->message = $msg;
            $h->username = Auth::user()->name;
            $h->save();
        }

        return response()->json(["code" => 200, "messages" => 'Social content Saved Sucessfully']);
    }


    public function editCategory(Request $request)
    {
        $category = StoreSocialContentCategory::find($request->categoryId);
        if ($category) {
            $category->title = $request->category;
            $category->save();
        }

        return response()->json(["code" => 200, "messages" => 'Category Edited Sucessfully']);
    }

        public function showHistory(Request $request) {
            $social_content = StoreSocialContent::where('store_social_content_category_id',$request->category)->where('store_website_id',$request->websiteId)->first();
            if($social_content) {
                $h = StoreSocialContentHistory::where('store_social_content_id',$social_content->id)->where('type',$request->type)->get();
                return response()->json(['history' => $h],200);
            }
            return response()->json(['message' => ''],500);
        }


        public function uploadDocuments(Request $request)
        {
            $path = storage_path('tmp/uploads');
    
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
    
            $file = $request->file('file');
    
            $name = uniqid() . '_' . trim($file->getClientOriginalName());
    
            $file->move($path, $name);
    
            return response()->json([
                'name'          => $name,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }


        public function saveDocuments(Request $request)
        {
            $site      = null;
            $documents = $request->input('document', []);
            if (!empty($documents)) {
                if ($request->id) {
                    $site = StoreSocialContent::find($request->id);
                }

                if (!$site || $request->id == null) {
                    $site                               = new StoreSocialContent;
                    $site->store_social_content_status_id  = 0;
                    $site->store_website_id                   = $request->store_website_id;
                    $site->store_social_content_category_id = $request->store_social_content_category_id;
                    $site->save();
                }

                foreach ($request->input('document', []) as $file) {
                    $path  = storage_path('tmp/uploads/' . $file);
                    $media = MediaUploader::fromSource($path)
                        ->toDirectory('site-development/' . floor($site->id / config('constants.image_per_folder')))
                        ->upload();
                    $site->attachMedia($media, config('constants.media_tags'));
                }

                return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
            } else {
                return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
            }

        }


    public function listDocuments(Request $request, $id)
    {
        $site = StoreSocialContent::find($request->id);

        $userList = [];

        if ($site->publisher_id) {
            $userList[$site->publisher->id] = $site->publisher->name;
        }

        if ($site->creator_id) {
            $userList[$site->creator->id] = $site->creator->name;
        }

        $userList = array_filter($userList);
        // create the select box design html here
        $usrSelectBox = "";
        if (!empty($userList)) {
            $usrSelectBox = (string) \Form::select("send_message_to", $userList, null, ["class" => "form-control send-message-to-id"]);
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        "id"        => $media->id,
                        'url'       => $media->getUrl(),
                        'site_id'   => $site->id,
                        'user_list' => $usrSelectBox,
                    ];
                }
            }
        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    public function deleteDocument(Request $request)
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();
                return response()->json(["code" => 200, "message" => "Document delete succesfully"]);
            }
        }

        return response()->json(["code" => 500, "message" => "No document found"]);
    }

    public function sendDocument(Request $request)
    {
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media        = \Plank\Mediable\Media::find($request->id);
            $user         = \App\User::find($request->user_id);
            if ($user) {
                if ($media) {
                    \App\ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        "Please find attached file",
                        $media->getUrl()
                    );
                    return response()->json(["code" => 200, "message" => "Document send succesfully"]);
                }
            }else{
                return response()->json(["code" => 200, "message" => "User or site is not available"]);
            }
        }

        return response()->json(["code" => 200, "message" => "Sorry required fields is missing like id, siteid , userid"]);
    }


    public function remarks(Request $request, $id)
    {
        $response = \App\StoreSocialContentRemark::join("users as u","u.id","store_social_content_remarks.user_id")
        ->where("store_social_content_id",$id)
        ->select(["store_social_content_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("store_social_content_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);
    }

    public function saveRemarks(Request $request, $id)
    {
        \App\StoreSocialContentRemark::create([
            "remarks" => $request->remark,
            "store_social_content_id" => $id,
            "user_id" => \Auth::user()->id,
        ]);

        $response = \App\StoreSocialContentRemark::join("users as u","u.id","store_social_content_remarks.user_id")
        ->where("store_social_content_id",$id)
        ->select(["store_social_content_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("store_social_content_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd("a");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd("b");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
