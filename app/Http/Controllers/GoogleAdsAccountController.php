<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
use stdClass;
use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Session;

class GoogleAdsAccountController extends Controller
{
    // show campaigns in main page
    public function index(Request $request)
    {
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campInfo = $this->getCampaigns($adWordsServices, $session); */
        $query=\App\GoogleAdsAccount::query();
        if($request->website){
			$query = $query->where('store_websites', $request->website);
		}
		if($request->accountname){
			$query = $query->where('account_name', 'LIKE','%'.$request->accountname.'%');
		}

        $googleadsaccount = $query->orderby('id','desc')->paginate(25)->appends(request()->except(['page']));
		if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleadsaccounts.partials.list-adsaccount', compact('googleadsaccount'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$googleadsaccount->render(),
                'count' => $googleadsaccount->total(),
            ], 200);
        }

        $store_website=\App\StoreWebsite::all();
        $totalentries = $googleadsaccount->count();
        return view('googleadsaccounts.index', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries,'store_website'=>$store_website]);
    }
    
    public function createGoogleAdsAccountPage()
    {
        $store_website=\App\StoreWebsite::all();
        return view('googleadsaccounts.create',['store_website'=>$store_website]);
    }

    public function createGoogleAdsAccount(Request $request)
    {
        //create account
        $this->validate($request, [
            'account_name' => 'required',
            'store_websites' => 'required',
            'config_file_path' => 'required',
            'status' => 'required',
        ]);

        $accountArray = array(
            'account_name' => $request->account_name,
            'store_websites' => $request->store_websites,
            'notes' => $request->notes,
            'status' => $request->status,
        );
        $googleadsAc = \App\GoogleAdsAccount::create($accountArray);
        $account_id = $googleadsAc->id;
        if($request->file('config_file_path')){
            $uploadfile = MediaUploader::fromSource($request->file('config_file_path'))
                ->toDestination('adsapi', $account_id)
                ->upload();
            $getfilename = $uploadfile->filename . '.' . $uploadfile->extension;
            $googleadsAc->config_file_path = $getfilename;
            $googleadsAc->save();
        }
        return redirect()->to('/google-campaigns/ads-account')->with('actSuccess', 'GoogleAdwords account details added successfully');
    }

    public function editeGoogleAdsAccountPage($id)
    {
        $store_website=\App\StoreWebsite::all();
        $googleAdsAc=\App\GoogleAdsAccount::find($id);
        return view('googleadsaccounts.update',['account'=>$googleAdsAc,'store_website'=>$store_website]);
    }

    public function updateGoogleAdsAccount(Request $request)
    {
        $account_id = $request->account_id;
        //update account
        $this->validate($request, [
            'account_name' => 'required',
            'store_websites' => 'required',
            'status' => 'required',
        ]);

        $accountArray = array(
            'account_name' => $request->account_name,
            'store_websites' => $request->store_websites,
            'notes' => $request->notes,
            'status' => $request->status,
        );
        $googleadsAcQuery = New \App\GoogleAdsAccount;
        $googleadsAc=$googleadsAcQuery->find($account_id);
        if($request->file('config_file_path')){
            //find old one
            if(isset($googleadsAc->config_file_path) && $googleadsAc->config_file_path!="" && \Storage::disk('adsapi')->exists($account_id.'/'.$googleadsAc->config_file_path)){
                \Storage::disk('adsapi')->delete($account_id.'/'.$googleadsAc->config_file_path);
            }
            $uploadfile = MediaUploader::fromSource($request->file('config_file_path'))
                ->toDestination('adsapi', $account_id)
                ->upload();
            $getfilename = $uploadfile->filename . '.' . $uploadfile->extension;
            $accountArray['config_file_path'] = $getfilename;
        }
        $googleadsAc->fill($accountArray);
        $googleadsAc->save();
        return redirect()->to('/google-campaigns/ads-account')->with('actSuccess', 'GoogleAdwords account details added successfully');
    }

    /*
    * used to get google refresh token for ads
    */
    public function refreshToken(Request $request){
        $google_redirect_url = route('googleadsaccount.get-refresh-token');

        $PRODUCTS = [
            ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
            ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
            ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE') . ' '
                . config('google.GOOGLE_ADS_MANAGER_API_SCOPE')]
        ];

        $client_id = $request->client_id;
        $client_secret = $request->client_secret;
        Session::put('client_id', $client_id);
        Session::put('client_secret', $client_secret);
        

        $api = intval(2);

        $scopes = $PRODUCTS[$api][1];

        $oauth2 = new OAuth2( 
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $client_id,
                'clientSecret' => $client_secret,
                'scope' => $scopes
            ]
        );
        
        header('Location: '.$oauth2->buildFullAuthorizationUri());
    }

    /*
    * Refresh token Redirect API
    */
    public function getRefreshToken(Request $request){
        $google_redirect_url = route('googleadsaccount.get-refresh-token');
        $api = intval(2);
        $PRODUCTS = [
            ['AdWords API', config('google.GOOGLE_ADS_WORDS_API_SCOPE')],
            ['Ad Manager API', config('google.GOOGLE_ADS_MANAGER_API_SCOPE')],
            ['AdWords API and Ad Manager API', config('google.GOOGLE_ADS_WORDS_API_SCOPE') . ' '
                . config('google.GOOGLE_ADS_MANAGER_API_SCOPE')]
        ];
        $scopes = $PRODUCTS[$api][1];
        $oauth2 = new OAuth2( 
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => Session::get('client_id'),
                'clientSecret' => Session::get('client_secret'),
                'scope' => $scopes
            ]
        );
        if($request->code) {
            $code = $request->code;  
            $oauth2->setCode($code);
            $authToken = $oauth2->fetchAuthToken(); 
            Session::forget('client_secret');
            Session::forget('client_id');
            return view('googleadsaccounts.view_token',['refresh_token'=>$authToken['refresh_token'], 'access_token' => $authToken['access_token']]);
        }else{
            return redirect('/google-campaigns/ads-account')->with('message','Unable to Get Tokens ');
        }

    }
}
