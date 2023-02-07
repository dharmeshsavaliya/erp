<?php

namespace App\Http\Controllers;

use Exception;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V12\Common\ManualCpc;
use Google\Ads\GoogleAds\V12\Common\FrequencyCapEntry;
use Google\Ads\GoogleAds\V3\Common\FrequencyCapKey;
use Google\Ads\GoogleAds\V12\Common\TargetCpa;
use Google\Ads\GoogleAds\V12\Common\TargetSpend;
use Google\Ads\GoogleAds\V3\Enums\FrequencyCapLevelEnum\FrequencyCapLevel;
use Google\Ads\GoogleAds\V12\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V3\Enums\FrequencyCapTimeUnitEnum\FrequencyCapTimeUnit;
use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V3\Enums\FrequencyCapEventTypeEnum\FrequencyCapEventType;
use Google\Ads\GoogleAds\V12\Enums\PositiveGeoTargetTypeEnum\PositiveGeoTargetType;
use Google\Ads\GoogleAds\V12\Enums\NegativeGeoTargetTypeEnum\NegativeGeoTargetType;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V12\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V12\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Resources\Campaign;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V12\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V12\Resources\Campaign\GeoTargetTypeSetting;
use Google\Ads\GoogleAds\V12\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V12\Resources\ShoppingSetting;
use Google\Ads\GoogleAds\V12\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;
use Google\Ads\GoogleAds\Lib\ConfigurationLoader;
use Illuminate\Http\Request;

class GoogleCampaignsController extends Controller
{
    // show campaigns in main page
    public $exceptionError = 'Something went wrong';

