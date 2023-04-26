<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use Carbon\Carbon;
use Google_Client;
use App\Validator;
use Google\Auth\OAuth2;
use Google_Service_YouTube;
use Illuminate\Http\Request;
use App\Models\YoutubeVideo;
use App\Models\YoutubeChannel;
use App\Models\YoutubeComment;
use App\Library\Youtube\Helper;
use Google_Service_YouTube_Video;
use Google\Auth\CredentialsLoader;
use App\Models\StoreWebsiteYoutube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchYoutubeChannelData;
use Google_Service_YouTube_VideoStatus;
use Laravel\Socialite\Facades\Socialite;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoContentDetails;

// require_once __DIR__.'/../../vendor/autoload.php';

class YoutubeController extends Controller
{

    /*
    * used to get google refresh token for ads
    */
    public function refreshToken(Request $request)
    {
        $google_redirect_url = route('youtubeaccount.get-refresh-token');

        $PRODUCTS = [
            ['YouTube API', config('youtube.YOUTUBE_API_SCOPE')],
        ];

        $client_id = $request->client_id;
        $client_secret = $request->client_secret;
        Session::put('client_id', $client_id);
        Session::put('client_secret', $client_secret);
        Session::save();

        $api = intval(0);

        $scopes = ['Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl', 'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit', 'Youtube3' => 'https://www.googleapis.com/auth/youtube.upload'];


        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('youtube.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $client_id,
                'clientSecret' => $client_secret,
                'scope' => $scopes,
            ]
        );

        $authUrl = $oauth2->buildFullAuthorizationUri([
            'prompt' => 'consent',
        ]);

        $authUrl = filter_var($authUrl, FILTER_SANITIZE_URL);

