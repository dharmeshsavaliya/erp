<?php

///App/Console/Commands/QuizStart.php

namespace App\Console\Commands;

use App\Site;
use App\GoogleWebMasters;
use App\GoogleClientAccount;
use Illuminate\Http\Request;
use App\GoogleSearchAnalytics;
use Illuminate\Console\Command;
use App\GoogleClientAccountMail;

class GoogleWebMasterFetchAllRecords extends Command
{
    protected $signature = 'fetch-all-records:start';

    protected $description = 'it will fetch data from that date and insert it';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $google_redirect_url = route('googlewebmaster.get-access-token');

        $id = \Cache::get('google_client_account_id');

        $GoogleClientAccounts = GoogleClientAccount::get();

        foreach ($GoogleClientAccounts as $GoogleClientAccount) {
            $refreshToken = GoogleClientAccountMail::where('google_client_account_id', $GoogleClientAccount->id)->first();
            if (isset($refreshToken['GOOGLE_CLIENT_REFRESH_TOKEN']) and $refreshToken['GOOGLE_CLIENT_REFRESH_TOKEN'] != null) {
                // $GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN = '1//0cUsEThSeeU-1CgYIARAAGAwSNwF-L9Irzg0ANYiSFNvpHvNr0d3BaXU9mGOH2alV3w0AH6LFuOtpN8uidPbnhSKJaP9KtAra6bU';
                $GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN = $refreshToken->GOOGLE_CLIENT_REFRESH_TOKEN;

                if ($GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN == null) {
                    continue;
                }

                $this->client = new \Google_Client();
                $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
                $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
                $this->client->refreshToken($GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN);

                $token = $this->client->getAccessToken();
                //$request->session()->put('token',$token);
                //echo"<pre>";print_r($token);die;
                if (empty($token)) {
                    continue;
                }

                $google_oauthV2 = new \Google_Service_Oauth2($this->client);

                if ($this->client->getAccessToken()) {
                    //  $details=$this->updateSitesData($request);
                    $details = $this->updateSitesData($token);
                    //echo"<pre>";print_r($token);die;
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://www.googleapis.com/webmasters/v3/sites/',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'authorization:Bearer ' . $this->client->getAccessToken()['access_token'],
                        ],
                    ]);

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    if (curl_errno($curl)) {
                        $error_msg = curl_error($curl);
                    }

                    //echo '<pre>';print_r($response);die;
                    if (isset($error_msg)) {
                        $this->curl_errors_array[] = ['key' => 'sites', 'error' => $error_msg, 'type' => 'sites'];
                        activity('v3_sites')->log($error_msg);
                    }

                    $check_error_response = json_decode($response);

                    curl_close($curl);

