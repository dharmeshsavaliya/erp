<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\AssetsManager;
use App\BuildProcessHistory;
use App\ChatMessage;
use App\Github\GithubRepository;
use App\Jobs\DuplicateStoreWebsiteJob;
use App\LogStoreWebsiteUser;
use App\MagentoDevScripUpdateLog;
use App\MagentoSettingUpdateResponseLog;
use App\ProductCancellationPolicie;
use App\Service;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\SocialStrategy;
use App\SocialStrategySubject;
use App\StoreReIndexHistory;
use App\StoreViewCodeServerMap;
use App\StoreWebsite;
use App\StoreWebsiteAnalytic;
use App\StoreWebsiteAttributes;
use App\StoreWebsiteBrand;
use App\StoreWebsiteCategory;
use App\StoreWebsiteCategorySeo;
use App\StoreWebsiteColor;
use App\StoreWebsiteGoal;
use App\StoreWebsiteImage;
use App\StoreWebsitePage;
use App\StoreWebsiteProduct;
use App\StoreWebsiteProductAttribute;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteProductScreenshot;
use App\StoreWebsitesCountryShipping;
use App\StoreWebsiteSeoFormat;
use App\StoreWebsiteSize;
use App\StoreWebsiteTwilioNumber;
use App\StoreWebsiteUserHistory;
use App\StoreWebsiteUsers;
use App\User;
use App\Website;
use App\WebsiteStoreView;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use seo2websites\MagentoHelper\MagentoHelperv2;

class StoreWebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = 'List | Store Website';
        $services = Service::get();
        $assetManager = AssetsManager::whereNotNull('ip');
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('title')->get();

        return view('storewebsite::index', compact('title', 'services', 'assetManager', 'storeWebsites'));
    }

    public function logWebsiteUsers($id)
    {
        $title = 'List | Store Website User Logs';
        $logstorewebsiteuser = LogStoreWebsiteUser::where('store_website_id', $id)->orderBy('id', 'DESC')->get();

        return view('storewebsite::log_store_website_users', compact('title', 'logstorewebsiteuser'));
    }

    public function cancellation()
    {
        $title = 'Cancellation Policy | Store Website';

        return view('storewebsite::cancellation', compact('title'));
    }

    /**
     * records Page
     *
     * @param  Request  $request [description]
     * @return
     */
    public function records(Request $request)
    {
        $records = StoreWebsite::whereNull('deleted_at')->orderBy('title');

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%")
                    ->orWhere('title', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function saveCancellation(Request $request)
    {
        $id = $request->get('id', 0);
        $checkCacellation = ProductCancellationPolicie::find($id);
        if ($checkCacellation != null) {
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->update();
        } else {
            $checkCacellation = new ProductCancellationPolicie();
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->save();
        }

        return response()->json(['code' => 200, 'data' => $checkCacellation]);
    }

    public function savelogwebsiteuser($log_case_id, $id, $username, $userEmail, $firstName, $lastName, $password, $website_mode, $msg)
    {
        $log = new LogStoreWebsiteUser();
        $log->log_case_id = $log_case_id;
        $log->store_website_id = $id;
        $log->username = $username;
        $log->username = $username;
        $log->useremail = $userEmail;
        $log->first_name = $firstName;
        $log->last_name = $lastName;
        $log->password = $password;
        $log->website_mode = $website_mode;
        $log->log_msg = $msg;
        $log->save();
    }

    /**
     * records Page
     *
     * @param  Request  $request [description]
     * @return
     */
    public function save(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'title' => 'required',
            'website' => 'required',
            'product_markup' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $id = $request->get('id', 0);

        $records = StoreWebsite::find($id);

        if (! $records) {
            $records = new StoreWebsite;
        } else {
            if (! is_null($request->is_debug_true)) {
                if (! $request->server_ip) {
                    $outputString = 'Server IP is required to enable db logs';

                    return response()->json(['code' => 500, 'error' => $outputString]);
                }
                if ($records->is_debug_true !== $request->is_debug_true) {
                    $this->enableDBLog($request);
                }
            }
        }

        if ($request->key_file_path1 != 'undefined' && $request->key_file_path1 != '') {
            $keyPath = public_path('bigData');
            if (! file_exists($keyPath)) {
                mkdir($keyPath, 0777, true);
            }
            $file = $request->file('key_file_path1');
            $keyPathName = uniqid().strtotime(date('YmdHis')).'_'.trim($file->getClientOriginalName());
            $file->move($keyPath, $keyPathName);
            $post['key_file_path'] = $keyPathName;
        }
        if ($post['site_folder'] != '') {
            $post['site_folder'] = $post['site_folder'];
        }
        $records->fill($post);

        $records->save();

        if (isset($post['username'])) {
            $this->savelogwebsiteuser('#1', $post['id'], $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['website_mode'], 'For this Website '.$post['id'].' ,A new user has been created.');
        }
        if ($request->staging_username && $request->staging_password) {
            $message = 'Staging Username: '.$request->staging_username.', Staging Password is: '.$request->staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($request->mysql_username && $request->mysql_password) {
            $message = 'Mysql Username: '.$request->mysql_username.', Mysql Password is: '.$request->mysql_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($request->mysql_staging_username && $request->mysql_staging_password) {
            $message = 'Mysql Staging Username: '.$request->mysql_staging_username.', Mysql Staging Password is: '.$request->mysql_staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($id == 0) {
            $siteDevelopmentCategories = SiteDevelopmentCategory::all();
            foreach ($siteDevelopmentCategories as $develop) {
                $site = new SiteDevelopment;
                $site->site_development_category_id = $develop->id;
                $site->site_development_master_category_id = $develop->master_category_id;
                $site->website_id = $records->id;
                $site->save();
            }
        }

        return response()->json(['code' => 200, 'message' => 'Data successfully saved', 'data' => $records]);
    }

    /**
     * Creates store website from an existing store website and insert necessary data to the corresponding tables
     *
     * @param  Request  $request [description]
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function saveDuplicateStore(Request $request)
    {
//        set_time_limit(0);
        $numberOfDuplicates = $request->get('number') - 1;
        if ($numberOfDuplicates <= 0) {
            return response()->json(['code' => 500, 'error' => 'Number of duplicates must be 1 or more!']);
        }

        $storeWebsiteId = $request->get('id');
        $storeWebsite = StoreWebsite::find($storeWebsiteId);
        $existingDuplicateCount = StoreWebsite::where('parent_id', '=', $storeWebsiteId)->count();
        $numberOfDuplicates = $numberOfDuplicates + $existingDuplicateCount;
        $serverId = 1;
        $response = $this->updateStoreViewServer($storeWebsiteId, $serverId);
        if (! $response) {
            return response()->json(['code' => 500, 'error' => 'Something went wrong in update store view server!']);
        }

        if (! $storeWebsite) {
            return response()->json(['code' => 500, 'error' => 'No website found!']);
        }

        for ($i = $existingDuplicateCount + 1; $i <= $numberOfDuplicates; $i++) {
            $copyStoreWebsite = $storeWebsite->replicate();
            $title = $copyStoreWebsite->title;
            unset($copyStoreWebsite->id);
            unset($copyStoreWebsite->title);
            $copyStoreWebsite->title = $title.' '.$i;
            $copyStoreWebsite->parent_id = $storeWebsiteId;
            $copyStoreWebsite->save();

            \Log::info($copyStoreWebsite->title.' step 1 created.');

            DuplicateStoreWebsiteJob::dispatch($storeWebsiteId, $copyStoreWebsite, $i)->onQueue('sololuxury');

            if ($i == $numberOfDuplicates) {
                return response()->json(['code' => 200, 'error' => 'Store website created successfully']);
            }
        }
    }

    /**
     * Function to update store view server mapping of a store website
     *
     * @param $storeWebsiteId
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     * @return bool
     */
    public function updateStoreViewServer($storeWebsiteId, $serverId)
    {
        $servers = StoreViewCodeServerMap::where('server_id', '=', $serverId)->pluck('code')->toArray();
        $storeViews = WebsiteStoreView::whereIn('code', $servers)->get();
        $count = 0;
        foreach ($storeViews as $key => $view) {
            $storeView = WebsiteStoreView::find($view->id);
            if (! $storeView->websiteStore) {
                \Log::error('Website store not found for '.$view->id.'!');
            } elseif (! $storeView->websiteStore->website) {
                \Log::error('Website not found for '.$view->id.'!');
            } else {
                $websiteId = $view->websiteStore->website->id;
                $website = Website::find($websiteId);
                $website->store_website_id = $storeWebsiteId;
                $response = $website->save();
            }
            $count++;
        }

        if ($response && $count == $key + 1) {
            return true;
        } else {
            \Log::error('Count is not equal to total store views');

            return false;
        }
    }

    public function saveUserInMagento(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'username' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'userEmail' => 'required',
            'password' => 'required',
            'websitemode' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsites = StoreWebsite::where('id', '=', $post['store_id'])->orWhere('parent_id', '=', $post['store_id'])->get();
        $count = 0;
        foreach ($storeWebsites as $key => $storeWebsite) {
            $this->savelogwebsiteuser('#2', $storeWebsite->id, $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['websitemode'], 'For this Website '.$storeWebsite->id.' ,A user has been updated.');

            $checkUserNameExist = '';
            if (! empty($post['store_website_userid'])) {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->where('id', '<>', $post['store_website_userid'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->where('id', '<>', $post['store_website_userid'])->first();
                }
            } else {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->first();
                }
            }

            if (! empty($checkUserExist)) {
                return response()->json(['code' => 500, 'error' => 'User Email already exist!']);
            }
            if (! empty($checkUserNameExist)) {
                return response()->json(['code' => 500, 'error' => 'Username already exist!']);
            }

            $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
            if (! $uppercase || strlen($post['password']) < 7) {
                return response()->json(['code' => 500, 'error' => 'Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters.']);
            }

            if (! empty($post['store_website_userid'])) {
                $getUser = StoreWebsiteUsers::where('id', $post['store_website_userid'])->first();
                $getUser->first_name = $post['firstName'];
                $getUser->last_name = $post['lastName'];
                $getUser->email = $post['userEmail'];
                $getUser->password = $post['password'];
                $getUser->website_mode = $post['websitemode'];
                $getUser->save();

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $getUser->store_website_id,
                    'store_website_user_id' => $getUser->id,
                    'model' => \App\StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'updated',
                    'new_value' => 'updated',
                    'user_id' => Auth::id(),
                ]);

                if ($getUser->is_deleted == 0) {
                    $magentoHelper = new MagentoHelperv2();
                    $magentoHelper->updateMagentouser($storeWebsite, $post);
                }
            } else {
                $params['username'] = $post['username'];
                $params['first_name'] = $post['firstName'];
                $params['last_name'] = $post['lastName'];
                $params['email'] = $post['userEmail'];
                $params['password'] = $post['password'];
                $params['store_website_id'] = $storeWebsite->id;
                $params['website_mode'] = $post['websitemode'];

                $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

                if ($post['userEmail'] && $post['password']) {
                    $message = 'Email: '.$post['userEmail'].', Password is: '.$post['password'];
                    $params['user_id'] = Auth::id();
                    $params['message'] = $message;
                    ChatMessage::create($params);
                }

                $magentoHelper = new MagentoHelperv2();
                $magentoHelper->addMagentouser($storeWebsite, $post);

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $StoreWebsiteUsersid->store_website_id,
                    'store_website_user_id' => $StoreWebsiteUsersid->id,
                    'model' => \App\StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'new_added',
                    'new_value' => 'new_added',
                    'user_id' => Auth::id(),
                ]);
            }
            $count++;
        }
        if ($count == $key + 1) {
            return response()->json(['code' => 200, 'messages' => 'User details saved Successfully']);
        } else {
            return response()->json(['code' => 500, 'messages' => 'Something went wrong']);
        }
    }

    public function deleteUserInMagento(Request $request)
    {
        $post = $request->all();
        $getUser = StoreWebsiteUsers::where('id', $post['store_website_userid'])->first();
        $username = $getUser->username;
        $getUser->is_deleted = 1;
        $getUser->save();

        $this->savelogwebsiteuser('#3', $getUser['store_website_id'], $getUser['username'], $getUser['email'], $getUser['first_name'], $getUser['last_name'], $getUser['password'], $getUser['website_mode'], 'For this Website '.$getUser['store_website_id'].' ,User has been Deleted.');

        $storeWebsite = StoreWebsite::find($getUser->store_website_id);

        $magentoHelper = new MagentoHelperv2();
        $result = $magentoHelper->deleteMagentouser($storeWebsite, $username);

        StoreWebsiteUserHistory::create([
            'store_website_id' => $getUser->store_website_id,
            'store_website_user_id' => $getUser->id,
            'model' => \App\StoreWebsiteUsers::class,
            'attribute' => 'username_password',
            'old_value' => 'delete',
            'new_value' => 'delete',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['code' => 200, 'messages' => 'User Deleted Sucessfully']);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request [description]
     * @return
     */
    public function edit(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        $services = Service::get();
        //->where('is_deleted',0)

        $storewebsiteusers = StoreWebsiteUsers::where('store_website_id', $id)->get();

        if ($storeWebsite) {
            return response()->json([
                'code' => 200,
                'data' => $storeWebsite,
                'userdata' => $storewebsiteusers,
                'services' => $services,
                'totaluser' => count($storewebsiteusers), ]
            );
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function editCancellation(Request $request, $id)
    {
        $storeWebsite = ProductCancellationPolicie::where('store_website_id', $id)->first();
        // $storewebsiteusers = StoreWebsiteUsers::where('store_website_id',$id)->where('is_deleted',0)->get();
        if ($storeWebsite) {
            return response()->json(['code' => 200, 'data' => $storeWebsite]);
        }

        return response()->json(['code' => 200, 'data' => ['store_website_id' => $id]]);
    }

    /**
     * delete Page
     *
     * @param  Request  $request [description]
     * @return
     */
    public function delete(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();

        if ($storeWebsite) {
            $storeWebsite->delete();
            SiteDevelopment::where('website_id', '=', $id)->delete();
            \Log::info('Deleted from SiteDevelopment with id '.$id);
            StoreWebsitesCountryShipping::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsitesCountryShipping with id '.$id);
            StoreWebsiteAnalytic::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteAnalytic with id '.$id);
            StoreWebsiteAttributes::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteAttributes with id '.$id);
            StoreWebsiteBrand::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteBrand with id '.$id);
            StoreWebsiteCategory::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteCategory with id '.$id);
            StoreWebsiteCategorySeo::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteCategorySeo with id '.$id);
            StoreWebsiteColor::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteColor with id '.$id);
            StoreWebsiteGoal::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteGoal with id '.$id);
            StoreWebsiteImage::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteImage with id '.$id);
            StoreWebsiteProduct::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteProduct with id '.$id);
            StoreWebsitePage::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsitePage with id '.$id);
            StoreWebsiteProductAttribute::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteProductAttribute with id '.$id);
            StoreWebsiteProductPrice::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteProductPrice with id '.$id);
            StoreWebsiteProductScreenshot::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteProductScreenshot with id '.$id);
            StoreWebsiteSeoFormat::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteSeoFormat with id '.$id);
            StoreWebsiteSize::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteSize with id '.$id);
            StoreWebsiteTwilioNumber::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteTwilioNumber with id '.$id);
            StoreWebsiteUsers::where('store_website_id', '=', $id)->delete();
            \Log::info('Deleted from StoreWebsiteUsers with id '.$id);

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function updateSocialRemarks(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();

        if ($storeWebsite) {
            $facebook_remarks = $request->get('facebook_remarks');
            if (! empty($facebook_remarks)) {
                $storeWebsite->facebook_remarks = $facebook_remarks;
            }

            $instagram_remarks = $request->get('instagram_remarks');
            if (! empty($instagram_remarks)) {
                $storeWebsite->instagram_remarks = $instagram_remarks;
            }

            $storeWebsite->save();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function socialStrategy($id, Request $request)
    {
        $website = StoreWebsite::find($id);
        $subjects = SocialStrategySubject::orderBy('id', 'desc');

        if ($request->k != null) {
            $subjects = $subjects->where('title', 'like', '%'.$request->k.'%');
        }

        $subjects = $subjects->paginate(Setting::get('pagination'));
        foreach ($subjects as $subject) {
            $subject->strategy = SocialStrategy::where('social_strategy_subject_id', $subject->id)->where('website_id', $id)->first();
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

    public function submitSubject(Request $request)
    {
        if ($request->text) {
            $subjectCheck = SocialStrategySubject::where('title', $request->text)->first();

            if (empty($subjectCheck)) {
                $subject = new SocialStrategySubject;
                $subject->title = $request->text;
                $subject->save();

                return response()->json(['code' => 200, 'messages' => 'Subject Saved Sucessfully']);
            } else {
                return response()->json(['code' => 500, 'messages' => 'Subject Already Exist']);
            }
        } else {
            return response()->json(['code' => 500, 'messages' => 'Please Enter Text']);
        }
    }

    public function submitStrategy($id, Request $request)
    {
        $store_strategy = SocialStrategy::where('social_strategy_subject_id', $request->subject)->where('website_id', $request->site)->first();

        if (! $store_strategy) {
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
        $store_strategy->website_id = $request->site;

        $store_strategy->save();

        return response()->json(['code' => 200, 'messages' => 'Social strategy Saved Sucessfully']);
    }

    public function uploadDocuments(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request)
    {
        $strategy = null;
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            if ($request->id) {
                $strategy = SocialStrategy::find($request->id);
            }

            if (! $strategy || $request->id == null) {
                $strategy = new SocialStrategy;
                $strategy->description = '';
                $strategy->website_id = $request->store_website_id;
                $strategy->social_strategy_subject_id = $request->site_development_subject_id;
                $strategy->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/'.floor($strategy->id / config('constants.image_per_folder')))
                    ->upload();
                $strategy->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
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
        $usrSelectBox = '';
        if (! empty($userList)) {
            $usrSelectBox = (string) \Form::select('send_message_to', $userList, null, ['class' => 'form-control send-message-to-id']);
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'site_id' => $site->id,
                        'user_list' => $usrSelectBox,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function deleteDocument(Request $request)
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();

                return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function sendDocument(Request $request)
    {
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user = \App\User::find($request->user_id);
            if ($user) {
                if ($media) {
                    \App\ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        'Please find attached file',
                        $media->getUrl()
                    );

                    return response()->json(['code' => 200, 'message' => 'Document send succesfully']);
                }
            } else {
                return response()->json(['code' => 200, 'message' => 'User or site is not available']);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Sorry required fields is missing like id, siteid , userid']);
    }

    public function remarks(Request $request, $id)
    {
        $response = \App\SocialStrategyRemark::join('users as u', 'u.id', 'social_strategy_remarks.user_id')->where('social_strategy_id', $request->id)
        ->select(['social_strategy_remarks.*', \DB::raw('u.name as created_by')])
        ->orderBy('social_strategy_remarks.created_at', 'desc')
        ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function saveRemarks(Request $request, $id)
    {
        \App\SocialStrategyRemark::create([
            'remarks' => $request->remark,
            'social_strategy_id' => $request->id,
            'user_id' => \Auth::user()->id,
        ]);

        $response = \App\SocialStrategyRemark::join('users as u', 'u.id', 'social_strategy_remarks.user_id')->where('social_strategy_id', $request->id)
        ->select(['social_strategy_remarks.*', \DB::raw('u.name as created_by')])
        ->orderBy('social_strategy_remarks.created_at', 'desc')
        ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function viewSubject(Request $request)
    {
        $subject = SocialStrategySubject::find($request->id);

        return response()->json(['code' => 200, 'data' => $subject]);
    }

    public function submitSubjectChange(Request $request, $id)
    {
        $subject = SocialStrategySubject::find($request->id);
        $subject->title = $request->subject_title;
        $subject->save();

        return response()->json(['code' => 200, 'message' => 'Successful']);
    }

    public function generateStorefile(Request $request)
    {
        $server = $request->get('for_server');

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'pem-generate.sh '.$server.' 2>&1';

        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        \Log::info(print_r($allOutput, true));

        $string = [];
        if (! empty($allOutput)) {
            $continuetoFill = false;
            foreach ($allOutput as $ao) {
                if ($ao == '-----BEGIN RSA PRIVATE KEY-----' || $continuetoFill) {
                    $string[] = $ao;
                    $continuetoFill = true;
                }
            }
        }

        $content = implode("\n", $string);

        $nameF = $server.'.pem';

        //header download
        header('Content-Disposition: attachment; filename="'.$nameF.'"');
        header('Content-Type: application/force-download');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/x-pem-file');

        echo $content;
        exit;
    }

    public function magentoUserList(Request $request)
    {
        $users = StoreWebsiteUsers::where('is_deleted', 0)->get();

        return response()->json(['code' => 200, 'data' => $users]);
    }

    public function userHistoryList(Request $request)
    {
        $histories = StoreWebsiteUserHistory::with('websiteuser', 'storewebsite')
            ->where('store_website_id', $request->id)
            ->latest()
            ->get();

        $resultArray = [];

        foreach ($histories as $history) {
            $resultArray[] = [
                'date' => $history->created_at->format('Y-m-d H:i:s'),
                'website_mode' => $history->websiteuser->website_mode,
                'username' => $history->websiteuser->username,
                'first_name' => $history->websiteuser->first_name,
                'last_name' => $history->websiteuser->last_name,
                'action' => $history->new_value,
            ];
        }

        return response()->json(['code' => 200, 'data' => $resultArray]);
    }

    public function storeReindexHistory(Request $request)
    {
        $website = StoreWebsite::find($request->id);
        $date = Carbon::now()->subDays(7);
        $histories = StoreReIndexHistory::where('server_name', $website->title)->where('created_at', '>=', $date)
            ->latest()
            ->get();

        $resultArray = [];

        foreach ($histories as $history) {
            $resultArray[] = [
                'date' => $history->created_at->format('Y-m-d H:i:s'),
                'server_name' => $history->server_name,
                'username' => $history->username,
                'action' => $history->action,
            ];
        }

        return response()->json(['code' => 200, 'data' => $resultArray]);
    }

    /**
     * Build Process Page
     *
     * @param  Request  $request [description]
     * @return
     */
    public function buildProcess(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        if ($storeWebsite) {
            return response()->json([
                'code' => 200,
                'data' => $storeWebsite,
            ]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function buildProcessSave(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'reference' => 'required',
            'repository' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }

        if (! empty($request->store_website_id)) {
            $StoreWebsite = StoreWebsite::find($request->store_website_id);

            if ($StoreWebsite != null) {
                $StoreWebsite->build_name = $request->repository;
                $StoreWebsite->repository = $request->repository;
                $StoreWebsite->reference = $request->reference;
                $StoreWebsite->update();

                if ($StoreWebsite) {
                    $jobName = $request->repository;
                    $repository = $request->repository;
                    $ref = $request->reference;
                    $staticdep = 1;

                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:117ed14fbbe668b88696baa43d37c6fb48@build.theluxuryunlimited.com:8080');
                    $jenkins->launchJob($jobName, ['repository' => $repository, 'ref' => $ref, 'staticdep' => 0]);
                    if ($jenkins->getJob($jobName)) {
                        $job = $jenkins->getJob($jobName);
                        $builds = $job->getBuilds();
                        $buildDetail = 'Build Name: '.$jobName.'<br> Build Repository: '.$repository.'<br> Reference: '.$ref;
                        $record = ['store_website_id' => $request->store_website_id, 'created_by' => Auth::id(), 'text' => $buildDetail, 'build_name' => $jobName, 'build_number' => $builds[0]->getNumber()];
                        BuildProcessHistory::create($record);

                        return response()->json(['code' => 200, 'error' => 'Process builed complete successfully.']);
                    } else {
                        return response()->json(['code' => 500, 'error' => 'Please try again, Jenkins job not created']);
                    }
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }
    }

    /**
     * This function is use to add company website address.
     *
     * @param  Request  $request
     * @param  int  $store_website_id
     * @return JsonResponce
     */
    public function addCompanyWebsiteAddress(Request $request, $store_website_id)
    {
        $StoreWebsite = StoreWebsite::where('id', '=', $store_website_id)->first();
        if ($StoreWebsite != null) {
            return response()->json([
                'code' => 200,
                'data' => $StoreWebsite,
            ]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function magentoDevScriptUpdate(Request $request)
    {
        try {
            $run = \Artisan::call('command:MagentoDevUpdateScript', ['id' => $request->id, 'folder_name' => $request->folder_name]);

            return response()->json(['code' => 200, 'message' => 'Magento Setting Updated successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getMagentoUpdateWebsiteSetting(Request $request, $store_website_id)
    {
        try {
            $responseLog = MagentoSettingUpdateResponseLog::where('website_id', '=', $store_website_id)->get();
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 100, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento setting updated successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function getFolderName(Request $request)
    {
        //$assetManager = AssetsManager::where('id', $request->id);
    }

    public function getMagentoDevScriptUpdatesLogs(Request $request, $store_website_id)
    {
        try {
            $responseLog = MagentoDevScripUpdateLog::where('store_website_id', '=', $store_website_id)->get();
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="website" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-website-'.$res->id.'">'.Str::limit($res->website, 15, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-website-'.$res->id.' hidden">'.$res->website.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="command" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-command-'.$res->id.'">'.Str::limit($res->command_name, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-command-'.$res->id.' hidden">'.$res->command_name.'</span>
                    </td>';

                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento setting updated successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    /**
     * This function is use to Update company's website address.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateCompanyWebsiteAddress(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'website_address' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }

        if (! empty($request->store_website_id)) {
            $StoreWebsite = StoreWebsite::find($request->store_website_id);
            if ($StoreWebsite != null) {
                $StoreWebsite->website_address = $request->website_address;
                $StoreWebsite->update();

                return response()->json(['code' => 200, 'message' => 'Address has been saved']);
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }
    }

    public function syncStageToMaster($storeWebId)
    {
        $websiteDetails = StoreWebsite::where('id', $storeWebId)->select('server_ip', 'repository_id')->first();
        if ($websiteDetails != null and $websiteDetails['server_ip'] != null and $websiteDetails['repository_id'] != null) {
            $repo = GithubRepository::where('id', $websiteDetails['repository_id'])->pluck('name')->first();
            if ($repo != null) {
                $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'sync-staticfiles.sh -r '.$repo.' -s '.$websiteDetails['server_ip'];
                $allOutput = [];
                $allOutput[] = $cmd;
                $result = exec($cmd, $allOutput); //Execute command
                \Log::info(print_r(['Command Output', $allOutput], true));

                return response()->json(['code' => 200, 'message' => 'Command executed']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Repository Not found.']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Request has been failed.']);
        }
    }

    public function enableDBLog($website)
    {
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-debug.sh --server '.$website->server_ip.' --debug '.($website->is_debug_true ? 'true' : 'false').' 2>&1';
        \Log::info('[SatyamTest] '.$cmd);
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);
        \Log::info(print_r($allOutput, true));

        return $result;
    }

    public function checkMagentoToken(Request $request)
    {
        $token = $request->id;
        $magentoHelper = new MagentoHelperv2();
        $result = $magentoHelper->checkToken($token, $request->url);
        if ($result) {
            return response()->json(['code' => 200, 'message' => 'Token is valid']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Token is invalid']);
        }
    }

    public function generateApiToken(Request $request)
    {
        $apiTokens = $request->api_token;

        if ($request->api_token) {
            foreach ($apiTokens as $key => $apiToken) {
                StoreWebsite::where('id', $key)->update(['api_token' => $apiToken, 'server_ip' => $request->server_ip[$key]]);
            }
            session()->flash('msg', 'Api Token Updated Successfully.');

            return redirect()->back();
        } else {
            session()->flash('msg', 'Api Token is invalid.');

            return redirect()->back();
        }
    }

    public function getApiToken(Request $request)
    {
        $search = $request->search;
        $storeWebsites = StoreWebsite::whereNull('deleted_at');
        if ($search != null) {
            $storeWebsites = $storeWebsites->where('title', 'Like', '%'.$search.'%');
        }
        $storeWebsites = $storeWebsites->get();

        return response()->json([
            'tbody' => view('storewebsite::api-token', compact('storeWebsites'))->render(),

        ], 200);
    }
}