    public function getstoragepath($account_id)
    {
        $result = \App\GoogleAdsAccount::find($account_id);
        if (isset($result->config_file_path) && $result->config_file_path != '' && \Storage::disk('adsapi')->exists($account_id.'/'.$result->config_file_path)) {
            $storagepath = \Storage::disk('adsapi')->url($account_id.'/'.$result->config_file_path);
            $storagepath = storage_path('app/adsapi/'.$account_id.'/'.$result->config_file_path);
            /* echo $storagepath; exit;
        echo storage_path('adsapi_php.ini'); exit; */
            /* echo '<pre>' . print_r($result, true) . '</pre>';
            die('developer working'); */
            return $storagepath;
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
    }

    public function index(Request $request)
    {
        if ($request->get('account_id')) {
            $account_id = $request->get('account_id');
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please add adspai_php.ini file');
        }
        $storagepath = $this->getstoragepath($account_id);
        //echo $storagepath; exit;
        //echo $storagepath; exit;
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($storagepath)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile($storagepath)
            ->withOAuth2Credential($oAuth2Credential)
            ->build(); */

        $query = \App\GoogleAdsCampaign::query();
        if ($request->googlecampaign_id) {
            $query = $query->where('google_campaign_id', $request->googlecampaign_id);
        }
        if ($request->googlecampaign_name) {
            $query = $query->where('campaign_name', 'LIKE', '%'.$request->googlecampaign_name.'%');
        }

        if ($request->googlecampaign_budget) {
            $query = $query->where('budget_amount', 'LIKE', '%'.$request->googlecampaign_budget.'%');
        }
        if ($request->start_date) {
            $query = $query->where('start_date', 'LIKE', '%'.$request->start_date.'%');
        }
        if ($request->end_date) {
            $query = $query->where('end_date', 'LIKE', '%'.$request->end_date.'%');
        }
        if ($request->budget_uniq_id) {
            $query = $query->where('budget_uniq_id', $request->budget_uniq_id);
        }

        if ($request->campaign_status) {
            $query = $query->where('status', $request->campaign_status);
        }

        $query->where('account_id', $account_id);
        $campInfo = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googlecampaigns.partials.list-adscampaign', ['campaigns' => $campInfo])->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $campInfo->render(),
                'count' => $campInfo->total(),
            ], 200);
        }

        $totalEntries = $campInfo->count();

        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        return view('googlecampaigns.index', ['campaigns' => $campInfo, 'totalNumEntries' => $totalEntries, 'biddingStrategyTypes' => $biddingStrategyTypes]);
        /*$adWordsServices = new AdWordsServices();
         $campInfo = $this->getCampaigns($adWordsServices, $session);
        return view('googlecampaigns.index', ['campaigns' => $campInfo['campaigns'], 'totalNumEntries' => $campInfo['totalNumEntries']]); */
    }

    // get campaigns and total count
    public function getCampaigns(AdWordsServices $adWordsServices, AdWordsSession $session)
    {
        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector.
        $campaignSelector = new Selector();
        $campaignSelector->setFields(['Id', 'Name', 'Status', 'BudgetId', 'BudgetName', 'Amount']);
        $campaignSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $campaignSelector->setPaging(new Paging(0, 10));

        $adGroupService = $adWordsServices->get($session, AdGroupService::class);

        // Create a selector to select all ad groups for the specified campaign.
        $groupSelector = new Selector();
        $groupSelector->setFields(['Id', 'Name']);
        $groupSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        $groupSelector->setPaging(new Paging(0, 10));

        //        $budgetService = $adWordsServices->get($session, BudgetService::class);
        $totalNumEntries = 0;
        $campaigns = [];
        do {
            // Make the get request.
            $page = $campaignService->get($campaignSelector);
            // Display results.
            if ($page->getEntries() !== null) {
                $totalNumEntries = $page->getTotalNumEntries();
                foreach ($page->getEntries() as $campaign) {
                    // getting campaign's adgroups
                    $groupSelector->setPredicates(
                        [new Predicate('CampaignId', PredicateOperator::IN, [$campaign->getId()])]
                    );
                    $adGroupPage = $adGroupService->get($groupSelector);
                    $adGroups = [];
                    if ($adGroupPage->getEntries() !== null) {
                        //                        $totalNumEntries = $page->getTotalNumEntries();
                        foreach ($adGroupPage->getEntries() as $adGroup) {
                            $adGroups[] = [
                                'adGroupId' => $adGroup->getId(),
                                'adGroupName' => $adGroup->getName(),
                            ];
                        }
                    }
                    // getting budget
                    $campaignBudget = $campaign->getBudget();
                    // adding new campaign
                    $campaigns[] = [
                        'campaignId' => $campaign->getId(),
                        'campaignGroups' => $adGroups,
                        'name' => $campaign->getName(),
                        'status' => $campaign->getStatus(),
                        'budgetId' => $campaignBudget->getBudgetId(),
                        'budgetName' => $campaignBudget->getName(),
                        'budgetAmount' => $campaignBudget->getAmount()->getMicroAmount() / 1000000,
                    ];
                }
            }

            // Advance the paging index.
            $campaignSelector->getPaging()->setStartIndex(
                $campaignSelector->getPaging()->getStartIndex() + 10
            );
        } while ($campaignSelector->getPaging()->getStartIndex() < $totalNumEntries);

        return [
            'totalNumEntries' => $totalNumEntries,
            'campaigns' => $campaigns,
        ];
    }

    // go to create page
    public function createPage()
    {
        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();

        return view('googlecampaigns.create', compact('biddingStrategyTypes'));
    }

    // create campaign
    public function createCampaign(Request $request)
    {
        /*  $this->validate($request, [
            'campaignName' => 'required',
            'budgetAmount' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'campaignStatus' => 'required',
        ]); */
        $this->validate($request, [
            'campaignName' => 'required|max:55',
            'budgetAmount' => 'required|max:55',
            'start_date' => 'required|max:15',
            'end_date' => 'required|max:15',
        ]);
        try {
            $campaignArray = [];
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $budgetAmount = $request->budgetAmount * 1000000;
            $campaignName = $request->campaignName;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];

            //start creating array to store data into database
            $account_id = $request->account_id;
            $campaignArray['account_id'] = $account_id;
            $storagepath = $this->getstoragepath($account_id);
            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;
            if ($request->channel_type) {
                $channel_type = $request->channel_type;
            } else {
                $channel_type = 'SEARCH';
            }
            $campaignArray['channel_type'] = $channel_type;

            if ($request->channel_sub_type) {
                $channel_sub_type = $request->channel_sub_type;
            } else {
                $channel_sub_type = 'UNKNOWN';
            }
            $campaignArray['channel_sub_type'] = $channel_sub_type;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;

            if ($request->ad_rotation) {
                $ad_rotation = $request->ad_rotation;
            } else {
                $ad_rotation = '';
            }
            $campaignArray['ad_rotation'] = $ad_rotation;

            if ($request->tracking_template_url) {
                $tracking_template_url = $request->tracking_template_url;
            } else {
                $tracking_template_url = '';
            }
            $campaignArray['tracking_template_url'] = $tracking_template_url;

            if ($request->final_url_suffix) {
                $final_url_suffix = $request->final_url_suffix;
            } else {
                $final_url_suffix = '';
            }
            $campaignArray['final_url_suffix'] = $final_url_suffix;

            if ($request->merchant_id) {
                $merchant_id = $request->merchant_id;
            } else {
                $merchant_id = '';
            }
            $campaignArray['merchant_id'] = $merchant_id;

            if ($request->sales_country) {
                $sales_country = $request->sales_country;
            } else {
                $sales_country = '';
            }
            $campaignArray['sales_country'] = $sales_country;

            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            $customerId = $googleAdsClient->getLoginCustomerId();

            $budget = self::addCampaignBudget($googleAdsClient, $customerId, $budgetAmount);
            $campaignArray['budget_uniq_id'] = $budget['budget_uniq_id'] ?? null;
            $campaignArray['budget_id'] = $budget['budget_id'] ?? null;
            $budgetResourceName = $budget['budget_resource_name'] ?? null;

            // Creates a campaign.
            $campaignArr = array(
                                'name' => $campaignName,
                                'campaign_budget' => $budgetResourceName,
                                'status' => CampaignStatus::PAUSED,
                                'advertising_channel_type' => self::getAdvertisingChannelType($channel_type),
                                'advertising_channel_sub_type' => self::getAdvertisingChannelSubType($channel_sub_type),
                                'network_settings' => self::getNetworkSettings($channel_type, $channel_sub_type),
                                'shopping_setting' => ($channel_type === 'SHOPPING') ? self::getShoppingSetting($merchant_id, $sales_country) : null,
                                'frequency_caps' => self::getFrequencyCaps(),
                                'geo_target_type_setting' => self::getGeoTargetTypeSetting(),
                                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                                'start_date' => $campaign_start_date,
                                'end_date' => $campaign_end_date,
                                'final_url_suffix' => $final_url_suffix,
                            );

            if (in_array($bidding_strategy_type, ['TARGET_CPA', 'MAXIMIZE_CONVERSION_VALUE']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            }

            $campaign = new Campaign($campaignArr);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);

            // Submits the campaign operation and prints the results.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);

            $createdCampaign = $response->getResults()[0];

            $campaignArray['google_campaign_id'] = $createdCampaign->getId();
            $campaignArray['campaign_response'] = json_encode($createdCampaign);
            
            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign created successfully');
        } catch (Exception $e) {
            // echo'<pre>'.print_r($e,true).'</pre>'; exit;
            return redirect()->to('google-campaigns/create?account_id='.$request->account_id)->with('actError', $e->getMessage());
        }
    }

    // go to update page
    public function updatePage(Request $request, $campaignId)
    {
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campaignService = $adWordsServices->get($session, CampaignService::class);

        // Create selector.
        $campaignSelector = new Selector();
        $campaignSelector->setFields(['Id', 'Name', 'Status']);
        //        $campaignSelector->setOrdering([new OrderBy('Name', SortOrder::ASCENDING)]);
        //        $campaignSelector->setPaging(new Paging(0, 10));
        $campaignSelector->setPredicates(
            [new Predicate('Id', PredicateOperator::IN, [$campaignId])]
        );

        $page = $campaignService->get($campaignSelector);
        $pageEntries = $page->getEntries();

        if ($pageEntries !== null) {
            $campaign = $pageEntries[0];
        }
        $campaign = [
            "campaignId" => $campaign->getId(),
            //            "campaignGroups" => $adGroups,
            "name" => $campaign->getName(),
            "status" => $campaign->getStatus(),
            //                        "budgetId" => $campaignBudget->getBudgetId(),
            //                        "budgetName" => $campaignBudget->getName(),
            //                        "budgetAmount" => $campaignBudget->getAmount()
        ];
        // */
        $biddingStrategyTypes = $this->getBiddingStrategyTypeArray();
        $campaign = \App\GoogleAdsCampaign::where('google_campaign_id', $campaignId)->first();

        return view('googlecampaigns.update', ['campaign' => $campaign, 'biddingStrategyTypes' => $biddingStrategyTypes]);
    }

    // save campaign's changes
    public function updateCampaign(Request $request)
    {
        $this->validate($request, [
            'campaignName' => 'required|max:55',
            'budgetAmount' => 'required|max:55',
            'start_date' => 'required|max:15',
            'end_date' => 'required|max:15',
        ]);

        $campaignDetail = \App\GoogleAdsCampaign::where('google_campaign_id',
            $request->campaignId)->first();
        $account_id = $campaignDetail->account_id;
        try {
            $storagepath = $this->getstoragepath($account_id);
            $campaignStatusArr = ['UNKNOWN', 'ENABLED', 'PAUSED', 'REMOVED'];
            $campaignId = $request->campaignId;
            $campaignName = $request->campaignName;
            $campaignStatus = $campaignStatusArr[$request->campaignStatus];

            $campaignArray = [];
            $budgetAmount = $request->budgetAmount * 1000000;
            $campaign_start_date = $request->start_date;
            $campaign_end_date = $request->end_date;

            //start creating array to store data into database

            $campaignArray['campaign_name'] = $campaignName;
            $campaignArray['budget_amount'] = $request->budgetAmount;
            $campaignArray['start_date'] = $campaign_start_date;
            $campaignArray['end_date'] = $campaign_end_date;
            $campaignArray['status'] = $campaignStatus;

            if ($request->biddingStrategyType) {
                $bidding_strategy_type = $request->biddingStrategyType;
            } else {
                $bidding_strategy_type = 'UNKNOWN';
            }
            $campaignArray['bidding_strategy_type'] = $bidding_strategy_type;

            if ($request->txt_target_cpa) {
                $txt_target_cpa = $request->txt_target_cpa;
            } else {
                $txt_target_cpa = 0.0;
            }
            $campaignArray['target_cpa_value'] = $txt_target_cpa;

            if ($request->txt_target_roas) {
                $txt_target_roas = $request->txt_target_roas;
            } else {
                $txt_target_roas = 0.0;
            }
            $campaignArray['target_roas_value'] = $txt_target_roas;

            if ($request->txt_maximize_clicks) {
                $txt_maximize_clicks = $request->txt_maximize_clicks;
            } else {
                $txt_maximize_clicks = '';
            }
            $campaignArray['maximize_clicks'] = $txt_maximize_clicks;


            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            $customerId = $googleAdsClient->getLoginCustomerId();

            $budget = self::updateCampaignBudget($googleAdsClient, $customerId, $budgetAmount, $request->budget_id);
            $budgetResourceName = $budget['budget_resource_name'] ?? null;

            // Creates a campaign.
            $campaignArr = array(
                                'resource_name' => ResourceNames::forCampaign($customerId, $campaignId),
                                
                                'name' => $campaignName,
                                'campaign_budget' => $budgetResourceName,
                                'status' => CampaignStatus::PAUSED,
                                'bidding_strategy_type' => self::getBiddingStrategyType($bidding_strategy_type),
                                'start_date' => $campaign_start_date,
                                'end_date' => $campaign_end_date,
                            );

            if (in_array($bidding_strategy_type, ['TARGET_CPA', 'MAXIMIZE_CONVERSION_VALUE']) && $txt_target_cpa) {
                $campaignArr['target_cpa'] = new TargetCpa(['target_cpa_micros' => $txt_target_cpa * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_SPEND']) && $txt_maximize_clicks) {
                $campaignArr['target_spend'] = new TargetSpend(['target_spend_micros' => $txt_maximize_clicks * 1000000]);
            }

            if (in_array($bidding_strategy_type, ['TARGET_ROAS']) && $txt_target_roas) {
                $campaignArr['target_roas'] = new TargetRoas(['target_roas' => $txt_target_roas]);
            }

            if (in_array($bidding_strategy_type, [ 'MANUAL_CPC'])) {
                $campaignArr['target_cpa'] = null;
                $campaignArr['target_spend'] = null;
                $campaignArr['target_roas'] = null;
            }

            // Creates a campaign object with the specified resource name and other changes.
            $campaign = new Campaign($campaignArr);

            // Constructs an operation that will update the campaign with the specified resource name,
            // using the FieldMasks utility to derive the update mask. This mask tells the Google Ads
            // API which attributes of the campaign you want to change.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

            // Issues a mutate request to update the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns(
                $customerId,
                [$campaignOperation]
            );

            $updatedCampaign = $response->getResults()[0];

            $campaignArray['google_campaign_id'] = $updatedCampaign->getId();
            $campaignArray['campaign_response'] = json_encode($updatedCampaign);
            \App\GoogleAdsCampaign::whereId($campaignDetail->id)->update($campaignArray);

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign updated successfully');
        } catch (Exception $e) {
            return redirect()->to('google-campaigns/update/'.$request->campaignId.'?account_id='.$account_id)->with('actError', $e->getMessage());
        }
    }

    // delete campaign
    public function deleteCampaign(Request $request, $campaignId)
    {
        try {
            $account_id = $request->delete_account_id;
            $storagepath = $this->getstoragepath($account_id);
            
            // Get OAuth2 configuration from file.
            $oAuth2Configuration = (new ConfigurationLoader())->fromFile($storagepath);

            // Generate a refreshable OAuth2 credential for authentication.
            $oAuth2Credential = (new OAuth2TokenBuilder())->from($oAuth2Configuration)->build();

            $googleAdsClient = (new GoogleAdsClientBuilder())
                                ->from($oAuth2Configuration)
                                ->withOAuth2Credential($oAuth2Credential)
                                ->build();

            $customerId = $googleAdsClient->getLoginCustomerId();

            // Creates the resource name of a campaign to remove.
            $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setRemove($campaignResourceName);

            // Issues a mutate request to remove the campaign.
            $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);

            \App\GoogleAdsCampaign::where('account_id', $account_id)->where('google_campaign_id', $campaignId)->delete();

            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actSuccess', 'Campaign deleted successfully');
        } catch (Exception $e) {
            return redirect()->to('google-campaigns?account_id='.$account_id)->with('actError', $this->exceptionError);
        }
    }

    // create a campaign single shared budget
    public function addCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount)
    {
        $response = [];
        try {
            $uniqId = uniqid();

            // Creates a campaign budget.
            $budget = new CampaignBudget([
                'name' => 'Interplanetary Cruise Budget #' . $uniqId,
                'delivery_method' => BudgetDeliveryMethod::STANDARD,
                'amount_micros' => $amount * 1000000
            ]);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setCreate($budget);

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $createdBudget = $response->getResults()[0];

            $response = array(
                            'budget_uniq_id' => $uniqId,
                            'budget_id' => $createdBudget->getBudgetId(),
                            'budget_resource_name' => $createdBudget->getResourceName(),
                        );
        } catch (Exception $e) {
            dd($e);            
        }

        return $response;
    }

    // update a campaign single shared budget
    public function updateCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId, $amount, $campaignBudgetId)
    {
        $response = [];
        try {
            // Creates a campaign budget.
            $budget = new CampaignBudget([
                            'resource_name' => ResourceNames::forCampaignBudget($customerId, $campaignBudgetId),

                            'amount_micros' => $amount * 1000000
                        ]);

            // Creates a campaign budget operation.
            $campaignBudgetOperation = new CampaignBudgetOperation();
            $campaignBudgetOperation->setUpdate($budget);
            $campaignBudgetOperation->setUpdateMask(FieldMasks::allSetFieldsOf($budget));

            // Issues a mutate request.
            $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
            $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
                $customerId,
                [$campaignBudgetOperation]
            );
            $updatedBudget = $response->getResults()[0];

            $response = array(
                            'budget_id' => $updatedBudget->getBudgetId(),
                            'budget_resource_name' => $updatedBudget->getResourceName(),
                        );
        } catch (Exception $e) {
            dd($e);            
        }

        return $response;
    }

    //function to retrieve data from library
    //get advertising channel type
    public function getAdvertisingChannelType($v)
    {
        switch ($v) {
            case 'SEARCH':
                return AdvertisingChannelType::SEARCH;
                break;

            case 'DISPLAY':
                return AdvertisingChannelType::DISPLAY;
                break;

            case 'SHOPPING':
                return AdvertisingChannelType::SHOPPING;
                break;

            case 'MULTI_CHANNEL':
                return AdvertisingChannelType::MULTI_CHANNEL;
                break;

            case 'UNKNOWN':
                return AdvertisingChannelType::UNKNOWN;
                break;

            default:
                return AdvertisingChannelType::SEARCH;
        }
    }

    //get advertising sub type
    private function getAdvertisingChannelSubType($v)
    {
        switch ($v) {
            case 'UNKNOWN':
                return AdvertisingChannelSubType::UNKNOWN;
                break;

            case 'SEARCH_MOBILE_APP':
                return AdvertisingChannelSubType::SEARCH_MOBILE_APP;
                break;

            case 'DISPLAY_MOBILE_APP':
                return AdvertisingChannelSubType::DISPLAY_MOBILE_APP;
                break;

            case 'SEARCH_EXPRESS':
                return AdvertisingChannelSubType::SEARCH_EXPRESS;
                break;

            case 'DISPLAY_EXPRESS':
                return AdvertisingChannelSubType::DISPLAY_EXPRESS;
                break;
            case 'DISPLAY_SMART_CAMPAIGN':
                return AdvertisingChannelSubType::DISPLAY_SMART_CAMPAIGN;
                break;
            case 'SHOPPING_GOAL_OPTIMIZED_ADS':
                return AdvertisingChannelSubType::SHOPPING_GOAL_OPTIMIZED_ADS;
                break;
            case 'DISPLAY_GMAIL_AD':
                return AdvertisingChannelSubType::DISPLAY_GMAIL_AD;
                break;

            default:
                return AdvertisingChannelSubType::UNKNOWN;
        }
    }

    public function getBiddingStrategyTypeArray()
    {
        return ['MANUAL_CPC' => 'Manually set bids', 'MANUAL_CPM' => 'Viewable CPM', 'PAGE_ONE_PROMOTED' => 'Page one promoted', 'TARGET_SPEND' => 'Maximize clicks', 'TARGET_CPA' => 'Target CPA', 'TARGET_ROAS' => 'Target Roas', 'MAXIMIZE_CONVERSIONS' => 'max conv', 'MAXIMIZE_CONVERSION_VALUE' => 'Automatically maximize conversions', 'TARGET_OUTRANK_SHARE' => 'Target outrank sharing', 'NONE' => 'None', 'UNKNOWN' => 'Unknown'];
    }

    // get network settings
    private function getNetworkSettings($channel_type, $channel_sub_type)
    {

        $networkSettingsArr = array(
                                'target_google_search' => false,
                                'target_search_network' => false,
                                'target_content_network' => false,
                                'target_partner_search_network' => false
                            );

        if ($channel_type == 'SEARCH' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS')) {

            $networkSettingsArr['target_google_search'] = true;

        } elseif ($channel_type == 'MULTI_CHANNEL' || $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS') {

            $networkSettingsArr['target_search_network'] = true;

        }

        if ($channel_type == 'MULTI_CHANNEL' || $channel_type == 'MULTI_CHANNEL' || ($channel_type == 'DISPLAY' && $channel_sub_type == 'DISPLAY_SMART_CAMPAIGN') || $channel_sub_type == 'SHOPPING_GOAL_OPTIMIZED_ADS') {

            $networkSettingsArr['target_content_network'] = true;

        }

        if ($channel_type == 'MULTI_CHANNEL') {

            $networkSettingsArr['target_partner_search_network'] = true;

        }

        $networkSettings = new NetworkSettings($networkSettingsArr);

        return $networkSettings;
    }

    // get shopping setting
    private function getShoppingSetting($merchant_id, $sales_country)
    {
        $shoppingSetting = new ShoppingSetting([
            'sales_country' => $sales_country,
            'campaign_priority' => 0,
            'merchant_id' => $merchant_id,
            'enable_local' => true
        ]);

        return $shoppingSetting;
    }

    //get frequency caps
    private function getFrequencyCaps()
    {
        $frequencyCaps = new FrequencyCapEntry([
            'key' => new FrequencyCapKey([
                'level'=> FrequencyCapLevel::ADGROUP,
                'event_type'=> FrequencyCapEventType::IMPRESSION,
                'time_unit'=> FrequencyCapTimeUnit::DAY,
            ]),
            'cap' => new Int32Value(['value'=>5])
        ]);
    }

    // get shopping setting
    private function getGeoTargetTypeSetting()
    {

        $shoppingSetting = new GeoTargetTypeSetting([
            'positive_geo_target_type' => PositiveGeoTargetType::DONT_CARE,
            'negative_geo_target_type' => NegativeGeoTargetType::DONT_CARE,
        ]);

        return $shoppingSetting;
    }

    //get bidding strategy type
    private function getBiddingStrategyType($v)
    {
        switch ($v) {
            case 'MANUAL_CPC':
                return BiddingStrategyType::MANUAL_CPC;
                break;

            case 'MANUAL_CPM':
                return BiddingStrategyType::MANUAL_CPM;
                break;

            case 'PAGE_ONE_PROMOTED':
                return BiddingStrategyType::PAGE_ONE_PROMOTED;
                break;

            case 'TARGET_SPEND':
                return BiddingStrategyType::TARGET_SPEND;
                break;

            case 'TARGET_CPA':
                return BiddingStrategyType::TARGET_CPA;
                break;

            case 'TARGET_ROAS':
                return BiddingStrategyType::TARGET_ROAS;
                break;

            case 'MAXIMIZE_CONVERSIONS':
                return BiddingStrategyType::MAXIMIZE_CONVERSIONS;
                break;

            case 'MAXIMIZE_CONVERSION_VALUE':
                return BiddingStrategyType::MAXIMIZE_CONVERSION_VALUE;
                break;

            case 'TARGET_OUTRANK_SHARE':
                return BiddingStrategyType::TARGET_OUTRANK_SHARE;
                break;

            case 'NONE':
                return BiddingStrategyType::NONE;
                break;

            default:
                return BiddingStrategyType::UNKNOWN;
        }
    }
}