                    if (isset($check_error_response->error->message) || $err) {
                        $this->curl_errors_array[] = ['key' => 'sites', 'error' => $check_error_response->error->message, 'type' => 'sites'];
                        activity('v3_sites')->log($check_error_response->error->message);
                        echo $this->curl_errors_array[0]['error'];
                    } else {
                        if (is_array(json_decode($response)->siteEntry)) {
                            foreach (json_decode($response)->siteEntry as $key => $site) {
                                // Create ot update site url
                                GoogleWebMasters::updateOrCreate(['sites' => $site->siteUrl]);

                                echo 'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site->siteUrl) . '/sitemaps';
                                $curl1 = curl_init();
                                //replace website name with code coming form site list

                                curl_setopt_array($curl1, [
                                    CURLOPT_URL => 'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site->siteUrl) . '/sitemaps',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 30,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                    CURLOPT_HTTPHEADER => [
                                        'authorization: Bearer ' . $this->client->getAccessToken()['access_token'],
                                    ],
                                ]);

                                $response1 = curl_exec($curl1);
                                $err = curl_error($curl1);

                                if ($err) {
                                    activity('v3_sites')->log($err);
                                    echo 'cURL Error #:' . $err;
                                } else {
                                    if (isset(json_decode($response1)->sitemap) && is_array(json_decode($response1)->sitemap)) {
                                        foreach (json_decode($response1)->sitemap as $key => $sitemap) {
                                            GoogleWebMasters::where('sites', $site->siteUrl)->update(['crawls' => $sitemap->errors]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateSitesData($token)
    {
        if (! (isset($token['access_token']))) {
            redirect()->route('googlewebmaster.get-access-token');
        }

        $GOOGLE_CLIENT_MULTIPLE_KEYS = config('google.GOOGLE_CLIENT_MULTIPLE_KEYS');

        $google_keys = explode(',', $GOOGLE_CLIENT_MULTIPLE_KEYS);
        //$token = $request->session()->get('token');
        foreach ($google_keys as $google_key) {
            if ($google_key) {
                $this->apiKey = $google_key;
                //$this->apiKey='';

                $this->googleToken = $token['access_token'];

                $url_for_sites = 'https://www.googleapis.com/webmasters/v3/sites?key=' . $this->apiKey . '<br>';

                // die;

                $curl = curl_init();
                //replace website name with code coming form site list
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url_for_sites,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'authorization: Bearer ' . $this->googleToken,

                    ],
                ]);

                $response = curl_exec($curl);
                $response = json_decode($response);

                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                }

                curl_close($curl);

                if (isset($error_msg)) {
                    $this->curl_errors_array[] = ['key' => $google_key, 'error' => $error_msg, 'type' => 'site_list'];
                    activity('v3_sites')->log($error_msg);
                }

                if (isset($response->error->message)) {
                    $this->curl_errors_array[] = ['key' => $google_key, 'error' => $response->error->message, 'type' => 'site_list'];
                    activity('v3_sites')->log($response->error->message);
                }

                if (isset($response->siteEntry) && count($response->siteEntry)) {
                    $this->updateSites($response->siteEntry);
                }
            }
        }

        //  return array('status'=>1,'sitesUpdated'=>$this->sitesUpdated,'sitesCreated'=>$this->sitesCreated,'searchAnalyticsCreated'=>$this->searchAnalyticsCreated,'success'=>$this->sitesUpdated. ' of sites are updated.','error'=>count($this->curl_errors_array).' error found in this request.','error_message'=>$this->curl_errors_array[0]['error']??'');
    }

    public function updateSites($sites)
    {
        foreach ($sites as $key => $site) {
            if ($siteRow = Site::whereSiteUrl($site->siteUrl)->first()) {
                $siteRow->update(['permission_level' => $site->permissionLevel]);
                $this->sitesUpdated++;
            } else {
                $siteRow = Site::create(['site_url' => $site->siteUrl, 'permission_level' => $site->permissionLevel]);
                $this->sitesCreated++;
            }
            $this->SearchAnalytics($site->siteUrl, $siteRow->id);
            $this->SearchAnalyticsBysearchApperiance($site->siteUrl, $siteRow->id);
        }
    }

    public function SearchAnalyticsBysearchApperiance($siteUrl, $siteID)
    {
        $params['startDate'] = '2000-01-01';
        $params['endDate'] = date('Y-m-d');
        $params['dimensions'] = ['searchAppearance'];

        $response = $this->googleResultForAnaylist($siteUrl, $params);

        if (isset($response->rows) && count($response->rows)) {
            $this->updateSearchAnalyticsForSearchAppearence($response->rows, $siteID);
        }
    }

    public function updateSearchAnalyticsForSearchAppearence($rows, $siteID)
    {
        foreach ($rows as $row) {
            $record = ['clicks' => $row->clicks, 'impressions' => $row->impressions, 'position' => $row->position, 'ctr' => $row->ctr, 'site_id' => $siteID];

            $record['search_apperiance'] = $row->keys[0];
            $rowData = new GoogleSearchAnalytics;

            foreach ($record as $col => $val) {
                $rowData = $rowData->where($col, $val);
            }

            if (! $rowData->first()) {
                GoogleSearchAnalytics::create($record);
                $this->searchAnalyticsCreated++;
            }
        }
    }

    public function SearchAnalytics($siteUrl, $siteID)
    {
        $params['startDate'] = '2000-01-01';
        $params['endDate'] = date('Y-m-d');
        $params['dimensions'] = ['country', 'device', 'page', 'query', 'date'];

        $response = $this->googleResultForAnaylist($siteUrl, $params);

        if (isset($response->rows) && count($response->rows)) {
            $this->updateSearchAnalytics($response->rows, $siteID);
        }
    }

    public function updateSearchAnalytics($rows, $siteID)
    {
        foreach ($rows as $row) {
            $record = ['clicks' => $row->clicks, 'impressions' => $row->impressions, 'position' => $row->position, 'ctr' => $row->ctr, 'site_id' => $siteID];

            $record['country'] = $row->keys[0];
            $record['device'] = $row->keys[1];
            $record['page'] = $row->keys[2];
            $record['query'] = $row->keys[3];
            $record['date'] = $row->keys[4];

            $rowData = new GoogleSearchAnalytics;

            foreach ($record as $col => $val) {
                $rowData = $rowData->where($col, $val);
            }

            if (! $rowData->first()) {
                $here = GoogleSearchAnalytics::create($record);
                $this->searchAnalyticsCreated++;
            }
        }
    }

    public function googleResultForAnaylist($siteUrl, $params)
    {
        $url = 'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($siteUrl) . '/searchAnalytics/query';

        $curl = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //  CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->googleToken,
                'Content-Type:application/json',
            ],
        ]);

        $response = curl_exec($curl);

        $response = json_decode($response);

        if (isset($response->error->message)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $response->error->message, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($response->error->message);
        }

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $error_msg, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($error_msg);
        }

        return $response;
    }
}
