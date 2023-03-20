<?php

namespace App\Http\Controllers;

use Exception;
use Google\Ads\GoogleAds\Examples\Utils\Helper;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V12\Enums\AdGroupTypeEnum\AdGroupType;
use Google\Ads\GoogleAds\V12\Resources\AdGroup;
use Google\Ads\GoogleAds\V12\Services\AdGroupOperation;
use Google\Ads\GoogleAds\V12\Services\KeywordAndUrlSeed;
use Google\Ads\GoogleAds\V12\Services\KeywordSeed;
use Google\Ads\GoogleAds\V12\Services\UrlSeed;
use Google\Ads\GoogleAds\V12\Enums\KeywordPlanNetworkEnum\KeywordPlanNetwork;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V12\Common\KeywordInfo;
use Google\Ads\GoogleAds\V12\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V12\Enums\AdGroupCriterionStatusEnum\AdGroupCriterionStatus;
use Google\Ads\GoogleAds\V12\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V12\Services\AdGroupCriterionOperation;
use Illuminate\Http\Request;
use App\Models\GoogleAdGroupKeyword;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleResponsiveDisplayAdMarketingImage;
use App\Models\GoogleAppAd;
use App\Models\GoogleAppAdImage;
use App\GoogleAd;

class GoogleAdGroupController extends Controller
{
    const PAGE_LIMIT = 500;

    const CPC_BID_MICRO_AMOUNT = null;

    public $exceptionError = 'Something went wrong';