        return redirect()->away($authUrl);
    }

    public function viewUploadVideo(Request $request, $id)
    {

        $chaneltableData = YoutubeChannel::where('id', $id)->first();

        if (empty($chaneltableData)) {

            return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
        }
        $chanelTableId = $chaneltableData->id;

        $accessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
        // $categoriesData= Helper::getVideoCategories($accessToken, $chaneltableData->id);
        $categoriesData = Helper::getVideoCategories();


        return view('youtube.chanel.video.create', compact('chanelTableId', 'categoriesData'));
    }

    public function uploadVideo(Request $request)
    {

        try {

            $this->validate($request, [
                'videoCategories' => 'required',
                'status' => 'required',
                'title' => 'required',
                'description' => 'required',
                'youtubeVideo' => 'required',
            ]);



            $chaneltableData = YoutubeChannel::where('id', $request->tableChannelId)->first();
            if (empty($chaneltableData)) {
                return redirect()->to('/youtube/add-chanel')->with('actError', 'Data Not Found');
            }
            Helper::regenerateToken($chaneltableData->id);

            $accessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
            if (empty($accessToken)) {
                return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
            }

            // @ini_set('upload_max_size', '64M');
            // @ini_set('post_max_size', '64M');
            // @ini_set('max_execution_time', '300');
            $client = new Google_Client();
            $client->setApplicationName('Youtube Upload video');
            $client->setScopes([
                'https://www.googleapis.com/auth/youtube.upload',
            ]);

            // $client->setAuthConfig(public_path('credentialYoutube.json'));
            $client->setAccessToken($accessToken);

            $client->setAccessType('offline');

            $service = new Google_Service_YouTube($client);


            $video = new Google_Service_YouTube_Video();
            //$videoContentDetails = new Google_Service_YouTube_VideoContentDetails();
            // $video->setContentDetails($videoContentDetails);


            $videoSnippet = new Google_Service_YouTube_VideoSnippet();

            $videoSnippet->setCategoryId($request->videoCategories);
            // $videoSnippet->setChannelId($chaneltableData->chanelId);
            $videoSnippet->setDescription($request->description);
            $videoSnippet->setTitle($request->title);
            $videoSnippet->setPublishedAt(now());
            $video->setSnippet($videoSnippet);


            // Add 'status' object to the $video object.
            $videoStatus = new Google_Service_YouTube_VideoStatus();
            $videoStatus->setPrivacyStatus($request->status);
            $video->setStatus($videoStatus);

            $response = $service->videos->insert(
                'snippet,status',
                $video,
                array(
                    'data' => \File::get($request->file('youtubeVideo')),
                    'mimeType' => 'application/octet-stream',
                    'uploadType' => 'multipart'
                )
            );

            if (!empty($response['id'])) {
                if (!empty($chaneltableData->oauth2_refresh_token)) {
                    $accessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
                    if (!empty($accessToken)) {
                        Helper::getVideoAndInsertDB($chaneltableData->id, $accessToken, $chaneltableData->chanelId);
                    }
                }

                return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Upload Video Successfully!');
            }
        } catch (Exception $e) {

            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }




    public function createChanel(Request $request)
    {
        //create account
        $this->validate($request, [
            'store_websites' => 'required',
            // 'config_file_path' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
        ]);

        try {

            $input = $request->all();
            $createChannel = YoutubeChannel::create($input);
            FetchYoutubeChannelData::dispatch($createChannel);
            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Youtube Channel added successfully');
        } catch (Exception $e) {
            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }

    /*
    * Refresh token Redirect API
    */
    public function getRefreshToken(Request $request)
    {
        $google_redirect_url = route('youtubeaccount.get-refresh-token');
        $api = intval(0);
        // $PRODUCTS = [
        //     ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
        //     ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
        //     ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE').' '
        //         .config('google.GOOGLE_ADS_MANAGER_API_SCOPE'), ],
        // ];
        $PRODUCTS = [
            ['YouTube API', config('youtube.YOUTUBE_API_SCOPE')],
        ];

        $scopes = ['Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl', 'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit'];

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => Session::get('client_id'),
                'clientSecret' => Session::get('client_secret'),
                'scope' => $scopes,
            ]
        );
        if ($request->code) {
            $code = $request->code;
            $oauth2->setCode($code);
            $authToken = $oauth2->fetchAuthToken();
            Session::forget('client_secret');
            Session::forget('client_id');

            return view('youtube.chanel.view_token', ['refresh_token' => $authToken['refresh_token'], 'access_token' => $authToken['access_token']]);
        } else {
            return redirect('/youtube/add-chanel')->with('message', 'Unable to Get Tokens ');
        }
    }

    public function youtubeRedirect(Request $request)
    {
        return Socialite::driver('youtube')->with(['state' => $request->id, 'access_type' => 'offline', 'prompt' => 'consent select_account', 'scope' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit', 'scope' => 'https://www.googleapis.com/auth/youtube.force-ssl'])->redirect();
    }

    public function updateYoutubeAccessToken($websiteId)
    {
        try {
            $websiteData =  StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();

            $params = [
                'refresh_token' => $websiteData->refresh_token,
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com'
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $websiteData->access_token = $response['access_token'];
            $expireIn = !empty($response['expires_in']) ? $response['expires_in'] : null;
            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }
            $websiteData->token_expire_time = $expireIn;
            $websiteData->save();
        } catch (\Exception $e) {
            Log::info(__('failedToUpdateUserAccessToken', [$websiteData]));
            Log::info($e->getMessage());
        }
    }

    public function creteChanel(Request $request)
    {
        // Create Chanel Means Get Chanel Data  using refresh Token.
        $query = YoutubeChannel::query();
        if ($request->website) {
            $query = $query->where('store_websites', $request->website);
        }

        // Account name meand Channel name

        if ($request->accountname) {
            $query = $query->where('chanel_name', 'LIKE', '%' . $request->accountname . '%');
        }

        $googleadsaccount = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('youtube.chanel.filter-channel', compact('googleadsaccount'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $googleadsaccount->render(),
                'count' => $googleadsaccount->total(),
            ], 200);
        }

        $store_website = \App\StoreWebsite::all();
        $totalentries = $googleadsaccount->count();

        return view('youtube.chanel.chanel-create', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries, 'store_website' => $store_website]);
    }

    public function GetChanelData()
    {
        $user  = Socialite::driver('youtube')->stateless()->user();
        $websiteId = request()->input('state');
        $socialsObj = StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();

        if (empty($socialsObj)) {
            $expireIn = !empty($user->accessTokenResponseBody['expires_in']) ? $user->accessTokenResponseBody['expires_in'] : null;
            if (!empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }

            $data = [
                'access_token' => !empty($user->accessTokenResponseBody['access_token']) ? $user->accessTokenResponseBody['access_token'] : null,
                'refresh_token' => !empty($user->accessTokenResponseBody['refresh_token']) ? $user->accessTokenResponseBody['refresh_token'] : null,
                'store_website_id' => !empty(request()->input('state')) ? request()->input('state') : null,
                'token_expire_time' => $expireIn

            ];
            StoreWebsiteYoutube::create($data);
        }
        $this->regenerateToken($websiteId);


        if (!empty($user)) {
            return redirect()->route('chanelList', ['website_id' => $websiteId]);
        }

        abort(404);
    }

    public function VideoListByChanelId(Request $request)
    {

        $websiteId = !empty($request->route('websiteId')) ? $request->route('websiteId') : null;
        $chanelId = !empty($request->route('chanelId')) ? $request->route('chanelId') : null;
        if (empty($websiteId) || empty($chanelId)) {
            abort(404);
        }
        $accessToken = $this->getAccessToken($websiteId);
        $this->regenerateToken($websiteId);

        $videoIds = $this->getVideoIds($accessToken, $chanelId);

        $videoData = $this->getVideo($accessToken, $videoIds);
        return view('youtube.chanel.video.video-list', compact('videoData', 'websiteId'));
    }

    public function CommentByVideoId(Request $request, $videoId)
    {
        // $chaneltableData = YoutubeComment::where('video_id', $videoId)->first();

        // if (empty($chaneltableData)) {

        //     return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
        // }
        $query = YoutubeComment::query();
        $commentsList =  $query->where('video_id', $videoId)->paginate(10)->appends(request()->except(['page']));
        return view('youtube.chanel.comment.comment-list', compact('commentsList'));
    }


    public function editChannel($id)
    {
        $store_website = \App\StoreWebsite::all();
        $googleAdsAc = YoutubeChannel::findOrFail($id);
        return $googleAdsAc;
    }

    public function updateChannel(Request $request)
    {
       
        $account_id = $request->account_id;
       
;        //update account
        //create account
        $this->validate($request, [
            'store_websites' => 'required',
            // 'config_file_path' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'oauth2_client_id' => 'required',
            'oauth2_client_secret' => 'required',
            'oauth2_refresh_token' => 'required',
        ]);
      
        try {
            $input = $request->all();

            $googleadsAcQuery = new YoutubeChannel();
            $googleadsAc = $googleadsAcQuery->find($account_id);
            $googleadsAc->fill($input);
            $googleadsAc->save();

            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Channel updated successfully');
        } catch (Exception $e) {
           
            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }


    public function listVideo(Request $request, $youtubeChannelTableId)
    {
        $chaneltableData = YoutubeChannel::where('id', $youtubeChannelTableId)->first();

        if (empty($chaneltableData)) {

            return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
        }
        $query = YoutubeVideo::query();
        $videoList =  $query->where('channel_id', $chaneltableData->chanelId)->paginate(5)->appends(request()->except(['page']));
        // $videoList = YoutubeVideo::where('channel_id', $chaneltableData->chanelId)->paginate(5);

        return view('youtube.chanel.video.video-list', compact('videoList'));
    }
}