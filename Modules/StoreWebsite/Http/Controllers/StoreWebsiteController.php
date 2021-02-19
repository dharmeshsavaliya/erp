<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
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
use App\StoreWebsiteUsers;
use App\MagentoUserPasswordHistory;
use seo2websites\MagentoHelper\MagentoHelperv2;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
class StoreWebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Store Website";

        return view('storewebsite::index', compact('title'));
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function records(Request $request)
    {
        $records = StoreWebsite::whereNull("deleted_at");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("website", "LIKE", "%$keyword%")
                    ->orWhere("title", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function save(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'title'   => 'required',
            'website' => 'required',
            'magento_username' => 'required',
            'magento_password' => 'required',
            'api_token' => 'required',
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

        $records = StoreWebsite::find($id);

        if (!$records) {
            $records = new StoreWebsite;
        }

        $records->fill($post);
        $records->save();

        if($request->staging_username && $request->staging_password) {
            $message = 'Staging Username: '.$request->staging_username.', Staging Password is: ' . $request->staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }
        
        if($request->mysql_username && $request->mysql_password) {
            $message = 'Mysql Username: '.$request->mysql_username.', Mysql Password is: ' . $request->mysql_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if($request->mysql_staging_username && $request->mysql_staging_password) {
            $message = 'Mysql Staging Username: '.$request->mysql_staging_username.', Mysql Staging Password is: ' . $request->mysql_staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        return response()->json(["code" => 200, "data" => $records]);
    }
	
	public function saveUserInMagento(Request $request) {
        $post = $request->all();
        $validator = Validator::make($post, [
            'username'   => 'required',
            'firstName'   => 'required',
            'lastName'   => 'required',
            'userEmail'   => 'required',
            'password' => 'required',
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

        $checkUserNameExist = '';
        if(!empty($post['store_website_userid'])) {
            $checkUserExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('email',$post['userEmail'])->where('id','<>',$post['store_website_userid'])->first();
            if(empty($checkUserExist)) {
                $checkUserNameExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('username',$post['username'])->where('id','<>',$post['store_website_userid'])->first();
            }
        } else {
            $checkUserExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('email',$post['userEmail'])->first();
            if(empty($checkUserExist)) {
                $checkUserNameExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('username',$post['username'])->first();
            }
        }

        if(!empty($checkUserExist)) {
            return response()->json(["code" => 500, "error" => "User Email already exist!"]);
        }
        if(!empty($checkUserNameExist)) {
            return response()->json(["code" => 500, "error" => "Username already exist!"]);
        }

        $uppercase = preg_match('@[A-Z]@', $post['password']);
        $lowercase = preg_match('@[a-z]@', $post['password']);
        $number    = preg_match('@[0-9]@', $post['password']);
        if( !$uppercase || !$lowercase || !$number || strlen( $post['password']) < 7)
        {
            return response()->json(["code" => 500, "error" => "Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters."]);
        }

        $storeWebsite = StoreWebsite::find($post['store_id']);
        if(!empty($post['store_website_userid'])) {
            $getUser = StoreWebsiteUsers::where('id',$post['store_website_userid'])->first();
            $old_password = $getUser->password;
            $getUser->first_name = $post['firstName'];
            $getUser->last_name = $post['lastName'];
            $getUser->email = $post['userEmail'];
            $getUser->password = $post['password'];
            $getUser->save();

            if($old_password != $post['password']) {
                $history_param['store_website_userid'] = $post['store_website_userid'];
                $history_param['old_password'] = $old_password;
                $history_param['new_password'] = $post['password'];
                MagentoUserPasswordHistory::create($history_param);
            }

            $magentoHelper = new MagentoHelperv2();
            $result = $magentoHelper->updateMagentouser($storeWebsite, $post);
            return response()->json(["code" => 200, "messages" => 'User details updated Sucessfully']);
        } else {
            $params['username'] = $post['username'];
            $params['first_name'] = $post['firstName'];
            $params['last_name'] = $post['lastName'];
            $params['email'] = $post['userEmail'];
            $params['password'] = $post['password'];
            $params['store_website_id'] = $post['store_id'];
            $response = StoreWebsiteUsers::create($params);

            if($response) {
                $history_param['store_website_userid'] = $response->id;
                $history_param['old_password'] = $post['password'];
                $history_param['new_password'] = null;
                MagentoUserPasswordHistory::create($history_param);
            }
            
            if($post['userEmail'] && $post['password']) {
                $message = 'Email: '.$post['userEmail'].', Password is: ' . $post['password'];
                $params['user_id'] = Auth::id();
                $params['message'] = $message;
                $chat_message = ChatMessage::create($params);
            }

            $magentoHelper = new MagentoHelperv2();
            $result = $magentoHelper->addMagentouser($storeWebsite, $post);
            return response()->json(["code" => 200, "messages" => 'User details saved Sucessfully']);
        }
    }

    public function deleteUserInMagento(Request $request) {
        $post = $request->all();
        $getUser = StoreWebsiteUsers::where('id',$post['store_website_userid'])->first();
        $username = $getUser->username;
        $getUser->is_deleted = 1;
        $getUser->save();

        $storeWebsite = StoreWebsite::find($getUser->store_website_id);

        $magentoHelper = new MagentoHelperv2();
        $result = $magentoHelper->deleteMagentouser($storeWebsite, $username);
        return response()->json(["code" => 200, "messages" => 'User Deleted Sucessfully']);
    }

    public function userPasswordHistory(Request $request) {
        $post = $request->all();
        $getHistory = MagentoUserPasswordHistory::join("store_website_users as su","su.id","magento_user_password_history.store_website_userid")->where('store_website_userid',$post['store_website_userid'])
        ->select(["magento_user_password_history.*","su.username"])
        ->orderBy('magento_user_password_history.id','desc')
        ->get();
        $data = '';
        if(sizeof($getHistory) > 0) {
            foreach ($getHistory as $key => $value) {
                $data .= '
                        <tr>
                            <td>'.$value->username.'</td>
                            <td>'.$value->old_password.'</td>
                            <td>'.$value->new_password.'</td>
                        </tr>
                ';
            }
        } else {
            $data .= '
                    <tr>
                        <td colspan="3">No record</td>
                    </tr>
            ';
        }
        return response()->json(["code" => 200, "data" => $data]);
    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where("id", $id)->first();
		$storewebsiteusers = StoreWebsiteUsers::where('store_website_id',$id)->where('is_deleted',0)->get();

        if ($storeWebsite) {
            return response()->json(["code" => 200, "data" => $storeWebsite,"userdata" => $storewebsiteusers, "totaluser" => count($storewebsiteusers)]);
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
        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {
            $storeWebsite->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    public function updateSocialRemarks(Request $request , $id)
    {

        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {

            $facebook_remarks = $request->get("facebook_remarks");
            if(!empty($facebook_remarks)) {
                $storeWebsite->facebook_remarks = $facebook_remarks;               
            }

            $instagram_remarks = $request->get("instagram_remarks");
            if(!empty($instagram_remarks)) {
                $storeWebsite->instagram_remarks = $instagram_remarks;               
            }
            
            $storeWebsite->save();

            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);

    }

    public function socialStrategy($id, Request $request)
    {
        $website = StoreWebsite::find($id);
        $subjects = SocialStrategySubject::orderBy('id', 'desc');

        if ($request->k != null) {
            $subjects = $subjects->where("title", "like", "%" . $request->k . "%");
        }

        $subjects = $subjects->paginate(Setting::get('pagination'));
        foreach($subjects as $subject) {
            $subject->strategy = SocialStrategy::where('social_strategy_subject_id',$subject->id)->where('website_id',$id)->first();
        }
        $users = User::select('id', 'name')->get();

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::social-strategy.partials.data', compact('subjects', 'users', 'website'))->render(),
                'links' => (string) $subjects->render(),
            ], 200);
        }

        return view('storewebsite::social-strategy.index', compact('subjects', 'users', 'website'));
    }

    public function submitSubject(Request $request) {
        if ($request->text) {
        $subjectCheck = SocialStrategySubject::where('title', $request->text)->first();

        if (empty($subjectCheck)) {
            $subject        = new SocialStrategySubject;
            $subject->title = $request->text;
            $subject->save();

            return response()->json(["code" => 200, "messages" => 'Subject Saved Sucessfully']);

        } else {

            return response()->json(["code" => 500, "messages" => 'Subject Already Exist']);
        }

        } else {
            return response()->json(["code" => 500, "messages" => 'Please Enter Text']);
        }
    }


    public function submitStrategy($id,Request $request) {


       $store_strategy = SocialStrategy::where('social_strategy_subject_id',$request->subject)->where('website_id',$request->site)->first();

        if(!$store_strategy) {
            $store_strategy = new SocialStrategy;
        }
        if ($request->type == 'description') {
            $store_strategy->description = $request->text;
        }


        if ($request->type == 'execution') {
            $store_strategy->execution_id = $request->text;
        }

        if ($request->type == 'content') {
            $store_strategy->content_id = $request->text;
        }


        $store_strategy->social_strategy_subject_id = $request->subject;
        $store_strategy->website_id   = $request->site;

        $store_strategy->save();

        return response()->json(["code" => 200, "messages" => 'Social strategy Saved Sucessfully']);
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
        $strategy      = null;
        $documents = $request->input('document', []);
        if (!empty($documents)) {
            if ($request->id) {
                $strategy = SocialStrategy::find($request->id);
            }

            if (!$strategy || $request->id == null) {
                $strategy                               = new SocialStrategy;
                $strategy->description                  = "";
                $sistrategyte->website_id                   = $request->store_website_id;
                $strategy->social_strategy_subject_id = $request->site_development_subject_id;
                $strategy->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/' . floor($strategy->id / config('constants.image_per_folder')))
                    ->upload();
                $strategy->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }

    }


    public function listDocuments(Request $request, $id)
    {
        $site = SocialStrategy::find($request->id);

        $userList = [];

        if ($site->execution_id) {
            $userList[$site->execution->id] = $site->execution->name;
        }

        if ($site->content_id) {
            $userList[$site->content->id] = $site->content->name;
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
        $response = \App\SocialStrategyRemark::join("users as u","u.id","social_strategy_remarks.user_id")->where("social_strategy_id",$request->id)
        ->select(["social_strategy_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("social_strategy_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);
    }

    public function saveRemarks(Request $request, $id)
    {
        \App\SocialStrategyRemark::create([
            "remarks" => $request->remark,
            "social_strategy_id" => $request->id,
            "user_id" => \Auth::user()->id,
        ]);

        $response = \App\SocialStrategyRemark::join("users as u","u.id","social_strategy_remarks.user_id")->where("social_strategy_id",$request->id)
        ->select(["social_strategy_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("social_strategy_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);

    }



    
    public function viewSubject(Request $request) {
        $subject = SocialStrategySubject::find($request->id);
        return response()->json(["code" => 200 , "data" => $subject]);
    }

    public function submitSubjectChange(Request $request, $id) {
        $subject = SocialStrategySubject::find($request->id);
        $subject->title = $request->subject_title;
        $subject->save();
        return response()->json(["code" => 200 , "message" => 'Successful']);
    }

	 public function googleKeywordsSearch( Request $request )
    {    
        $title    = $request->title;
        $language = $request->lan;

        // dd( $request->all() );

        try {
            // create & initialize a curl session
            $curl = curl_init();

            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "API_URL");

            // return the transfer as a string, also with setopt()
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // curl_exec() executes the started curl session
            // $output contains the output string
            $output = curl_exec($curl);
            $output = json_decode( $output );

            return response()->json([
                "status"  => true,
                "code"    => 200,
                "data"    => json_encode( $output ),
                "message" => 'Successful'
            ]);

         } catch (\Throwable $th) {
            return response()->json([
                "status"  => false,
                "code"    => 200,
                "message" => $th->getMessage()
            ]);
        }

        return response()->json([
            "status"  => false,
            "code"    => 200,
            "message" => 'Something went wrong!'
        ]);
    }
   
}