    // show campaigns in main page
    public function getstoragepath($account_id)
    {
        $result = \App\GoogleAdsAccount::find($account_id);
        if (\Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = \Storage::disk('adsapi')->url($account_id.'/'.$result->config_file_path);
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);
            /* echo $storagepath; exit;
        echo storage_path('adsapi_php.ini'); exit; */
            /* echo '<pre>' . print_r($result, true) . '</pre>';
            die('developer working'); */
            return $storagepath;
        } else {
            abort(404, 'Please add adspai_php.ini file');
        }
    }

    public function getAccountDetail($campaignId)
    {
        $campaignDetail = \App\GoogleAdsCampaign::where('google_campaign_id', $campaignId)->first();
        if ($campaignDetail->exists() > 0) {
            return [
                'account_id' => $campaignDetail->account_id,
                'campaign_name' => $campaignDetail->campaign_name,
                'google_customer_id' => $campaignDetail->google_customer_id,
                'campaign_channel_type' => $campaignDetail->channel_type,
            ];
        } else {
            abort(404, 'Invalid account!');
        }
    }

    public function index(Request $request, $campaignId)
    {
        /* // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile(storage_path('adsapi_php.ini'))->build();

        // Construct an API session configured from a properties file and the
        // OAuth2 credentials above.
        $session = (new AdWordsSessionBuilder())->fromFile(storage_path('adsapi_php.ini'))->withOAuth2Credential($oAuth2Credential)->build();

        $adGroups = $this->getAdGroups(new AdWordsServices(), $session, $campaignId);
        return view('googleadgroups.index', ['adGroups' => $adGroups['adGroups'], 'totalNumEntries' => $adGroups['totalNumEntries'], 'campaignId' => $campaignId]); */
        $acDetail = $this->getAccountDetail($campaignId);
        $campaign_account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $campaign_channel_type = $acDetail['campaign_channel_type'];

        $query = \App\GoogleAdsGroup::query();

        if ($request->googlegroup_name) {
            $query = $query->where('ad_group_name', 'LIKE', '%'.$request->googlegroup_name.'%');
        }

        if ($request->bid) {
            $query = $query->where('bid', 'LIKE', '%'.$request->bid.'%');
        }

        if ($request->googlegroup_id) {
            $query = $query->where('google_adgroup_id', $request->googlegroup_id);
        }

        if ($request->adsgroup_status) {
            $query = $query->where('status', $request->adsgroup_status);
        }

        $query->where('adgroup_google_campaign_id', $campaignId);
        $adGroups = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleadgroups.partials.list-adsgroup', ['adGroups' => $adGroups, 'campaignId' => $campaignId, 'campaign_channel_type' => $campaign_channel_type])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $adGroups->render(),
                'count' => $adGroups->total(),
            ], 200);
        }

        $totalEntries = $adGroups->total();

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Ad Group',
                    'message' => "Viewed ad group listing for ". $campaign_name
                );
        insertGoogleAdsLog($input);

        return view('googleadgroups.index', ['adGroups' => $adGroups, 'totalNumEntries' => $totalEntries, 'campaignId' => $campaignId, 'campaign_name' => $campaign_name, 'campaign_account_id' => $campaign_account_id, 'campaign_channel_type' => $campaign_channel_type]);
    }

    // getting all Ad Groups of specific campaign
    public function getAdGroups(AdWordsServices $adWordsServices, AdWordsSession $session, $campaignId)
    {
        $adGroupService = $adWordsServices->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        $selector = new Selector();
        $selector->setFields(['Id', 'Name', 'Status', 'CpcBid']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [new Predicate('CampaignId', PredicateOperator::IN, [$campaignId])]
        );
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $totalNumEntries = 0;
        $adGroups = [];
        do {
            // Retrieve ad groups one page at a time, continuing to request pages
            // until all ad groups have been retrieved.
            $page = $adGroupService->get($selector);

            // Print out some information for each ad group.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $adGroup) {
                    $adGroups[] = [
                        'adGroupId' => $adGroup->getId(),
                        'name' => $adGroup->getName(),
                        'status' => $adGroup->getStatus(),
                        'bidAmount' => $adGroup->getBiddingStrategyConfiguration()->getBids()[0]->getBid()->getMicroAmount() / 1000000,
                    ];
                }
            }

            $selector->getPaging()->setStartIndex(
                $selector->getPaging()->getStartIndex() + self::PAGE_LIMIT
            );
        } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'totalNumEntries' => $totalNumEntries,
            'adGroups' => $adGroups,
        ];
    }

    // got to ad group create page
    public function createPage($campaignId)
    {
        //
        $acDetail = $this->getAccountDetail($campaignId);
        $campaign_name = $acDetail['campaign_name'];
        $campaign_channel_type = $acDetail['campaign_channel_type'];

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Ad Group',
                    'message' => "Viewed create ad group for ". $campaign_name
                );
        insertGoogleAdsLog($input);

        return view('googleadgroups.create', ['campaignId' => $campaignId, 'campaign_name' => $campaign_name, 'campaign_channel_type' => $campaign_channel_type]);
    }

    // create ad group
    public function createAdGroup(Request $request, $campaignId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $customerId = $acDetail['google_customer_id'];
        $campaign_channel_type = $acDetail['campaign_channel_type'];

        $rules = array('adGroupName' => 'required|max:55');
        if($campaign_channel_type != 'MULTI_CHANNEL'){
            $rules['microAmount'] = 'required';              
        }

        $this->validate($request, $rules);

        try {
            $adGroupStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            // $criterionTypeGroups = ['KEYWORD', 'USER_INTEREST_AND_LIST', 'VERTICAL', 'GENDER', 'AGE_RANGE', 'PLACEMENT', 'PARENT', 'INCOME_RANGE', 'NONE', 'UNKNOWN'];
            // $adRotationModes = ['UNKNOWN', 'OPTIMIZE', 'ROTATE_FOREVER'];
            $addgroupArray = [];
            $adGroupName = $request->adGroupName;
            $microAmount = $request->microAmount * 1000000;
            $adGroupStatus = $adGroupStatusArr[$request->adGroupStatus];

            $storagepath = $this->getstoragepath($account_id);
            $addgroupArray['adgroup_google_campaign_id'] = $campaignId;
            $addgroupArray['google_customer_id'] = $customerId;
            $addgroupArray['ad_group_name'] = $adGroupName;
            $addgroupArray['bid'] = $request->microAmount ?? null;
            $addgroupArray['status'] = $adGroupStatus;
            // $criterionTypeGroup = $criterionTypeGroups[$request->criterionTypeGroup];
            // $adRotationMode = $adRotationModes[$request->adRotationMode];

            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

            // Constructs another ad group.
            $addgroupArr = array(
                                'name' => $adGroupName,
                                'campaign' => $campaignResourceName,
                                'status' => self::getAdGroupStatus($adGroupStatus),
                            );

            if($campaign_channel_type != 'MULTI_CHANNEL'){
                $addgroupArr['cpc_bid_micros'] = $microAmount;
            }

            $adGroup = new AdGroup($addgroupArr);

            $adGroupOperation = new AdGroupOperation();
            $adGroupOperation->setCreate($adGroup);

            // Issues a mutate request to add the ad groups.
            $adGroupServiceClient = $googleAdsClient->getAdGroupServiceClient();
            $response = $adGroupServiceClient->mutateAdGroups(
                $customerId,
                [$adGroupOperation]
            );

            $addedAdGroup = $response->getResults()[0];
            $adGroupResourceName = $addedAdGroup->getResourceName();
            $adGroupId = substr($adGroupResourceName, strrpos($adGroupResourceName, "/") + 1);
            
            $addgroupArray['google_adgroup_id'] = $adGroupId;
            $addgroupArray['adgroup_response'] = json_encode($addedAdGroup);
            \App\GoogleAdsGroup::create($addgroupArray);

            // Start keyword
            $keywords = $request->suggested_keywords;

            if(!empty($keywords)){
                
                ini_set('max_execution_time', -1);

                $keywordArr = array_slice(explode(",", $keywords), 0, 80);

                foreach($keywordArr as $key => $keyword){
                    $keyword = substr($keyword, 0, 80);

                    $keywordInfo = new KeywordInfo([
                        'text' => $keyword,
                        'match_type' => KeywordMatchType::EXACT
                    ]);  

                    // Constructs an ad group criterion using the keyword text info above.
                    $adGroupCriterion = new AdGroupCriterion([
                        'ad_group' => ResourceNames::forAdGroup($customerId, $adGroupId),
                        'status' => AdGroupCriterionStatus::ENABLED,
                        'keyword' => $keywordInfo
                    ]);

                    $adGroupCriterionOperation = new AdGroupCriterionOperation();
                    $adGroupCriterionOperation->setCreate($adGroupCriterion);

                    // Issues a mutate request to add the ad group criterion.
                    $adGroupCriterionServiceClient = $googleAdsClient->getAdGroupCriterionServiceClient();
                    $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
                        $customerId,
                        [$adGroupCriterionOperation]
                    );

                    $addedKeyword = $response->getResults()[0];
                    $keywordResourceName = $addedKeyword->getResourceName();
                    if(!empty($keywordResourceName)){
                        $keywordId = substr($keywordResourceName, strrpos($keywordResourceName, "~") + 1);

                        $inputKeyword = array(
                                        'google_customer_id' => $customerId,
                                        'adgroup_google_campaign_id' => $campaignId,
                                        'google_adgroup_id' => $adGroupId,
                                        'google_keyword_id' => $keywordId,
                                        'keyword' => $keyword,
                                        'created_at'=> date("Y-m-d H:i:s"),
                                        'updated_at'=> date("Y-m-d H:i:s")
                                    );

                        GoogleAdGroupKeyword::updateOrCreate(
                                            [
                                                'google_adgroup_id' => $adGroupId,
                                                'keyword' => $keyword,
                                            ],
                                            $inputKeyword
                                        );
                    }
                }
            }
            // End keyword

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Ad Group',
                        'message' => "Created ad group for ". $campaign_name,
                        'response' => json_encode($addgroupArray)
                    );
            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups')->with('actSuccess', 'Adsgroup added successfully');
        } catch (Exception $e) {

            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Ad Group',
                        'message' => "Create ad group > ". $e->getMessage()
                    );
            insertGoogleAdsLog($input);

            return redirect()->back()->with('actError', $this->exceptionError);
        }
    }

    // go to update page
    public function updatePage(Request $request, $campaignId, $adGroupId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $campaign_channel_type = $acDetail['campaign_channel_type'];

        $storagepath = $this->getstoragepath($account_id);
        // $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile($storagepath)->build();

        // // Construct an API session configured from a properties file and the
        // // OAuth2 credentials above.
        // $session = (new AdWordsSessionBuilder())->fromFile($storagepath)->withOAuth2Credential($oAuth2Credential)->build();

        // $adGroupService = (new AdWordsServices())->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        /* $selector = new Selector();
        $selector->setFields(['Id', 'Name', 'Status', 'CampaignId', 'CampaignName', 'CpcBid']);
        $selector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $selector->setPredicates(
            [new Predicate('CampaignId', PredicateOperator::IN, [$campaignId]),
             new Predicate('Id', PredicateOperator::IN, [$adGroupId])]
        );
        $selector->setPaging(new Paging(0, self::PAGE_LIMIT));

        $page = $adGroupService->get($selector);
        $pageEntries = $page->getEntries();

        $adGroup = [];
        if ($pageEntries !== null) {
            $adGroup = $pageEntries[0];
        }

        $adGroup = [
            'adGroupId' => $adGroup->getId(),
            'name' => $adGroup->getName(),
            'status' => $adGroup->getStatus(),
            'bidAmount' => $adGroup->getBiddingStrategyConfiguration()->getBids()[0]->getBid()->getMicroAmount() / 1000000
        ]; */
        $adGroup = \App\GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->where('adgroup_google_campaign_id', $campaignId)->firstOrFail();

        // Insert google ads log 
        $input = array(
                    'type' => 'SUCCESS',
                    'module' => 'Ad Group',
                    'message' => "Viewed update ad group for ". $adGroup->ad_group_name
                );
        insertGoogleAdsLog($input);

        return view('googleadgroups.update', ['adGroup' => $adGroup, 'campaignId' => $campaignId , 'campaign_channel_type' => $campaign_channel_type]);
    }

    // update ad group
    public function updateAdGroup(Request $request, $campaignId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $customerId = $acDetail['google_customer_id'];
        $campaign_channel_type = $acDetail['campaign_channel_type'];

        $rules = array('adGroupName' => 'required|max:55');
        if($campaign_channel_type != 'MULTI_CHANNEL'){
            $rules['cpcBidMicroAmount'] = 'required';              
        }

        $this->validate($request, $rules);

        try {
            $storagepath = $this->getstoragepath($account_id);
            $addgroupArray = [];
            $adGroupStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $adGroupId = $request->adGroupId;
            $adGroupName = $request->adGroupName;
            $cpcBidMicroAmount = $request->cpcBidMicroAmount * 1000000;
            $adGroupStatus = $adGroupStatusArr[$request->adGroupStatus];

            $addgroupArray['ad_group_name'] = $adGroupName;
            $addgroupArray['bid'] = $request->cpcBidMicroAmount ?? null;
            $addgroupArray['status'] = $adGroupStatus;

            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            // Creates an ad group object with the specified resource name and other changes.
            $addgroupArr = array(
                                'resource_name' => ResourceNames::forAdGroup($customerId, $adGroupId),
                                'name' => $adGroupName,
                                'status' => self::getAdGroupStatus($adGroupStatus),
                            );
            if($campaign_channel_type != 'MULTI_CHANNEL'){
                $addgroupArr['cpc_bid_micros'] = $cpcBidMicroAmount;
            }
            
            $adGroup = new AdGroup($addgroupArr);

            $adGroupOperation = new AdGroupOperation();
            $adGroupOperation->setUpdate($adGroup);
            $adGroupOperation->setUpdateMask(FieldMasks::allSetFieldsOf($adGroup));

            // Issues a mutate request to update the ad group.
            $adGroupServiceClient = $googleAdsClient->getAdGroupServiceClient();
            $response = $adGroupServiceClient->mutateAdGroups(
                $customerId,
                [$adGroupOperation]
            );

            $updatedAdGroup = $response->getResults()[0];
            $addgroupArray['adgroup_response'] = json_encode($updatedAdGroup);

            $adGroupUpdate = \App\GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->where('adgroup_google_campaign_id', $campaignId)->update($addgroupArray);

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Ad Group',
                        'message' => "Updated account details for ". $adGroupName,
                        'response' => json_encode($addgroupArray)
                    );
            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups')->with('actSuccess', 'Adsgroup updated successfully');
        } catch (Exception $e) {

            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Ad Group',
                        'message' => 'Update ad group > '. $e->getMessage()
                    );
            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups/update/'.$request->adGroupId)->with('actError', $this->exceptionError);
        }
    }

    // delete ad group
    public function deleteAdGroup(Request $request, $campaignId, $adGroupId)
    {
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $campaign_name = $acDetail['campaign_name'];
        $customerId = $acDetail['google_customer_id'];

        $storagepath = $this->getstoragepath($account_id);

        $adGroup = \App\GoogleAdsGroup::where('google_adgroup_id', $adGroupId)->where('adgroup_google_campaign_id', $campaignId)->firstOrFail();

        try {
            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            // Creates ad group resource name.
            $adGroupResourceName = ResourceNames::forAdGroup($customerId, $adGroupId);

            // Constructs an operation that will remove the ad group with the specified resource name.
            $adGroupOperation = new AdGroupOperation();
            $adGroupOperation->setRemove($adGroupResourceName);

            // Issues a mutate request to remove the ad group.
            $adGroupServiceClient = $googleAdsClient->getAdGroupServiceClient();
            $response = $adGroupServiceClient->mutateAdGroups(
                $customerId,
                [$adGroupOperation]
            );

            $removedAdGroup = $response->getResults()[0];

            // Insert google ads log 
            $input = array(
                        'type' => 'SUCCESS',
                        'module' => 'Ad Group',
                        'message' => "Deleted ad group for ". $campaign_name,
                        'response' => json_encode($adGroup)
                    );

            // Delete other data
            GoogleAdGroupKeyword::where('google_adgroup_id', $adGroupId)->delete();
            GoogleResponsiveDisplayAd::where('google_adgroup_id', $adGroupId)->delete();
            GoogleResponsiveDisplayAdMarketingImage::where('google_adgroup_id', $adGroupId)->delete();
            GoogleAppAd::where('google_adgroup_id', $adGroupId)->delete();
            GoogleAppAdImage::where('google_adgroup_id', $adGroupId)->delete();
            GoogleAd::where('google_adgroup_id', $adGroupId)->delete();

            $adGroup->delete();

            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups')->with('actSuccess', 'Adsgroup deleted successfully');
        } catch (Exception $e) {

            // Insert google ads log 
            $input = array(
                        'type' => 'ERROR',
                        'module' => 'Ad Group',
                        'message' => 'Delete ad group > ' . $e->getMessage(),
                    );
            insertGoogleAdsLog($input);

            return redirect('google-campaigns/'.$campaignId.'/adgroups')->with('actError', $this->exceptionError);
        }
    }

    public function generateKeywords(Request $request) {
        $campaignId = $request->campaignId;
        $acDetail = $this->getAccountDetail($campaignId);
        $account_id = $acDetail['account_id'];
        $storagepath = $this->getstoragepath($account_id);

        $keywords = [];
        if(!empty($request->scan_keywords)){
            $scan_keywords = explode(',',$request->scan_keywords);
            foreach ($scan_keywords as $key => $v) {
                $keywords[] = $v;
            }
        }

        $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

        $googleAdsClient = (new GoogleAdsClientBuilder())
                            ->from($oAuth2Configuration)
                            ->withOAuth2Credential($oAuth2Credential)
                            ->build();
        
        $customerId = $googleAdsClient->getLoginCustomerId();
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

        $pageUrl = $request->scanurl;
        $keywordPlanIdeaServiceClient = $googleAdsClient->getKeywordPlanIdeaServiceClient();
        
        $requestOptionalArgs = [];
        $keywordsArr = [];
        $locationIds = [];
        $languageId = 1000;

        $geoTargetConstants =  array_map(function ($locationId) {
                                    return ResourceNames::forGeoTargetConstant($locationId);
                                }, $locationIds);

        if (!empty($keywords) && !empty($pageUrl)) {
            $requestOptionalArgs['keywordAndUrlSeed'] =
                new KeywordAndUrlSeed(['url' => $pageUrl, 'keywords' => $keywords]);

            $response = $keywordPlanIdeaServiceClient->generateKeywordIdeas([
                                    'language' => ResourceNames::forLanguageConstant($languageId),
                                    'page_size' => 1,
                                    'customerId' => $customerId,
                                    'geoTargetConstants' => $geoTargetConstants,
                                    'keywordPlanNetwork' => KeywordPlanNetwork::GOOGLE_SEARCH_AND_PARTNERS
                                ] + $requestOptionalArgs 
                            );
            
            foreach ($response->iterateAllElements() as $result) {
                array_push($keywordsArr, $result->getText());
            }

            $keywordsArr = array_slice($keywordsArr, 0, 40);
        } 

        unset($requestOptionalArgs['keywordAndUrlSeed']);
        if (!empty($pageUrl)) {
            $requestOptionalArgs['urlSeed'] = new UrlSeed(['url' => $pageUrl]);
        } else if(!empty($keywords)) {
            $requestOptionalArgs['keywordSeed'] = new KeywordSeed(['keywords' => $keywords]);
        }
        
        $response = $keywordPlanIdeaServiceClient->generateKeywordIdeas([
                                'language' => ResourceNames::forLanguageConstant($languageId),
                                'page_size' => 1,
                                'customerId' => $customerId,
                                'geoTargetConstants' => $geoTargetConstants,
                                'keywordPlanNetwork' => KeywordPlanNetwork::GOOGLE_SEARCH_AND_PARTNERS
                            ] + $requestOptionalArgs 
                        );
        
        foreach ($response->iterateAllElements() as $result) {
            array_push($keywordsArr, $result->getText());
        }

        $keywordsArr = array_slice($keywordsArr, 0, 80);

        return response()->json(['count' => count($keywordsArr), 'result' => $keywordsArr]);
    }
    
    //get ad group status  
    private function getAdGroupStatus($v)
    {
        switch ($v) {
            case 'UNKNOWN':
                return AdGroupStatus::UNKNOWN;
                break;

            case 'ENABLED':
                return AdGroupStatus::ENABLED;
                break;

            case 'PAUSED':
                return AdGroupStatus::PAUSED;
                break;

            case 'REMOVED':
                return AdGroupStatus::REMOVED;
                break;

            default:
                return AdGroupStatus::UNKNOWN;
        }
    }
}
